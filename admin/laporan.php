<?php
include '../koneksi.php';
include '../tcpdf/tcpdf.php';
session_start();

// Authentication Check
if ($_SESSION['status'] != 'login') {
    session_unset();
    session_destroy();
    header('location:../');
    exit();
}

/**
 * Calculate Late Payment Fine
 */
function hitungDenda($tanggal_jatuh_tempo, $biaya_spp)
{
    $denda_per_bulan = 0.05; // 5% from monthly tuition per month
    $sekarang = new DateTime();
    $jatuh_tempo = new DateTime($tanggal_jatuh_tempo);

    // No fine if not past due date
    if ($sekarang <= $jatuh_tempo) {
        return 0;
    }

    // Calculate month difference
    $interval = $jatuh_tempo->diff($sekarang);
    $selisih_bulan = $interval->y * 12 + $interval->m;

    // Calculate total fine
    $total_denda = $biaya_spp * $denda_per_bulan * $selisih_bulan;

    return $total_denda;
}

/**
 * Custom TCPDF class to override Header and Footer
 */
class CustomPDF extends TCPDF
{
    private $start_date;
    private $end_date;

    public function __construct($start_date, $end_date)
    {
        parent::__construct('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function Header()
    {
        // Title
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 15, 'Laporan Pembayarn Spp', 0, 1, 'C');

        // Reporting Period
        $this->SetFont('helvetica', '', 10);
        $this->Cell(0, 15, "Periode: {$this->start_date} sampai {$this->end_date}", 0, 1, 'C');
    }

    public function Footer()
    {
        // Position from bottom
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);

        // Page number
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

/**
 * Generate Enhanced PDF Report
 */
function generateEnhancedPDF($data, $start_date, $end_date)
{
    // Create PDF with custom header/footer
    $pdf = new CustomPDF($start_date, $end_date);

    // Document Properties
    $pdf->SetCreator('Laporan Pembayaran Spp');
    $pdf->SetAuthor('Laporan Pembayaran Spp');
    $pdf->SetTitle('Laporan Pembayaran Spp');
    $pdf->SetSubject('Laporan Pembayaran Spp');

    // Page Setup
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(10, 40, 10);
    $pdf->SetHeaderMargin(10);
    $pdf->SetFooterMargin(10);
    $pdf->SetAutoPageBreak(true, 15);

    // Add First Page
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 9);

    // HTML Table Generation (Same as previous version)
    $html = '
    <style>
        table { 
            border-collapse: collapse; 
            width: 100%; 
        }
        th { 
            background-color: #f2f2f2; 
            font-weight: bold; 
            text-align: center; 
            border: 1px solid #ddd; 
            padding: 8px; 
        }
        td { 
            border: 1px solid #ddd; 
            padding: 10px; 
            height: 25px;
            text-align: center; 
        }
        tr:nth-child(even) { 
            background-color: #f9f9f9; 
        }
    </style>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Nama Siswa</th>
                <th width="10%">Kelas</th>
                <th width="12%">Biaya Spp</th>
                <th width="15%">Bulan</th>
                <th width="15%">Bulan Denda</th>
                <th width="10%">Jumlah Denda</th>
                <th width="10%">Total Pembayaran</th>
                <th width="8%">Status</th>
            </tr>
        </thead>
        <tbody>';

    $no = 1;
    $total_spp = 0;
    $total_fine = 0;
    $total_payment = 0;

    while ($row = mysqli_fetch_assoc($data)) {
        $html .= '<tr>';
        $html .= '<td width="5%">' . htmlspecialchars($no++) . '</td>';
        $html .= '<td width="15%">' . htmlspecialchars($row['nama_siswa']) . '</td>';
        $html .= '<td width="10%">' . htmlspecialchars($row['kelas']) . '</td>';
        $html .= '<td width="12%">Rp ' . number_format($row['total_biaya_spp'], 0, ',', '.') . '</td>';
        $html .= '<td width="15%">' . htmlspecialchars(formatBulan($row['bulan_pembayaran'])) . '</td>';
        $html .= '<td width="15%">' . htmlspecialchars(formatBulan($row['bulan_denda'] ?? '-')) . '</td>';
        $html .= '<td width="10%">Rp ' . number_format($row['total_denda'], 0, ',', '.') . '</td>';
        $html .= '<td width="10%">Rp ' . number_format($row['total_bayar'], 0, ',', '.') . '</td>';
        $html .= '<td width="8%">' . htmlspecialchars($row['status_pembayaran']) . '</td>';
        $html .= '</tr>';

        // Accumulate totals
        $total_spp += $row['total_biaya_spp'];
        $total_fine += $row['total_denda'];
        $total_payment += $row['total_bayar'];
    }

    // Add Summary Row
    $html .=
        '
        <tr style="font-weight: bold; background-color: #e6e6e6;">
            <td colspan="3">TOTAL</td>
            <td>Rp ' .
        number_format($total_spp, 0, ',', '.') .
        '</td>
            <td colspan="2"></td>
            <td>Rp ' .
        number_format($total_fine, 0, ',', '.') .
        '</td>
            <td>Rp ' .
        number_format($total_payment, 0, ',', '.') .
        '</td>
            <td></td>
        </tr>
    </tbody></table>';

    // Print HTML Table
    $pdf->writeHTML($html, true, false, true, false, '');

    // Output PDF
    $pdf->Output('comprehensive_spp_report.pdf', 'I');
}

// Default Date Range - Current Month
$start_date = date('Y-01-01');
$end_date = date('Y-m-t');

// Date Filter Handling
if (isset($_POST['filter_tanggal'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
}

// Comprehensive Payment Query
$query = "SELECT 
    siswa_221043.id_221043 AS id_siswa,
    pembayaran_221043.id_221043 AS id,
    pembayaran_221043.bukti_pembayaran_221043 AS bukti_pembayaran,
    siswa_221043.nama_221043 AS nama_siswa, 
    kelas_221043.kelas_221043 AS kelas,
    GROUP_CONCAT(DISTINCT DATE_FORMAT(CONCAT('2024-', SUBSTRING(pembayaran_221043.bulan_221043, 1, 2), '-01'), '%M %Y') ORDER BY pembayaran_221043.bulan_221043 ASC) AS bulan_pembayaran,
    SUM(spp_221043.biaya_221043) AS total_biaya_spp,
    SUM(
        CASE 
            WHEN DATE(CONCAT('2024-', SUBSTRING(pembayaran_221043.bulan_221043, 1, 2), '-01')) < NOW()
            THEN spp_221043.biaya_221043 * 0.05 * TIMESTAMPDIFF(MONTH, DATE(CONCAT('2024-', SUBSTRING(pembayaran_221043.bulan_221043, 1, 2), '-01')), NOW())
            ELSE 0
        END
    ) AS total_denda,
    GROUP_CONCAT(
        DISTINCT CASE 
            WHEN DATE(CONCAT('2024-', SUBSTRING(pembayaran_221043.bulan_221043, 1, 2), '-01')) < DATE_FORMAT(NOW(), '%Y-%m-01')
            THEN DATE_FORMAT(CONCAT('2024-', SUBSTRING(pembayaran_221043.bulan_221043, 1, 2), '-01'), '%M %Y')
            ELSE NULL
        END
        ORDER BY pembayaran_221043.bulan_221043 ASC
    ) AS bulan_denda,
    SUM(spp_221043.biaya_221043) + SUM(
        CASE 
            WHEN DATE(CONCAT('2024-', SUBSTRING(pembayaran_221043.bulan_221043, 1, 2), '-01')) < NOW()
            THEN spp_221043.biaya_221043 * 0.05 * TIMESTAMPDIFF(MONTH, DATE(CONCAT('2024-', SUBSTRING(pembayaran_221043.bulan_221043, 1, 2), '-01')), NOW())
            ELSE 0
        END
    ) AS total_bayar,
    CASE 
        WHEN COUNT(CASE WHEN pembayaran_221043.status_221043 = 'pending' THEN 1 END) > 0 
        THEN 'pending'
        ELSE 'lunas'
    END AS status_pembayaran
FROM 
    pembayaran_221043 
JOIN 
    siswa_221043 ON pembayaran_221043.siswa_id_221043 = siswa_221043.id_221043 
JOIN 
    kelas_221043 ON siswa_221043.id_kelas_221043 = kelas_221043.id_221043 
JOIN 
    spp_221043 ON siswa_221043.id_kelas_221043 = spp_221043.id_kelas_221043
WHERE 
    pembayaran_221043.tanggal_bayar_221043 BETWEEN ? AND ?
GROUP BY 
    siswa_221043.id_221043";

// PDF Export Handling
if (isset($_POST['cetak_pdf'])) {
    // Prepare statement
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    generateEnhancedPDF($result, $start_date, $end_date);
    exit();
}

// Prepare statement for displaying data
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
mysqli_stmt_execute($stmt);
$tampil = mysqli_stmt_get_result($stmt);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="img/logo/logo.png" rel="icon">
    <title>RuangAdmin - Dashboard</title>
    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="../assets/css/ruang-admin.min.css" rel="stylesheet">
    <link href="../assets/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon">
                    <img src="img/logo/logo2.png">
                </div>
                <div class="sidebar-brand-text mx-3">RuangAdmin</div>
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item active">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>
            <hr class="sidebar-divider">
            <div class="sidebar-heading">
                Features
            </div>
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBootstrap"
                    aria-expanded="true" aria-controls="collapseBootstrap">
                    <i class="far fa-fw fa-window-maximize"></i>
                    <span>Data Siswa</span>
                </a>
                <div id="collapseBootstrap" class="collapse" aria-labelledby="headingBootstrap"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="siswa.php">Lihat Data Siswa</a>
                        <a class="collapse-item" href="tambahsiswa.php">Tambah Siswa</a>
                    </div>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseForm"
                    aria-expanded="true" aria-controls="collapseForm">
                    <i class="fab fa-fw fa-wpforms"></i>
                    <span>Data Spp</span>
                </a>
                <div id="collapseForm" class="collapse" aria-labelledby="headingForm" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="spp.php">Data Spp</a>
                        <a class="collapse-item" href="tambahspp.php">Tambah Spp</a>
                    </div>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTable"
                    aria-expanded="true" aria-controls="collapseTable">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Data Pembayaran</span>
                </a>
                <div id="collapseTable" class="collapse" aria-labelledby="headingTable" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="pembayaran.php">Data Pembayaran</a>
                    </div>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#kelas"
                    aria-expanded="true" aria-controls="kelas">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Data Kelas</span>
                </a>
                <div id="kelas" class="collapse" aria-labelledby="headingTable" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="kelas.php">Data Kelas</a>
                        <a class="collapse-item" href="tambahkelas.php">Tambah Kelas</a>
                    </div>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#laporan"
                    aria-expanded="true" aria-controls="kelas">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Laporan</span>
                </a>
                <div id="laporan" class="collapse" aria-labelledby="headingTable" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="laporan.php">Lihat Laporan</a>
                    </div>
                </div>
            </li>
        </ul>
        <!-- Sidebar -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- TopBar -->
                <nav class="navbar navbar-expand navbar-light bg-navbar topbar mb-4 static-top">
                    <button id="sidebarToggleTop" class="btn btn-link rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img class="img-profile rounded-circle" src="img/boy.png" style="max-width: 60px">
                                <span class="ml-2 d-none d-lg-inline text-white small">Maman Ketoprak</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="javascript:void(0);" data-toggle="modal"
                                    data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- Topbar -->

                <!-- Container Fluid-->
                <div class="container-fluid" id="container-wrapper">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Data Laporan</h1>
                    </div>
                    <div class="container-fluid" id="container-wrapper">
                        <div class="card mb-4">
                            <div class="card-header">
                                Filter Laporan
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Tanggal Mulai</label>
                                            <input type="date" name="start_date" class="form-control"
                                                value="<?= $start_date ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Tanggal Selesai</label>
                                            <input type="date" name="end_date" class="form-control"
                                                value="<?= $end_date ?>">
                                        </div>
                                        <div class="col-md-4 align-self-end">
                                            <button type="submit" name="filter_tanggal"
                                                class="btn btn-primary mr-2">Filter</button>
                                            <button type="submit" name="cetak_pdf" class="btn btn-success">Cetak
                                                PDF</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- Row -->
                        <div class="row">
                            <!-- Datatables -->
                            <div class="col-lg-12">
                                <div class="card mb-4">
                                    <div class="table-responsive p-3">
                                        <table class="table align-items-center table-flush" id="dataTable">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama</th>
                                                    <th>Kelas</th>
                                                    <th>Biaya SPP</th>
                                                    <th>Bulan</th>
                                                    <th>Bulan Denda</th>
                                                    <th>Denda</th>
                                                    <th>Total Bayar</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama</th>
                                                    <th>Kelas</th>
                                                    <th>Biaya SPP</th>
                                                    <th>Bulan</th>
                                                    <th>Bulan Denda</th>
                                                    <th>Denda</th>
                                                    <th>Total Bayar</th>
                                                    <th>Status</th>
                                                </tr>
                                            </tfoot>
                                            <tbody>
                                                <?php
                      $no = 1;

                      function formatBulan($bulanPenuh) {
                          $bulanArray = explode(',', $bulanPenuh);
                          
                          if (empty($bulanArray)) return '-';
                          
                          // Sort the months to ensure correct order
                          usort($bulanArray, function($a, $b) {
                              $monthOrder = [
                                  'January' => 1, 'February' => 2, 'March' => 3, 'April' => 4, 
                                  'May' => 5, 'June' => 6, 'July' => 7, 'August' => 8, 
                                  'September' => 9, 'October' => 10, 'November' => 11, 'December' => 12
                              ];
                              
                              $partsA = explode(' ', trim($a));
                              $partsB = explode(' ', trim($b));
                              
                              return $monthOrder[$partsA[0]] - $monthOrder[$partsB[0]];
                          });
                          
                          $firstMonth = explode(' ', $bulanArray[0]);
                          $lastMonth = explode(' ', $bulanArray[count($bulanArray) - 1]);
                          
                          // If only one month, return that month
                          if (count($bulanArray) == 1) {
                              return $firstMonth[0] . ' ' . $firstMonth[1];
                          }
                          
                          // Return range of months
                          return $firstMonth[0] . ' - ' . $lastMonth[0] . ' ' . $lastMonth[1];
                      }
                    

                      while ($data = mysqli_fetch_array($tampil)) :
                      ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td><?= $data['nama_siswa'] ?></td>
                                                    <td><?= $data['kelas'] ?></td>
                                                    <td>Rp <?= number_format($data['total_biaya_spp'], 0, ',', '.') ?>
                                                    </td>
                                                    <td><?= formatBulan($data['bulan_pembayaran']) ?></td>
                                                    <td><?= isset($data['bulan_denda']) && $data['bulan_denda'] ? formatBulan($data['bulan_denda']) : '-' ?>
                                                    </td>
                                                    <td>Rp <?= number_format($data['total_denda'], 0, ',', '.') ?></td>
                                                    <td>Rp <?= number_format($data['total_bayar'], 0, ',', '.') ?></td>
                                                    <td>
                                                        <?php if ($data['status_pembayaran'] == 'pending'): ?>
                                                        <span
                                                            class="badge badge-warning"><?= $data['status_pembayaran'] ?></span>
                                                        <?php else: ?>
                                                        <span
                                                            class="badge badge-success"><?= $data['status_pembayaran'] ?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php
                      endwhile; 
                      ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!--Row-->

                        <div class="modal fade" id="buktiModal" tabindex="-1" role="dialog"
                            aria-labelledby="buktiModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="buktiModalLabel">Bukti Pembayaran</h5>
                                        <button type="button" class="close" data-dismiss="modal"
                                            aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <img id="buktiImage" src="" alt="Bukti Pembayaran"
                                            class="img-fluid">
                                        <div id="statusSection" class="mt-3">
                                            <h6>Status Pembayaran</h6>
                                            <form action="update_status.php" method="POST">
                                                <input type="hidden" id="pembayaranId" name="pembayaran_id">
                                                <select class="form-control" id="statusPembayaran" name="status">
                                                    <option value="pending">Pending</option>
                                                    <option value="lunas">Lunas</option>
                                                </select>
                                                <button type="submit" class="btn btn-primary mt-3">Update
                                                    Status</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Logout -->
                        <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabelLogout" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabelLogout">Ohh No!</h5>
                                        <button type="button" class="close" data-dismiss="modal"
                                            aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure you want to logout?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-primary"
                                            data-dismiss="modal">Cancel</button>
                                        <a href="logout.php" class="btn btn-primary">Logout</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!---Container Fluid-->
                </div>
                <!-- Footer -->
                <footer class="sticky-footer bg-white">
                    <div class="container my-auto">
                        <div class="copyright text-center my-auto">
                            <span>copyright &copy;
                                <script>
                                    document.write(new Date().getFullYear());
                                </script> - developed by
                                <b><a href="https://indrijunanda.gitlab.io/" target="_blank">indrijunanda</a></b>
                            </span>
                        </div>
                    </div>

                    <div class="container my-auto py-2">
                        <div class="copyright text-center my-auto">
                            <span>copyright &copy;
                                <script>
                                    document.write(new Date().getFullYear());
                                </script> - distributed by
                                <b><a href="https://themewagon.com/" target="_blank">themewagon</a></b>
                            </span>
                        </div>
                    </div>
                </footer>
                <!-- Footer -->
            </div>
        </div>

        <!-- Scroll to top -->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        <script src="../assets/vendor/jquery/jquery.min.js"></script>
        <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="../assets/vendor/jquery-easing/jquery.easing.min.js"></script>
        <script src="../assets/js/ruang-admin.min.js"></script>
        <!-- Page level plugins -->
        <script src="../assets/vendor/datatables/jquery.dataTables.min.js"></script>
        <script src="../assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>

        <!-- Page level custom scripts -->
        <script>
            $(document).ready(function() {
                $('#dataTable').DataTable(); // ID From dataTable 
                $('#dataTableHover').DataTable(); // ID From dataTable with Hover
            });
        </script>


</body>

</html>
