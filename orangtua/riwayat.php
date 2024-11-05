<?php

include '../koneksi.php';

session_start();

$orangtua_id = $_SESSION['id_orangtua'];


if($_SESSION['status'] != 'login'){

    session_unset();
    session_destroy();

    header("location:../");

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
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseForm" aria-expanded="true"
          aria-controls="collapseForm">
          <i class="fab fa-fw fa-wpforms"></i>
          <span>Status Pembayaran Spp</span>
        </a>
        <div id="collapseForm" class="collapse" aria-labelledby="headingForm" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
          <a class="collapse-item" href="spp.php">Lihat Status Pembayaran</a>
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
                <img class="img-profile rounded-circle" src="img/boy.png" style="max-width: 60px">
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
            <h1 class="h3 mb-0 text-gray-800">Data Riwayat</h1>
          </div>

          <!-- Row -->
          <div class="row">
  <!-- Filter Card -->
  <div class="col-lg-12">
    <div class="card mb-4">
      <div class="card-body">
        <form method="GET" class="row">
          <div class="col-lg-3">
            <div class="form-group">
              <label>Tanggal Awal</label>
              <input type="date" class="form-control" name="start_date" value="<?= isset($_GET['start_date']) ? $_GET['start_date'] : '' ?>">
            </div>
          </div>
          <div class="col-lg-3">
            <div class="form-group">
              <label>Tanggal Akhir</label>
              <input type="date" class="form-control" name="end_date" value="<?= isset($_GET['end_date']) ? $_GET['end_date'] : '' ?>">
            </div>
          </div>
          <div class="col-lg-4">
            <div class="form-group">
              <label>&nbsp;</label>
              <div>
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-search"></i> Filter
                </button>
                <a href="?" class="btn btn-danger">
                  <i class="fas fa-undo"></i> Reset
                </a>
                <button type="button" id="exportExcel" class="btn btn-success">
                  <i class="fas fa-file-excel"></i> Export Excel
                </button>
              </div>
            </div>
          </div>
        </form>
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
              <th>Biaya Spp</th>
              <th>Bulan</th>
              <th>Status</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>Kelas</th>
              <th>Biaya Spp</th>
              <th>Bulan</th>
              <th>Status</th>
            </tr>
          </tfoot>
          <tbody>
          <?php
          $no = 1;
          $where_clause = "WHERE pembayaran_221043.status_221043 = 'lunas' 
                          AND siswa_221043.orangtua_id_221043 = '$orangtua_id'";

          if(isset($_GET['start_date']) && isset($_GET['end_date'])) {
              $start_date = mysqli_real_escape_string($koneksi, $_GET['start_date']);
              $end_date = mysqli_real_escape_string($koneksi, $_GET['end_date']);
              if(!empty($start_date) && !empty($end_date)) {
                  $where_clause .= " AND DATE(pembayaran_221043.tanggal_bayar_221043) 
                                   BETWEEN '$start_date' AND '$end_date'";
              }
          }

          $tampil = mysqli_query($koneksi, "SELECT 
                                              pembayaran_221043.*, 
                                              siswa_221043.nama_221043 AS nama_siswa, 
                                              spp_221043.biaya_221043 AS biaya_spp,
                                              kelas_221043.kelas_221043 AS kelas
                                          FROM 
                                              pembayaran_221043 
                                          JOIN 
                                              siswa_221043 ON pembayaran_221043.siswa_id_221043 = siswa_221043.id_221043 
                                          JOIN 
                                              kelas_221043 ON siswa_221043.id_kelas_221043 = kelas_221043.id_221043 
                                          JOIN 
                                              spp_221043 ON siswa_221043.id_kelas_221043 = spp_221043.id_kelas_221043
                                          $where_clause
                                          ORDER BY pembayaran_221043.tanggal_bayar_221043 DESC");
          
          while($data = mysqli_fetch_array($tampil)):
          ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= htmlspecialchars($data['nama_siswa']) ?></td>
              <td><?= htmlspecialchars($data['kelas']) ?></td>
              <td>Rp <?= number_format($data['biaya_spp'], 0, ',', '.') ?></td>
              <td><?= htmlspecialchars($data['bulan_221043']) ?></td>
              <td>
                <?php if ($data['status_221043'] == 'pending'): ?>
                  <span class="badge badge-warning"><?= $data['status_221043'] ?></span>
                <?php else: ?>
                  <span class="badge badge-success"><?= $data['status_221043'] ?></span>
                <?php endif; ?>
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

  <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#dataTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });

    // Date validation
    $('input[name="end_date"]').change(function() {
        let startDate = $('input[name="start_date"]').val();
        let endDate = $(this).val();
        
        if(startDate && endDate && startDate > endDate) {
            alert('Tanggal akhir harus lebih besar dari tanggal awal');
            $(this).val('');
        }
    });

    $('input[name="start_date"]').change(function() {
        let startDate = $(this).val();
        let endDate = $('input[name="end_date"]').val();
        
        if(startDate && endDate && startDate > endDate) {
            alert('Tanggal awal harus lebih kecil dari tanggal akhir');
            $(this).val('');
        }
    });

    // Excel Export
    $('#exportExcel').click(function() {
        let table = document.getElementById('dataTable');
        
        // Create a workbook
        let wb = XLSX.utils.book_new();
        
        // Convert table to worksheet
        let ws = XLSX.utils.table_to_sheet(table);
        
        // Add worksheet to workbook
        XLSX.utils.book_append_sheet(wb, ws, 'Laporan Pembayaran SPP');
        
        // Generate date string for filename
        let date = new Date();
        let dateStr = date.getFullYear() + '-' + 
                     String(date.getMonth() + 1).padStart(2, '0') + '-' + 
                     String(date.getDate()).padStart(2, '0');
        
        // Save the file
        XLSX.writeFile(wb, `Laporan_Pembayaran_SPP_${dateStr}.xlsx`);
    });
});
</script>

  <!-- Page level custom scripts -->
  <script>
    $(document).ready(function () {
      $('#dataTable').DataTable(); // ID From dataTable 
      $('#dataTableHover').DataTable(); // ID From dataTable with Hover
    });
  </script>


</body>

</html>