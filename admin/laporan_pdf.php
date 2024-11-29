<?php
// laporan_pdf.php
// Enhanced PDF Report Generator for School Payment System

require_once('../tcpdf/tcpdf.php');
include '../koneksi.php';

session_start();

// Authentication Check
if($_SESSION['status'] != 'login'){
    session_unset();
    session_destroy();
    header("location:../");
    exit();
}

/**
 * Calculate Late Payment Penalty
 * 
 * @param string $tanggal_jatuh_tempo Due date
 * @param float $biaya_spp Monthly tuition fee
 * @return float Total penalty amount
 */
function hitungDenda($tanggal_jatuh_tempo, $biaya_spp) {
    $denda_per_bulan = 0.05; // 5% penalty per month
    $sekarang = new DateTime();
    $jatuh_tempo = new DateTime($tanggal_jatuh_tempo);
    
    // No penalty if not past due date
    if ($sekarang <= $jatuh_tempo) {
        return 0;
    }
    
    // Calculate months overdue
    $interval = $jatuh_tempo->diff($sekarang);
    $selisih_bulan = ($interval->y * 12) + $interval->m;
    
    // Calculate total penalty
    $total_denda = $biaya_spp * $denda_per_bulan * $selisih_bulan;
    
    return $total_denda;
}

/**
 * Format Indonesian Date
 * 
 * @param string $date Date to format
 * @return string Formatted date in Indonesian
 */
function formatTanggalIndonesia($date) {
    $bulan_indo = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', 
        '04' => 'April', '05' => 'Mei', '06' => 'Juni', 
        '07' => 'Juli', '08' => 'Agustus', '09' => 'September', 
        '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];

    $timestamp = strtotime($date);
    $day = date('d', $timestamp);
    $month = $bulan_indo[date('m', $timestamp)];
    $year = date('Y', $timestamp);

    return "$day $month $year";
}

// Indonesian Month Names
$bulan_indo = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

// Filter Parameters
$filter_kelas = isset($_GET['kelas']) ? mysqli_real_escape_string($koneksi, $_GET['kelas']) : '';
$filter_bulan = isset($_GET['bulan']) ? mysqli_real_escape_string($koneksi, $_GET['bulan']) : '';
$filter_tahun = isset($_GET['tahun']) ? mysqli_real_escape_string($koneksi, $_GET['tahun']) : '';

// Comprehensive Query to Fetch Payment Data
$query = "SELECT 
    pembayaran_221043.*, 
    siswa_221043.nama_221043 AS nama_siswa, 
    spp_221043.biaya_221043 AS biaya_spp,
    kelas_221043.kelas_221043 AS kelas,
    DATE_FORMAT(CONCAT('2024-', SUBSTRING(pembayaran_221043.bulan_221043, 1, 2), '-01'), '%Y-%m-%d') as tanggal_jatuh_tempo
FROM 
    pembayaran_221043 
JOIN 
    siswa_221043 ON pembayaran_221043.siswa_id_221043 = siswa_221043.id_221043 
JOIN 
    kelas_221043 ON siswa_221043.id_kelas_221043 = kelas_221043.id_221043 
JOIN 
    spp_221043 ON siswa_221043.id_kelas_221043 = spp_221043.id_kelas_221043
WHERE pembayaran_221043.status_221043='lunas'";

// Apply Dynamic Filters
if (!empty($filter_kelas)) {
    $query .= " AND kelas_221043.kelas_221043 = '$filter_kelas'";
}

if (!empty($filter_bulan)) {
    $query .= " AND SUBSTRING(pembayaran_221043.bulan_221043, 1, 2) = '$filter_bulan'";
}

if (!empty($filter_tahun)) {
    $query .= " AND SUBSTRING(pembayaran_221043.bulan_221043, 4, 4) = '$filter_tahun'";
}

$tampil = mysqli_query($koneksi, $query);

// Create Customized PDF Document
class CustomPDF extends TCPDF {
    public function Header() {
        // Logo and Header
        $image_file = '../assets/school_logo.png';
        if (file_exists($image_file)) {
            $this->Image($image_file, 15, 10, 25, '', 'PNG', '', 'T', false, 300, '', false, false, 0);
        }
        
        $this->SetFont('helvetica', 'B', 14);
        $this->SetTextColor(52, 152, 219);
        $this->Cell(0, 15, 'Laporan Pembayaran SPP', 0, 1, 'C');
        
        $this->SetFont('helvetica', '', 10);
        $this->SetTextColor(0);
        $this->Cell(0, 5, 'Sistem Informasi Pembayaran Sekolah', 0, 1, 'C');
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Halaman '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C');
    }
}

$pdf = new CustomPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// PDF Document Properties
$filter_text = 'Laporan Pembayaran SPP ' . 
    (!empty($filter_kelas) ? 'Kelas ' . $filter_kelas . ' ' : '') .
    (!empty($filter_bulan) ? 'Bulan ' . $bulan_indo[$filter_bulan] . ' ' : '') .
    (!empty($filter_tahun) ? 'Tahun ' . $filter_tahun : '');

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistem Pembayaran SPP');
$pdf->SetTitle($filter_text);
$pdf->SetSubject($filter_text);
$pdf->SetKeywords('Laporan, Pembayaran, SPP, Keuangan');

