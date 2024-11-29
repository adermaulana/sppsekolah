<?php
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pembayaran_id = $_POST['pembayaran_id'];
    $status = $_POST['status'];
    $tanggal_bayar = date('Y-m-d');
    
    $query = "UPDATE pembayaran_221043 
              SET status_221043 = ?, 
                  tanggal_bayar_221043 = ? 
              WHERE siswa_id_221043 = ?";
              
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "ssi", $status, $tanggal_bayar, $pembayaran_id);
    
    $response = array();
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
            alert('Berhasil update status!');
            document.location='pembayaran.php';
        </script>";
    } else {
        echo "<script>
            alert('Gaggal update status!');
            document.location='pembayaran.php';
        </script>";
    }
    

}
?>