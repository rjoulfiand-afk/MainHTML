<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION["kg_isLoggedIn"])) {
    header("Location: ../index.html");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Query Hapus
    $query = "DELETE FROM inventory WHERE id_barang = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// Tendang balik ke dashboard
header("Location: ../dashboard.php");
exit;
?>