// PDF Configuration
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(PDF_MARGIN_LEFT, 30, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

$pdf->AddPage('L', 'A4');

// Check if Data Exists
if(mysqli_num_rows($tampil) == 0) {
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'Tidak ada data pembayaran yang sesuai dengan filter.', 0, 1, 'C');
    $pdf->Output('laporan_pembayaran.pdf', 'I');
    exit();
}

// Generate HTML Table
$html = '<table cellpadding="6" cellspacing="0" style="border-collapse:collapse; font-size:9pt; width:100%;">
    <thead>
        <tr style="background-color:rgb(52,152,219); color:white;">
            <th width="5%" style="text-align:center; border:1px solid #ddd;">No</th>
            <th width="15%" style="text-align:left; border:1px solid #ddd;">Nama</th>
            <th width="10%" style="text-align:center; border:1px solid #ddd;">Kelas</th>
            <th width="12%" style="text-align:right; border:1px solid #ddd;">Biaya SPP</th>
            <th width="12%" style="text-align:center; border:1px solid #ddd;">Bulan</th>
            <th width="12%" style="text-align:center; border:1px solid #ddd;">Jatuh Tempo</th>
            <th width="12%" style="text-align:right; border:1px solid #ddd;">Denda</th>
            <th width="12%" style="text-align:right; border:1px solid #ddd;">Total Bayar</th>
        </tr>
    </thead>
    <tbody>';

$no = 1;
$total_pembayaran = 0;
$total_denda = 0;

while($data = mysqli_fetch_array($tampil)) {
    $denda = hitungDenda($data['tanggal_jatuh_tempo'], $data['biaya_spp']);
    $total_bayar = $data['biaya_spp'] + $denda;

    $date = $data['bulan_221043'];
    list($month, $year) = explode('-', $date);

    $row_style = $no % 2 == 0 ? 'background-color:rgb(240,248,255);' : '';
    
    $html .= '<tr style="' . $row_style . '">';
    $html .= '<td style="text-align:center; border:1px solid #ddd;">' . $no++ . '</td>';
    $html .= '<td style="text-align:left; border:1px solid #ddd;">' . $data['nama_siswa'] . '</td>';
    $html .= '<td style="text-align:center; border:1px solid #ddd;">' . $data['kelas'] . '</td>';
    $html .= '<td style="text-align:right; border:1px solid #ddd;">Rp ' . number_format($data['biaya_spp'], 0, ',', '.') . '</td>';
    $html .= '<td style="text-align:center; border:1px solid #ddd;">' . $data['bulan_221043'] . '</td>';
    $html .= '<td style="text-align:center; border:1px solid #ddd;">' . $bulan_indo[$month] . ' ' . $year . '</td>';
    $html .= '<td style="text-align:right; color:red; border:1px solid #ddd;">' . ($denda > 0 ? 'Rp ' . number_format($denda, 0, ',', '.') : '-') . '</td>';
    $html .= '<td style="text-align:right; border:1px solid #ddd;">Rp ' . number_format($total_bayar, 0, ',', '.') . '</td>';
    $html .= '</tr>';

    $total_pembayaran += $total_bayar;
    $total_denda += $denda;
}

// Total Row
$html .= '<tr style="background-color:rgb(52,152,219); color:white; font-weight:bold;">
    <td colspan="6" style="text-align:right; border:1px solid #ddd;">Total</td>
    <td style="text-align:right; color:white; border:1px solid #ddd;">Rp ' . number_format($total_denda, 0, ',', '.') . '</td>
    <td style="text-align:right; border:1px solid #ddd;">Rp ' . number_format($total_pembayaran, 0, ',', '.') . '</td>
</tr>';

$html .= '</tbody></table>';

// Summary Section
$summary_html = '<div style="margin-top:20px; border-top:1px solid #ccc; padding-top:10px;">
    <h3 style="color:rgb(52,152,219);">Ringkasan Laporan</h3>
    <table width="100%" style="font-size:10pt;">
        <tr>
            <td width="50%">Total Pembayaran:</td>
            <td style="text-align:right;">Rp ' . number_format($total_pembayaran, 0, ',', '.') . '</td>
        </tr>
        <tr>
            <td>Total Denda:</td>
            <td style="text-align:right; color:red;">Rp ' . number_format($total_denda, 0, ',', '.') . '</td>
        </tr>
        <tr>
            <td>Jumlah Siswa:</td>
            <td style="text-align:right;">' . ($no - 1) . '</td>
        </tr>
    </table>
</div>';

// Write HTML Content
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->writeHTML($summary_html, true, false, true, false, '');

// Generate and Output PDF
$pdf->Output('laporan_pembayaran.pdf', 'I');
?>