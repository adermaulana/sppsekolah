<?php
include '../koneksi.php';

session_start();

if ($_SESSION['status'] != 'login') {
    session_unset();
    session_destroy();
    header('location:../');
}

if (isset($_POST['simpan'])) {
    $siswa_id = $_POST['siswa_id_221043'];
    $spp_id = $_POST['spp_id_221043'];
    $status = $_POST['status'];
    $bulan = $_POST['bulan'];

    $simpan = mysqli_query(
        $koneksi,
        "INSERT INTO pembayaran_221043 
        (siswa_id_221043, spp_id_221043, bulan_221043, status_221043) 
        VALUES ('$siswa_id', '$spp_id', '$bulan', '$status')"
    );

    if ($simpan) {
        echo "<script>
                alert('Simpan data sukses!');
                document.location='pembayaran.php';
            </script>";
    } else {
        echo "<script>
                alert('Simpan data Gagal!');
                document.location='pembayaran.php';
            </script>";
    }
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

    <style>
        .error-message {
            color: red;
            font-size: 0.8rem;
            margin-top: 5px;
            display: none;
        }
    </style>

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
                        <h1 class="h3 mb-0 text-gray-800">Tambah Pembayaran</h1>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <!-- Form Basic -->
                            <div class="card mb-4">
                                <div class="card-body">
                                    <form method="POST">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="siswa">Siswa</label>
                                                    <select class="form-control" id="siswa_id_221043"
                                                        name="siswa_id_221043" required>
                                                        <option value="" disabled selected>Pilih</option>
                                                        <?php
                            $tampil = mysqli_query($koneksi, "SELECT 
                                siswa_221043.*, 
                                kelas_221043.kelas_221043 AS nama_kelas,
                                spp_221043.biaya_221043 AS biaya_spp,
                                spp_221043.id_221043 AS id_spp
                            FROM 
                                siswa_221043
                            JOIN 
                                kelas_221043 ON siswa_221043.id_kelas_221043 = kelas_221043.id_221043
                            JOIN 
                                spp_221043 ON kelas_221043.id_221043 = spp_221043.id_kelas_221043");
                            while($data = mysqli_fetch_array($tampil)):
                            ?>
                                                        <option value="<?= $data['id_221043'] ?>"
                                                            data-kelas="<?= $data['nama_kelas'] ?>"
                                                            data-spp="<?= $data['id_spp'] ?>"
                                                            data-biaya="<?= $data['biaya_spp'] ?>">
                                                            <?= $data['nama_221043'] ?>
                                                        </option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                    <div id="siswaError" class="error-message">Siswa harus dipilih
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="biaya">Kelas</label>
                                                    <input type="text" class="form-control" id="kelas"
                                                        name="kelas" readonly>
                                                </div>

                                                <div class="form-group">
                                                    <label for="biaya">Biaya SPP</label>
                                                    <input type="hidden" id="biaya" name="biaya">
                                                    <input type="text" class="form-control" id="biayaDisplay"
                                                        name="biayaDisplay" readonly>
                                                </div>

                                                <div class="form-group">
                                                    <label for="bulan">Bulan</label>
                                                    <select class="form-control" id="bulan" name="bulan"
                                                        required>
                                                        <option value="" selected disabled>Pilih Bulan</option>
                                                        <?php
                                                        $tahun = date('Y');
                                                        for ($i = 1; $i <= 12; $i++) {
                                                            $bulan = str_pad($i, 2, '0', STR_PAD_LEFT);
                                                            $bulan_tahun = $bulan . '-' . $tahun;
                                                            $nama_bulan = date('F', mktime(0, 0, 0, $i, 1));
                                                            echo "<option value='$bulan_tahun'>$nama_bulan $tahun</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                    <div id="bulanError" class="error-message">Bulan harus dipilih
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="kelas">Status</label>
                                                    <select class="form-control" id="status" name="status"
                                                        required>
                                                        <option value="" selected disabled>Pilih</option>
                                                        <option value="pending">Pending</option>
                                                        <option value="lunas">Lunas</option>
                                                    </select>
                                                    <div id="statusError" class="error-message">Status harus dipilih
                                                    </div>
                                                </div>

                                                <input type="hidden" id="spp_id_221043" name="spp_id_221043">
                                            </div>
                                        </div>
                                        <button type="submit" name="simpan" id="submitButton"
                                            class="btn btn-primary">Submit</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Documentation Link -->
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <p>For more documentations you can visit<a
                                    href="https://getbootstrap.com/docs/4.3/components/forms/" target="_blank">
                                    bootstrap forms documentations.</a> and <a
                                    href="https://getbootstrap.com/docs/4.3/components/input-group/"
                                    target="_blank">bootstrap input
                                    groups documentations</a></p>
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
        document.addEventListener('DOMContentLoaded', function() {
            const siswa = document.getElementById('siswa_id_221043');
            const bulan = document.getElementById('bulan');
            const status = document.getElementById('status');
            const submitButton = document.getElementById('submitButton');

            // Error message elements
            const siswaError = document.getElementById('siswaError');
            const bulanError = document.getElementById('bulanError');
            const statusError = document.getElementById('statusError');

            // Validation functions
            function validateSiswa() {
                if (siswa.value.trim() === '') {
                    siswaError.style.display = 'block';
                    return false;
                }
                siswaError.style.display = 'none';
                return true;
            }

            function validateBulan() {
                if (bulan.value.trim() === '') {
                    bulanError.style.display = 'block';
                    return false;
                }
                bulanError.style.display = 'none';
                return true;
            }

            function validateStatus() {
                if (status.value.trim() === '') {
                    statusError.style.display = 'block';
                    return false;
                }
                statusError.style.display = 'none';
                return true;
            }

            function checkFormValidity() {
                const isSiswaValid = validateSiswa();
                const isBulanValid = validateBulan();
                const isStatusValid = validateStatus();

                submitButton.disabled = !(isSiswaValid && isBulanValid && isStatusValid);
            }

            // Event listeners
            siswa.addEventListener('change', checkFormValidity);
            bulan.addEventListener('change', checkFormValidity);
            status.addEventListener('change', checkFormValidity);
        });
    </script>



    <script type="text/javascript">
        function formatRupiah(angka) {
            var number_string = angka.toString().replace(/[^,\d]/g, ''),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            // Tambahkan titik jika angka lebih dari ribuan
            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            return rupiah; // hasilnya tanpa simbol Rp
        }

        $('#siswa_id_221043').on('change', function() {
            // ambil data dari elemen option yang dipilih
            const kelas = $('#siswa_id_221043 option:selected').data('kelas');
            const biaya = $('#siswa_id_221043 option:selected').data('biaya');
            const spp = $('#siswa_id_221043 option:selected').data('spp');

            // kalkulasi total harga
            const hargaFormatted = formatRupiah(biaya);
            // tampilkan data ke element
            $('[name=kelas]').val(`${kelas}`);
            $('[name=biayaDisplay]').val(`Rp. ${hargaFormatted}`);
            $('[name=spp_id_221043]').val(`${spp}`);

        });
    </script>


    <script>
        // Format tampilan bulan ke bahasa Indonesia
        const monthNames = {
            'January': 'Januari',
            'February': 'Februari',
            'March': 'Maret',
            'April': 'April',
            'May': 'Mei',
            'June': 'Juni',
            'July': 'Juli',
            'August': 'Agustus',
            'September': 'September',
            'October': 'Oktober',
            'November': 'November',
            'December': 'Desember'
        };

        document.addEventListener('DOMContentLoaded', function() {
            const select = document.getElementById('bulan');
            const options = select.options;

            for (let i = 1; i < options.length; i++) {
                const option = options[i];
                const text = option.text;
                const parts = text.split(' ');
                if (parts.length === 2) {
                    const monthName = monthNames[parts[0]] || parts[0];
                    option.text = `${monthName} ${parts[1]}`;
                }
            }
        });
    </script>


</body>

</html>
