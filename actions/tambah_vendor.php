<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION["kg_isLoggedIn"])) {
    header("Location: ../index.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $nama_vendor = $_POST['nama_vendor'];
    $kontak = $_POST['kontak'];
    $nama_barang_supply = $_POST['nama_barang_supply'];

    $query = "INSERT INTO vendor (nama_vendor, kontak, nama_barang_supply) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $query);
    
    mysqli_stmt_bind_param($stmt, "sss", $nama_vendor, $kontak, $nama_barang_supply);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../dashboard.php");
        exit;
    } else {
        echo "Gagal menambah vendor: " . mysqli_error($koneksi);
    }
    
    mysqli_stmt_close($stmt);
}
?>