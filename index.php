<?php

    include 'koneksi.php';

    session_start();

    if(isset($_SESSION['status']) == 'login'){

        header("location:admin");
    }

    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = md5($_POST['password']);
    
        $login = mysqli_query($koneksi, "SELECT * FROM `admin_221043`
                                    WHERE `username_221043` = '$username'
                                    AND `password_221043` = '$password'");
        $cek = mysqli_num_rows($login);

        $loginOrangtua = mysqli_query($koneksi, "SELECT * FROM `orangtua_221043`
                                    WHERE `username_221043` = '$username'
                                    AND `password_221043` = '$password'");
        $cekOrangtua = mysqli_num_rows($loginOrangtua);

        $loginSiswa = mysqli_query($koneksi, "SELECT * FROM `siswa_221043`
                                    WHERE `username_221043` = '$username'
                                    AND `password_221043` = '$password'");
        $cekSiswa = mysqli_num_rows($loginSiswa);
    
        if ($cek > 0) {
            // Ambil data user
            $admin_data = mysqli_fetch_assoc($login);
            // Simpan data ke dalam session
            $_SESSION['id_admin'] = $admin_data['id_221043']; // Pastikan sesuai dengan nama kolom di database
            $_SESSION['nama_admin'] = $admin_data['name_221043']; // Pastikan sesuai dengan nama kolom di database
            $_SESSION['username_admin'] = $username;
            $_SESSION['status'] = "login";
            // Redirect ke halaman admin
            header('location:admin');
        } else if ($cekOrangtua > 0) {
          // Ambil data user
          $admin_data = mysqli_fetch_assoc($loginOrangtua);
          // Simpan data ke dalam session
          $_SESSION['id_orangtua'] = $admin_data['id_221043']; // Pastikan sesuai dengan nama kolom di database
          $_SESSION['nama_orangtua'] = $admin_data['nama_221043']; // Pastikan sesuai dengan nama kolom di database
          $_SESSION['username_orangtua'] = $username;
          $_SESSION['status'] = "login";
          // Redirect ke halaman admin
          header('location:orangtua');
      } else if ($cekSiswa > 0) {
        // Ambil data user
        $admin_data = mysqli_fetch_assoc($loginSiswa);
        // Simpan data ke dalam session
        $_SESSION['id_siswa'] = $admin_data['id_221043']; // Pastikan sesuai dengan nama kolom di database
        $_SESSION['nama_siswa'] = $admin_data['nama_221043']; // Pastikan sesuai dengan nama kolom di database
        $_SESSION['username_siswa'] = $username;
        $_SESSION['status'] = "login";
        // Redirect ke halaman admin
        header('location:siswa');
    } else {
            echo "<script>
                alert('Login Gagal, Periksa Username dan Password Anda!');
                window.location.href = 'index.php';
                 </script>";
        }
    }
    

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <link rel="stylesheet" href="style.css" />
  <!-- Font Awesome CDN link for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />

  <style>
    /* Importing Google Fonts - Poppins */
    @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap");

    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Poppins", sans-serif;
    }

    body {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
    padding: 15px;
    background: #6777ef;
    overflow: hidden;
    }

    .wrapper {
    max-width: 500px;
    width: 100%;
    background: #fff;
    border-radius: 5px;
    box-shadow: 0px 4px 10px 1px rgba(0, 0, 0, 0.1);
    }

    .wrapper .title {
    height: 120px;
    background: #4c60da;
    border-radius: 5px 5px 0 0;
    color: #fff;
    font-size: 30px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    }

    .wrapper form {
    padding: 25px 35px;
    }

    .wrapper form .row {
    height: 60px;
    margin-top: 15px;
    position: relative;
    }

    .wrapper form .row input {
    height: 100%;
    width: 100%;
    outline: none;
    padding-left: 70px;
    border-radius: 5px;
    border: 1px solid lightgrey;
    font-size: 18px;
    transition: all 0.3s ease;
    }

    form .row input:focus {
    border-color: #4c60da;
    }

    form .row input::placeholder {
    color: #999;
    }

    .wrapper form .row i {
    position: absolute;
    width: 55px;
    height: 100%;
    color: #fff;
    font-size: 22px;
    background: #4c60da;
    border: 1px solid #4c60da;
    border-radius: 5px 0 0 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    }

    .wrapper form .pass {
    margin-top: 12px;
    }

    .wrapper form .pass a {
    color: #4c60da;
    font-size: 17px;
    text-decoration: none;
    }

    .wrapper form .pass a:hover {
    text-decoration: underline;
    }

    .wrapper form .button input {
    margin-top: 20px;
    color: #fff;
    font-size: 20px;
    font-weight: 500;
    padding-left: 0px;
    background: #4c60da;
    border: 1px solid #4c60da;
    cursor: pointer;
    }

    form .button input:hover {
    background: #6777ef;
    }

    .wrapper form .signup-link {
    text-align: center;
    margin-top: 45px;
    font-size: 17px;
    }

    .wrapper form .signup-link a {
    color: #16a085;
    text-decoration: none;
    }

    form .signup-link a:hover {
    text-decoration: underline;
    }
  </style>

</head>
<body>
  <div class="wrapper">
    <div class="title"><span>Login Form</span></div>
    <form method="POST">
      <div class="row">
        <i class="fas fa-user"></i>
        <input type="text" placeholder="Username" name="username" required />
      </div>
      <div class="row">
        <i class="fas fa-lock"></i>
        <input type="password" placeholder="Password" name="password" required />
      </div>
      <div class="row button">
        <input type="submit" value="Login" name="login" />
      </div>
      <!-- <div class="signup-link">Not a member? <a href="#">Signup now</a></div> -->
    </form>
  </div>
</body>
</html>