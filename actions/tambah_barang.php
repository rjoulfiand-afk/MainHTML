<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION["kg_isLoggedIn"])) {
    header("Location: ../index.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $serial_number = $_POST['serial_number'];
    $nama_barang   = $_POST['nama_barang'];
    $jenis_barang  = $_POST['jenis_barang'];
    $stok          = $_POST['stok'];
    $harga         = $_POST['harga'];
    $id_gudang     = $_POST['id_gudang'];
    $id_vendor     = $_POST['id_vendor'];

    $query = "INSERT INTO inventory (serial_number, nama_barang, jenis_barang, kuantitas_stok, harga, id_gudang, id_vendor) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
              
    $stmt = mysqli_prepare($koneksi, $query);
    
    mysqli_stmt_bind_param($stmt, "sssiiii", $serial_number, $nama_barang, $jenis_barang, $stok, $harga, $id_gudang, $id_vendor);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../dashboard.php");
        exit;
    } else {
        echo "Gagal menambahkan barang: " . mysqli_error($koneksi);
    }
    
    mysqli_stmt_close($stmt);
}
?>