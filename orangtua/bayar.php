<?php
include '../koneksi.php';

session_start();

$orangtua_id = $_SESSION['id_orangtua'];

if($_SESSION['status'] != 'login'){
    session_unset();
    session_destroy();
    header("location:../");
}

function hitungDenda($tanggal_jatuh_tempo, $biaya_spp) {
  $denda_per_bulan = 0.05; // 5% dari biaya SPP per bulan
  $sekarang = new DateTime();
  $jatuh_tempo = new DateTime($tanggal_jatuh_tempo);
  
  if ($sekarang <= $jatuh_tempo) {
      return 0;
  }
  
  $interval = $jatuh_tempo->diff($sekarang);
  $selisih_bulan = ($interval->y * 12) + $interval->m;
  
  $total_denda = $biaya_spp * $denda_per_bulan * $selisih_bulan;
  
  return $total_denda;
}

// Query untuk mendapatkan range bulan dan total denda
$query_range = "SELECT 
    MIN(bulan_221043) as min_bulan,
    MAX(bulan_221043) as max_bulan,
    SUM(CASE 
        WHEN status_221043 = 'pending' THEN 
            spp_221043.biaya_221043 * 0.05 * 
            PERIOD_DIFF(
                DATE_FORMAT(NOW(), '%Y%m'),
                CONCAT(SUBSTRING_INDEX(bulan_221043, '-', -1), SUBSTRING_INDEX(bulan_221043, '-', 1))
            )
        ELSE 0 
    END) as total_denda,
    COUNT(CASE WHEN status_221043 = 'pending' THEN 1 END) as jumlah_tunggakan
FROM 
    pembayaran_221043 
JOIN 
    siswa_221043 ON pembayaran_221043.siswa_id_221043 = siswa_221043.id_221043 
JOIN 
    spp_221043 ON siswa_221043.id_kelas_221043 = spp_221043.id_kelas_221043
WHERE 
    siswa_221043.orangtua_id_221043 = '$orangtua_id'";

$result_range = mysqli_query($koneksi, $query_range);
$denda_info = mysqli_fetch_assoc($result_range);

