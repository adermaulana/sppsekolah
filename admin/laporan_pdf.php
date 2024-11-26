<?php
// laporan_pdf.php
require_once('../tcpdf/tcpdf.php');
include '../koneksi.php';

session_start();

if($_SESSION['status'] != 'login'){
    session_unset();
    session_destroy();
    header("location:../");
    exit();
}

function hitungDenda($tanggal_jatuh_tempo, $biaya_spp) {
    $denda_per_bulan = 0.05; // 5% dari biaya SPP per bulan
    $sekarang = new DateTime();
    $jatuh_tempo = new DateTime($tanggal_jatuh_tempo);
    
    // Jika belum melewati tanggal jatuh tempo, tidak ada denda
    if ($sekarang <= $jatuh_tempo) {
        return 0;
    }
    
    // Hitung selisih bulan
    $interval = $jatuh_tempo->diff($sekarang);
    $selisih_bulan = ($interval->y * 12) + $interval->m;
    
    // Hitung total denda
    $total_denda = $biaya_spp * $denda_per_bulan * $selisih_bulan;
    
    return $total_denda;
}

// Array nama bulan dalam Bahasa Indonesia
$bulan_indo = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

// Filter parameters
$filter_kelas = isset($_GET['kelas']) ? mysqli_real_escape_string($koneksi, $_GET['kelas']) : '';
$filter_bulan = isset($_GET['bulan']) ? mysqli_real_escape_string($koneksi, $_GET['bulan']) : '';
$filter_tahun = isset($_GET['tahun']) ? mysqli_real_escape_string($koneksi, $_GET['tahun']) : '';

// Base query
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

// Apply filters
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

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistem Pembayaran SPP');
$pdf->SetTitle('Laporan Pembayaran SPP');
$pdf->SetSubject('Laporan Pembayaran');
$pdf->SetKeywords('TCPDF, PDF, laporan, pembayaran, spp');

// Set default header data
$pdf->SetHeaderData('', 0, 'Laporan Pembayaran SPP', 'Periode: ' . 
    (!empty($filter_bulan) ? $bulan_indo[$filter_bulan] : 'Semua Bulan') . ' ' . 
    (!empty($filter_tahun) ? $filter_tahun : 'Semua Tahun'));

// Set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Add a page
$pdf->AddPage('L', 'A4');

// Create table header
$html = '<table border="1" cellpadding="5">
    <thead>
        <tr style="background-color:#f0f0f0;">
            <th width="30">No</th>
            <th width="100">Nama</th>
            <th width="60">Kelas</th>
            <th width="80">Biaya SPP</th>
            <th width="80">Bulan</th>
            <th width="80">Jatuh Tempo</th>
            <th width="80">Denda</th>
            <th width="80">Total Bayar</th>
            <th width="80">Status</th>
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

    $html .= '<tr>
        <td width="30">' . $no++ . '</td>
        <td width="100">' . $data['nama_siswa'] . '</td>
        <td width="60">' . $data['kelas'] . '</td>
        <td width="80">Rp ' . number_format($data['biaya_spp'], 0, ',', '.') . '</td>
        <td width="80">' . $data['bulan_221043'] . '</td>
        <td width="80">' . $bulan_indo[$month] . ' ' . $year . '</td>
        <td width="80" style="color:red;">' . ($denda > 0 ? 'Rp ' . number_format($denda, 0, ',', '.') : '-') . '</td>
        <td width="80">Rp ' . number_format($total_bayar, 0, ',', '.') . '</td>
        <td width="80">' . strtoupper($data['status_221043']) . '</td>
    </tr>';

    $total_pembayaran += $total_bayar;
    $total_denda += $denda;
}

$html .= '<tr style="background-color:#f0f0f0;">
    <td colspan="6" align="right"><strong>Total</strong></td>
    <td style="color:red;">Rp ' . number_format($total_denda, 0, ',', '.') . '</td>
    <td>Rp ' . number_format($total_pembayaran, 0, ',', '.') . '</td>
    <td></td>
</tr>';

$html .= '</tbody></table>';

// Print text using writeHTMLCell()
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('laporan_pembayaran.pdf', 'I');
?>