<?php
    $server = "localhost";
    $user = "root";
    $pass = "";
    $database = "database_spp_221043";

    $koneksi = mysqli_connect($server,$user,$pass,$database) or die(mysqli_error($koneksi));
?>