<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION["kg_isLoggedIn"])) {
    header("Location: ../index.php");
    exit;
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_barang = $_POST['id_barang'];
    $sn = $_POST['serial_number'];
    $nama = $_POST['nama_barang'];
    $jenis = $_POST['jenis_barang'];
    $stok = $_POST['stok'];
    $harga = $_POST['harga'];
    $id_gudang = $_POST['id_gudang'];
    $id_vendor = $_POST['id_vendor'];

    $query = "UPDATE inventory SET serial_number=?, nama_barang=?, jenis_barang=?, kuantitas_stok=?, harga=?, id_gudang=?, id_vendor=? WHERE id_barang=?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "sssiiiii", $sn, $nama, $jenis, $stok, $harga, $id_gudang, $id_vendor, $id_barang);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: ../dashboard.php");
    exit;
}
?>