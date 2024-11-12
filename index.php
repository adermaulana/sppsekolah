<?php

    include 'koneksi.php';

    session_start();

    if(isset($_SESSION['status']) == 'login'){

        header("location:admin");
    }

    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = md5($_POST['password']);
        $role = $_POST['role'];


        if($role == 'admin'){
          $login = mysqli_query($koneksi, "SELECT * FROM `admin_221043`
          WHERE `username_221043` = '$username'
          AND `password_221043` = '$password'");
          $cek = mysqli_num_rows($login);
        }

        if($role == 'orangtua'){
          $loginOrangtua = mysqli_query($koneksi, "SELECT * FROM `orangtua_221043`
          WHERE `username_221043` = '$username'
          AND `password_221043` = '$password'");
          $cekOrangtua = mysqli_num_rows($loginOrangtua);
        }

        if($role == 'siswa'){
          $loginSiswa = mysqli_query($koneksi, "SELECT * FROM `siswa_221043`
          WHERE `username_221043` = '$username'
          AND `password_221043` = '$password'");
          $cekSiswa = mysqli_num_rows($loginSiswa);
        }
    
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
<html>
<head>
  <title>Login</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }
    
    body {
      background: #4c60da;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .wrapper {
      width: 380px;
      background: #fff;
      border-radius: 5px;
      padding: 40px 30px;
      box-shadow: 0px 10px 15px rgba(0,0,0,0.1);
    }
    
    .wrapper .title {
      margin-bottom: 30px;
      text-align: center;
    }
    
    .wrapper .title span {
      font-size: 26px;
      font-weight: 600;
      color: #4c60da;
    }
    
    .wrapper form .row {
      height: 45px;
      margin-bottom: 20px;
      position: relative;
    }
    
    .wrapper form .row input,
    .wrapper form .row select {
      height: 100%;
      width: 100%;
      padding: 0 35px;
      outline: none;
      font-size: 16px;
      border-radius: 5px;
      border: 1px solid #ccc;
      transition: all 0.3s ease;
    }
    
    .wrapper form .row select {
      padding-left: 35px;
      cursor: pointer;
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23888' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: right 10px center;
      background-size: 15px;
    }
    
    .wrapper form .row i {
      position: absolute;
      width: 47px;
      height: 100%;
      font-size: 18px;
      color: #888;
      line-height: 45px;
      text-align: center;
    }
    
    .wrapper form .row input:focus,
    .wrapper form .row select:focus {
      border-color: #4c60da;
      box-shadow: inset 0px 0px 2px 2px rgba(26,188,156,0.25);
    }
    
    .wrapper form .row .button input {
      color: #fff;
      font-size: 18px;
      font-weight: 500;
      padding: 0;
      background: #1abc9c;
      border: 1px solid #1abc9c;
      cursor: pointer;
    }
    
    .wrapper form .row .button input:hover {
      background: #16a085;
    }
  </style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
  <div class="wrapper">
    <div class="title"><span>Login Form</span></div>
    <form method="POST">
      <div class="row">
        <i class="fas fa-user"></i>
        <input type="text" placeholder="Username" name="username" required>
      </div>
      <div class="row">
        <i class="fas fa-lock"></i>
        <input type="password" placeholder="Password" name="password" required>
      </div>
      <div class="row">
        <i class="fas fa-user-tag"></i>
        <select name="role" required>
          <option value="" disabled selected>Select Role</option>
          <option value="admin">Admin</option>
          <option value="siswa">Siswa</option>
          <option value="orangtua">Orang Tua</option>
        </select>
      </div>
      <div class="row button">
        <input type="submit" value="Login" name="login">
      </div>
    </form>
  </div>
</body>
</html>