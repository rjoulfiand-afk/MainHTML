<?php
session_start();
include "../config/koneksi.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_barang = $_POST['id_barang_permanen'];
    $pass_input = $_POST['password_admin'];
    $q_cek = "SELECT * FROM admin WHERE password = ?";
    $stmt_cek = mysqli_prepare($koneksi, $q_cek);
    mysqli_stmt_bind_param($stmt_cek, "s", $pass_input);
    mysqli_stmt_execute($stmt_cek);
    $hasil = mysqli_stmt_get_result($stmt_cek);
    
    if (mysqli_num_rows($hasil) > 0) {
        $q_del = "DELETE FROM inventory WHERE id_barang = ?";
        $stmt_del = mysqli_prepare($koneksi, $q_del);
        mysqli_stmt_bind_param($stmt_del, "i", $id_barang);
        mysqli_stmt_execute($stmt_del);
        echo "<script>
                alert('OTORISASI SUKSES: Data telah dimusnahkan secara permanen dari database!'); 
                window.location.href='../riwayat_barang.php';
              </script>";
    } else {
        echo "<script>
                alert('OTORISASI GAGAL: Sandi Super Admin yang Anda masukkan salah!'); 
                window.location.href='../riwayat_barang.php';
              </script>";
    }
}
?>