<?php

include '../koneksi.php';

session_start();

if ($_SESSION['status'] != 'login') {
    session_unset();
    session_destroy();

    header('location:../');
}

function hitungDenda($tanggal_jatuh_tempo, $biaya_spp)
{
    $denda_per_bulan = 0.05; // 5% dari biaya SPP per bulan
    $sekarang = new DateTime();
    $jatuh_tempo = new DateTime($tanggal_jatuh_tempo);

    // Jika belum melewati tanggal jatuh tempo, tidak ada denda
    if ($sekarang <= $jatuh_tempo) {
        return 0;
    }

    // Hitung selisih bulan
    $interval = $jatuh_tempo->diff($sekarang);
    $selisih_bulan = $interval->y * 12 + $interval->m;

    // Hitung total denda
    $total_denda = $biaya_spp * $denda_per_bulan * $selisih_bulan;

    return $total_denda;
}

$tahun = date('Y');

$tampil = mysqli_query(
    $koneksi,
    "SELECT 
        siswa_221043.id_221043 AS id_siswa,
        pembayaran_221043.id_221043 AS id,
        pembayaran_221043.bukti_pembayaran_221043 AS bukti_pembayaran,
        siswa_221043.nama_221043 AS nama_siswa, 
        kelas_221043.kelas_221043 AS kelas,
        GROUP_CONCAT(DISTINCT DATE_FORMAT(CONCAT('$tahun-', SUBSTRING(pembayaran_221043.bulan_221043, 1, 2), '-01'), '%M %Y') ORDER BY pembayaran_221043.bulan_221043 ASC) AS bulan_pembayaran,
        SUM(spp_221043.biaya_221043) AS total_biaya_spp,
        SUM(
            CASE 
                WHEN DATE(CONCAT('$tahun-', SUBSTRING(pembayaran_221043.bulan_221043, 1, 2), '-01')) < NOW()
                THEN spp_221043.biaya_221043 * 0.05 * TIMESTAMPDIFF(MONTH, DATE(CONCAT('$tahun-', SUBSTRING(pembayaran_221043.bulan_221043, 1, 2), '-01')), NOW())
                ELSE 0
            END
        ) AS total_denda,
        GROUP_CONCAT(
            DISTINCT CASE 
                WHEN DATE(CONCAT('$tahun-', SUBSTRING(pembayaran_221043.bulan_221043, 1, 2), '-01')) < DATE_FORMAT(NOW(), '%Y-%m-01')
                THEN DATE_FORMAT(CONCAT('$tahun-', SUBSTRING(pembayaran_221043.bulan_221043, 1, 2), '-01'), '%M %Y')
                ELSE NULL
            END
            ORDER BY pembayaran_221043.bulan_221043 ASC
        ) AS bulan_denda,
        SUM(spp_221043.biaya_221043) + SUM(
            CASE 
                WHEN DATE(CONCAT('$tahun-', SUBSTRING(pembayaran_221043.bulan_221043, 1, 2), '-01')) < NOW()
                THEN spp_221043.biaya_221043 * 0.05 * TIMESTAMPDIFF(MONTH, DATE(CONCAT('$tahun-', SUBSTRING(pembayaran_221043.bulan_221043, 1, 2), '-01')), NOW())
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
    GROUP BY 
        siswa_221043.id_221043
",
);

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
                        <h1 class="h3 mb-0 text-gray-800">Data Pembayaran</h1>
                    </div>

                    <!-- Row -->
                    <div class="row">
                        <!-- Datatables -->
                        <div class="col-lg-12">
                            <div class="card mb-4">
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <a class="btn btn-success" href="tambahpembayaran.php">Tambah Data</a>
                                </div>
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
                                                <th>Aksi</th>
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
                                                <th>Aksi</th>
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
                                                <td>Rp <?= number_format($data['total_biaya_spp'], 0, ',', '.') ?></td>
                                                <td><?= formatBulan($data['bulan_pembayaran']) ?></td>
                                                <td><?= isset($data['bulan_denda']) && $data['bulan_denda'] ? formatBulan($data['bulan_denda']) : '-' ?>
                                                </td>
                                                <td>
                                                    <?php if ($data['status_pembayaran'] == 'pending'): ?>
                                                    Rp <?= number_format($data['total_denda'], 0, ',', '.') ?>
                                                    <?php else: ?>
                                                    -
                                                    <?php endif; ?>
                                                </td>
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
                                                <td>
                                                    <a class="btn btn-success viewBukti" href="#"
                                                        data-toggle="modal" data-target="#buktiModal"
                                                        data-id="<?= $data['id_siswa'] ?>"
                                                        data-bukti="<?= $data['bukti_pembayaran'] ?>"
                                                        data-status="<?= $data['status_pembayaran'] ?>">
                                                        Lihat Bukti Pembayaran
                                                    </a>
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
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <img id="buktiImage" src="" alt="Bukti Pembayaran" class="img-fluid">
                                    <div id="statusSection" class="mt-3">
                                        <h6>Status Pembayaran</h6>
                                        <form action="update_status.php" method="POST">
                                            <input type="hidden" id="pembayaranId" name="pembayaran_id">
                                            <select class="form-control" id="statusPembayaran" name="status">
                                                <option value="pending">Pending</option>
                                                <option value="lunas">Lunas</option>
                                            </select>
                                            <button type="submit" class="btn btn-primary mt-3">Update Status</button>
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
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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

    <script>
        $(document).ready(function() {
            // Handle modal open
            $('.viewBukti').on('click', function() {
                const id = $(this).data('id');
                const bukti = $(this).data('bukti');
                const status = $(this).data('status');

                $('#pembayaranId').val(id);
                $('#buktiImage').attr('src', '../orangtua/' + bukti);
                $('#statusPembayaran').val(status);
            });


        });
    </script>

</body>

</html>
