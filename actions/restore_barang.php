<?php
session_start();
include "../config/koneksi.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Kembalikan status hapus jadi 0 (Aktif)
    $query = "UPDATE inventory SET status_hapus = 0 WHERE id_barang = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
}

// Tendang balik ke halaman riwayat
header("Location: ../riwayat_barang.php");
exit;
?>