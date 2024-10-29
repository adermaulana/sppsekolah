<?php
// Koneksi ke database
include '../koneksi.php'; // Ganti dengan file koneksi Anda

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pembayaran_id = $_POST['id_221043'];
    
    // Cek apakah file telah diunggah
    if (isset($_FILES['bukti_pembayaran_221043']) && $_FILES['bukti_pembayaran_221043']['error'] == 0) {
        // Nama file dan lokasi penyimpanan
        $file_name = $_FILES['bukti_pembayaran_221043']['name'];
        $file_tmp = $_FILES['bukti_pembayaran_221043']['tmp_name'];
        $upload_dir = 'uploads/'; // Pastikan folder ini ada dan memiliki izin write

        // Menentukan path untuk menyimpan file
        $file_path = $upload_dir . basename($file_name);

        // Memindahkan file ke direktori yang diinginkan
        if (move_uploaded_file($file_tmp, $file_path)) {
            // Menyimpan informasi ke database (jika diperlukan)
            // Contoh query untuk menyimpan nama file
            $query = "UPDATE pembayaran_221043 SET bukti_pembayaran_221043 = '$file_path' WHERE id_221043 = '$pembayaran_id'";
            $result = mysqli_query($koneksi, $query);

            if ($result) {
                header("Location: bayar.php?message=Foto berhasil diunggah."); // Ganti dengan halaman yang sesuai
            } else {
                echo "Error updating record: " . mysqli_error($koneksi);
            }
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "No file uploaded or upload error.";
    }
} else {
    echo "Invalid request method.";
}
?>