// Query untuk data pembayaran
$tampil = mysqli_query($koneksi, "SELECT 
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
WHERE 
    siswa_221043.orangtua_id_221043 = '$orangtua_id'");

// Fungsi untuk format bulan
function formatBulan($bulan) {
    list($month, $year) = explode('-', $bulan);
    $bulan_indo = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember'
    ];
    return $bulan_indo[$month] . ' ' . $year;
}

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
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
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
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#bayar" aria-expanded="true"
          aria-controls="collapseForm">
          <i class="fab fa-fw fa-wpforms"></i>
          <span>Bayar Spp</span>
        </a>
        <div id="bayar" class="collapse" aria-labelledby="headingForm" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
          <a class="collapse-item" href="bayar.php">Bayar Spp</a>
          </div>
        </div>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTable" aria-expanded="true"
          aria-controls="collapseTable">
          <i class="fas fa-fw fa-table"></i>
          <span>Riwayat Pembayaran Spp</span>
        </a>
        <div id="collapseTable" class="collapse" aria-labelledby="headingTable" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
          <a class="collapse-item" href="riwayat.php">Lihat Riwayat Pembayaran</a>
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
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <img class="img-profile rounded-circle" src="../assets/img/boy.png" style="max-width: 60px">
                <span class="ml-2 d-none d-lg-inline text-white small"><?= $_SESSION['nama_orangtua'] ?></span>
              </a>
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="javascript:void(0);" data-toggle="modal" data-target="#logoutModal">
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
            <h1 class="h3 mb-0 text-gray-800">Bayar Spp</h1>
          </div>

          <!-- Row -->
          <div class="row">
            <!-- Card Ringkasan Denda -->
            <div class="col-lg-12">
                <div class="card mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Ringkasan Total Denda</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">Periode Pembayaran:</label>
                                    <p><?= formatBulan($denda_info['min_bulan']) ?> - <?= formatBulan($denda_info['max_bulan']) ?></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">Jumlah Tunggakan:</label>
                                    <p><?= $denda_info['jumlah_tunggakan'] ?> bulan</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">Total Denda:</label>
                                    <p class="text-danger">Rp <?= number_format($denda_info['total_denda'], 0, ',', '.') ?></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">Status:</label>
                                    <?php if ($denda_info['total_denda'] > 0): ?>
                                        <p class="text-danger">
                                            <i class="fas fa-exclamation-circle"></i>
                                            Terdapat Tunggakan
                                        </p>
                                    <?php else: ?>
                                        <p class="text-success">
                                            <i class="fas fa-check-circle"></i>
                                            Tidak Ada Tunggakan
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
                            <th>Jatuh Tempo</th>
                            <th>Denda</th>
                            <th>Total Bayar</th>
                            <th>Bukti Pembayaran</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while($data = mysqli_fetch_array($tampil)):
                            $denda = hitungDenda($data['tanggal_jatuh_tempo'], $data['biaya_spp']);
                            $total_bayar = $data['biaya_spp'] + $denda;

                            $date = $data['bulan_221043'];
                            list($month, $year) = explode('-', $date);
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $data['nama_siswa'] ?></td>
                            <td><?= $data['kelas'] ?></td>
                            <td>Rp <?= number_format($data['biaya_spp'], 0, ',', '.') ?></td>
                            <td><?= formatBulan($data['bulan_221043']) ?></td>
                            <td><?= formatBulan($data['bulan_221043']) ?></td>
                            <td><?php 
                                if ($denda > 0) {
                                    echo '<span class="text-danger">Rp ' . number_format($denda, 0, ',', '.') . ' (5%)</span>';
                                } else {
                                    echo '-';
                                }
                            ?></td>
                            <td>Rp <?= number_format($total_bayar, 0, ',', '.') ?></td>
                            <td>
                                <?php if (!empty($data['bukti_pembayaran_221043'])): ?>
                                    <img src="<?= $data['bukti_pembayaran_221043'] ?>" alt="Bukti Pembayaran" style="max-width: 100px; max-height: 100px;">
                                <?php else: ?>
                                    <span>Tidak ada bukti pembayaran</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($data['status_221043'] == 'pending'): ?>
                                    <span class="badge badge-warning"><?= $data['status_221043'] ?></span>
                                <?php else: ?>
                                    <span class="badge badge-success"><?= $data['status_221043'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-primary" data-toggle="modal" data-target="#uploadModal<?= $data['id_221043'] ?>">
                                    Upload Foto
                                </button>
                                
                                <!-- Modal Upload Foto -->
                                <div class="modal fade" id="uploadModal<?= $data['id_221043'] ?>" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Upload Bukti Pembayaran</h5>
                                                <button type="button" class="close" data-dismiss="modal">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="upload_foto.php" method="post" enctype="multipart/form-data">
                                                <div class="modal-body">
                                                    <input type="hidden" name="id_221043" value="<?= $data['id_221043'] ?>">
                                                    <div class="form-group">
                                                        <label>Total yang harus dibayar: Rp <?= number_format($total_bayar, 0, ',', '.') ?></label>
                                                        <?php if ($denda > 0): ?>
                                                            <div class="alert alert-warning">
                                                                Terdapat denda keterlambatan sebesar Rp <?= number_format($denda, 0, ',', '.') ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="bukti_pembayaran_221043">Upload Bukti Pembayaran:</label>
                                                        <input type="file" name="bukti_pembayaran_221043" class="form-control" accept="image/*" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Upload</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                </div>
              </div>
            </div>

          </div>
          <!--Row-->

          <!-- Modal Logout -->
          <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelLogout"
            aria-hidden="true">
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
                  <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Cancel</button>
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
            <span>copyright &copy; <script> document.write(new Date().getFullYear()); </script> - developed by
              <b><a href="https://indrijunanda.gitlab.io/" target="_blank">indrijunanda</a></b>
            </span>
          </div>
        </div>

        <div class="container my-auto py-2">
          <div class="copyright text-center my-auto">
            <span>copyright &copy; <script> document.write(new Date().getFullYear()); </script> - distributed by
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
    $(document).ready(function () {
      $('#dataTable').DataTable(); // ID From dataTable 
      $('#dataTableHover').DataTable(); // ID From dataTable with Hover
    });
  </script>

</body>

</html>