<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION["kg_isLoggedIn"])) {
    header("Location: ../index.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $nama_gudang = $_POST['nama_gudang'];
    $lokasi = $_POST['lokasi'];

    $query = "INSERT INTO storage_unit (nama_gudang, lokasi) VALUES (?, ?)";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "ss", $nama_gudang, $lokasi);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../dashboard.php");
        exit;
    } else {
        echo "Gagal menambah gudang: " . mysqli_error($koneksi);
    }
    
    mysqli_stmt_close($stmt);
}
?>