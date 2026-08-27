<?php
session_start();
include "../config/koneksi.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // BUKAN DELETE, TAPI UPDATE STATUS JADI 1 (Masuk History)
    $query = "UPDATE inventory SET status_hapus = 1 WHERE id_barang = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
}
header("Location: ../data_barang.php");
exit;
?>