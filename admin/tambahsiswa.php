<?php

include '../koneksi.php';

session_start();

if ($_SESSION['status'] != 'login') {
    session_unset();
    session_destroy();

    header('location:../');
}

if (isset($_POST['simpan'])) {
    // Insert into orangtua_221043 table first
    $namaOrtu = $_POST['namaortu'];
    $usernameOrtu = $_POST['usernameortu'];
    $emailOrtu = $_POST['emailortu'];
    $passwordOrtu = md5($_POST['passwordortu']); // Hash the password

    $insertOrtu = mysqli_query($koneksi, "INSERT INTO orangtua_221043 (nama_221043,username_221043, email_221043, password_221043) VALUES ('$namaOrtu','$usernameOrtu', '$emailOrtu', '$passwordOrtu')");

    if ($insertOrtu) {
        // Get the id of the inserted orangtua
        $idOrtu = mysqli_insert_id($koneksi);

        // Now insert into siswa_221043 table
        $namaSiswa = $_POST['nama'];
        $usernameSiswa = $_POST['username'];
        $kelas = $_POST['id_kelas_221043'];
        $alamat = $_POST['alamat'];
        $passwordSiswa = md5($_POST['password']); // Hash the password for siswa

        $insertSiswa = mysqli_query($koneksi, "INSERT INTO siswa_221043 (nama_221043,username_221043, id_kelas_221043, alamat_221043, password_221043, orangtua_id_221043) VALUES ('$namaSiswa','$usernameSiswa', '$kelas', '$alamat', '$passwordSiswa', '$idOrtu')");

        if ($insertSiswa) {
            echo "<script>
                    alert('Simpan data siswa dan orang tua sukses!');
                    document.location='siswa.php';
                </script>";
        } else {
            echo "<script>
                    alert('Simpan data siswa gagal!');
                    document.location='tambahsiswa.php';
                </script>";
        }
    } else {
        echo "<script>
                alert('Simpan data orang tua gagal!');
                document.location='tambahsiswa.php';
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
                        <h1 class="h3 mb-0 text-gray-800">Tambah Siswa</h1>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <!-- Form Basic -->
                            <div class="card mb-4">
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Form Siswa dan Orang Tua</h6>
                                </div>
                                <div class="card-body">
                                    <!-- Form start -->
                                    <form method="POST">
                                        <div class="row">
                                            <!-- Siswa Section (Left Column) -->
                                            <div class="col-lg-6">
                                                <h6 class="font-weight-bold text-primary">Siswa</h6>

                                                <!-- Nama Input -->
                                                <div class="form-group">
                                                    <label for="nama">Nama</label>
                                                    <input type="text" class="form-control" name="nama"
                                                        id="nama" placeholder="Enter Nama" required>
                                                    <div id="namaError" class="error-message">Nama harus diisi</div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="nama">Username</label>
                                                    <input type="text" class="form-control" name="username"
                                                        id="username" placeholder="Enter Username" required>
                                                    <div id="usernameError" class="error-message">Username harus diisi
                                                    </div>
                                                </div>

                                                <!-- Kelas Select -->
                                                <div class="form-group">
                                                    <label for="kelas">Kelas</label>
                                                    <select class="form-control" id="id_kelas_221043"
                                                        name="id_kelas_221043" required>
                                                        <option value="" disabled selected>Pilih</option>
                                                        <?php
                            $no = 1;
                            $tampil = mysqli_query($koneksi, "SELECT * FROM kelas_221043");
                            while($data = mysqli_fetch_array($tampil)):
                        ?>
                                                        <option value="<?= $data['id_221043'] ?>">
                                                            <?= $data['kelas_221043'] ?></option>
                                                        <?php
                        endwhile; 
                        ?>
                                                    </select>
                                                </div>

                                                <!-- Alamat Textarea -->
                                                <div class="form-group">
                                                    <label for="alamat">Alamat</label>
                                                    <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Enter Alamat" required></textarea>
                                                    <div id="alamatError" class="error-message">Alamat harus diisi
                                                    </div>
                                                </div>

                                                <!-- Password Input -->
                                                <div class="form-group">
                                                    <label for="password">Password</label>
                                                    <input type="password" class="form-control" id="password"
                                                        name="password" placeholder="Password" required>
                                                    <div id="passwordError" class="error-message">Password harus diisi
                                                    </div>
                                                </div>
                                            </div>


                                            <!-- Orang Tua Section (Right Column) -->
                                            <div class="col-lg-6">
                                                <h6 class="font-weight-bold text-primary">Orang Tua</h6>
                                                <!-- Nama Input -->
                                                <div class="form-group">
                                                    <label for="namaortu">Nama</label>
                                                    <input type="text" class="form-control" name="namaortu"
                                                        id="namaortu" placeholder="Enter Nama" required>
                                                    <div id="namaOrtuError" class="error-message">Nama harus diisi
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="nama">Username</label>
                                                    <input type="text" class="form-control" name="usernameortu"
                                                        id="usernameortu" placeholder="Enter Username" required>
                                                    <div id="usernameOrtuError" class="error-message">Username harus
                                                        diisi</div>
                                                </div>

                                                <!-- Email Input -->
                                                <div class="form-group">
                                                    <label for="email">Email</label>
                                                    <input type="email" class="form-control" id="emailortu"
                                                        name="emailortu" placeholder="Enter email" required>
                                                    <div id="emailError" class="error-message">Email harus diisi</div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="ortuPassword">Password</label>
                                                    <input type="password" class="form-control" id="passwordortu"
                                                        name="passwordortu" placeholder="Password" required>
                                                    <div id="passwordOrtuError" class="error-message">Password harus
                                                        diisi</div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Single Submit Button -->
                                        <button type="submit" name="simpan" id="submitButton"
                                            class="btn btn-primary">Submit</button>
                                    </form>
                                    <!-- Form end -->
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
            const nama = document.getElementById('nama');
            const username = document.getElementById('username');
            const alamat = document.getElementById('alamat');
            const password = document.getElementById('password');
            const namaortu = document.getElementById('namaortu');
            const usernameortu = document.getElementById('usernameortu');
            const emailortu = document.getElementById('emailortu');
            const passwordortu = document.getElementById('passwordortu');
            const submitButton = document.getElementById('submitButton');

            // Error message elements
            const namaError = document.getElementById('namaError');
            const usernameError = document.getElementById('usernameError');
            const alamatError = document.getElementById('alamatError');
            const passwordError = document.getElementById('passwordError');
            const namaOrtuError = document.getElementById('namaOrtuError');
            const usernameOrtuError = document.getElementById('usernameOrtuError');
            const emailError = document.getElementById('emailError');
            const passwordOrtuError = document.getElementById('passwordOrtuError');

            // Validation functions
            function validateNama() {
                if (nama.value.trim() === '') {
                    namaError.style.display = 'block';
                    return false;
                }

                if (nama.value.trim().length < 5) {
                    namaError.textContent = 'Nama minimal 5 karakter';
                    namaError.style.display = 'block';
                    return false;
                }

                namaError.style.display = 'none';
                return true;
            }

            function validateUsername() {
                if (username.value.trim().length < 6) {
                    usernameError.textContent = 'Username minimal 6 karakter';
                    usernameError.style.display = 'block';
                    return false;
                }
                usernameError.style.display = 'none';
                return true;
            }


            function validateAlamat() {
                if (alamat.value.trim() === '') {
                    alamatError.style.display = 'block';
                    return false;
                }
                alamatError.style.display = 'none';
                return true;
            }


            function validatePassword() {
                // Password must be at least 8 characters long
                // Must contain at least one uppercase, one lowercase, one number, and one special character
                const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

                if (password.value.length < 8) {
                    passwordError.textContent = 'Password minimal 8 karakter';
                    passwordError.style.display = 'block';
                    return false;
                }

                passwordError.style.display = 'none';
                return true;
            }

            function validateNamaOrtu() {
                if (namaortu.value.trim() === '') {
                    namaOrtuError.style.display = 'block';
                    return false;
                }

                if (namaortu.value.trim().length < 5) {
                    namaOrtuError.textContent = 'Nama minimal 5 karakter';
                    namaOrtuError.style.display = 'block';
                    return false;
                }

                namaOrtuError.style.display = 'none';
                return true;
            }


            function validateUsernameOrtu() {
                if (usernameortu.value.trim().length < 6) {
                    usernameOrtuError.textContent = 'Username minimal 6 karakter';
                    usernameOrtuError.style.display = 'block';
                    return false;
                }
                usernameOrtuError.style.display = 'none';
                return true;
            }

            function validateEmail() {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailortu.value)) {
                    emailError.style.display = 'block';
                    return false;
                }
                emailError.style.display = 'none';
                return true;
            }


            function validatePasswordOrtu() {
                // Password must be at least 8 characters long
                // Must contain at least one uppercase, one lowercase, one number, and one special character
                const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

                if (passwordortu.value.length < 8) {
                    passwordOrtuError.textContent = 'Password minimal 8 karakter';
                    passwordOrtuError.style.display = 'block';
                    return false;
                }

                passwordOrtuError.style.display = 'none';
                return true;
            }

            function checkFormValidity() {
                const isNamaValid = validateNama();
                const isUsernameValid = validateUsername();
                const isAlamatValid = validateAlamat();
                const isPasswordValid = validatePassword();
                const isNamaOrtuValid = validateNamaOrtu();
                const isUsernameOrtuValid = validateUsernameOrtu();
                const isEmailValid = validateEmail();
                const isPasswordOrtuValid = validatePasswordOrtu();

                // Enable or disable the submit button based on all validations
                if (isNamaValid && isUsernameValid && isAlamatValid && isPasswordValid && isNamaOrtuValid && isUsernameOrtuValid && isEmailValid && isPasswordOrtuValid) {
                    submitButton.disabled = false;
                } else {
                    submitButton.disabled = true;
                }

            }


            // Real-time validation
            nama.addEventListener('input', checkFormValidity);
            username.addEventListener('input', checkFormValidity);
            alamat.addEventListener('input', checkFormValidity);
            password.addEventListener('input', checkFormValidity);
            namaortu.addEventListener('input', checkFormValidity);
            usernameortu.addEventListener('input', checkFormValidity);
            emailortu.addEventListener('input', checkFormValidity);
            passwordortu.addEventListener('input', checkFormValidity);

            // Form submission validation
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                checkFormValidity();

                if (!submitButton.disabled) {
                    form.submit();
                }

            });
        });
    </script>

</body>

</html>
