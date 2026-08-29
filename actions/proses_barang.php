<?php
session_start();
include "../config/koneksi.php";

// Tangkap perintah aksi dari URL (GET)
$aksi = $_GET['aksi'] ?? '';

switch ($aksi) {
    case 'tambah':
        $sn = $_POST['serial_number'];
        $nama = $_POST['nama_barang'];
        $jenis = $_POST['jenis_barang'];
        $stok = $_POST['stok'];
        $harga = $_POST['harga'];
        $id_gudang = $_POST['id_gudang'];
        $id_vendor = $_POST['id_vendor'];

        $q = "INSERT INTO inventory (serial_number, nama_barang, jenis_barang, kuantitas_stok, harga, id_gudang, id_vendor) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $q);
        mysqli_stmt_bind_param($stmt, "sssiiii", $sn, $nama, $jenis, $stok, $harga, $id_gudang, $id_vendor);
        mysqli_stmt_execute($stmt);
        header("Location: ../data_barang.php");
        break;

    case 'edit':
        $id = $_POST['id_barang'];
        $sn = $_POST['serial_number'];
        $nama = $_POST['nama_barang'];
        $jenis = $_POST['jenis_barang'];
        $stok = $_POST['stok'];
        $harga = $_POST['harga'];
        $id_gudang = $_POST['id_gudang'];
        $id_vendor = $_POST['id_vendor'];

        $q = "UPDATE inventory SET serial_number=?, nama_barang=?, jenis_barang=?, kuantitas_stok=?, harga=?, id_gudang=?, id_vendor=? WHERE id_barang=?";
        $stmt = mysqli_prepare($koneksi, $q);
        mysqli_stmt_bind_param($stmt, "sssiiiii", $sn, $nama, $jenis, $stok, $harga, $id_gudang, $id_vendor, $id);
        mysqli_stmt_execute($stmt);
        header("Location: ../data_barang.php");
        break;

    case 'soft_delete':
        $id = $_GET['id'];
        $q = "UPDATE inventory SET status_hapus = 1 WHERE id_barang = ?";
        $stmt = mysqli_prepare($koneksi, $q);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        header("Location: ../data_barang.php");
        break;

    case 'restore':
        $id = $_GET['id'];
        $q = "UPDATE inventory SET status_hapus = 0 WHERE id_barang = ?";
        $stmt = mysqli_prepare($koneksi, $q);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        header("Location: ../riwayat_barang.php");
        break;

    case 'hapus_permanen':
        $id = $_POST['id_barang_permanen'];
        $pass = $_POST['password_admin'];
        
        $q_cek = "SELECT * FROM admin WHERE password = ?";
        $stmt_cek = mysqli_prepare($koneksi, $q_cek);
        mysqli_stmt_bind_param($stmt_cek, "s", $pass);
        mysqli_stmt_execute($stmt_cek);
        
        if (mysqli_num_rows(mysqli_stmt_get_result($stmt_cek)) > 0) {
            $q_del = "DELETE FROM inventory WHERE id_barang = ?";
            $stmt_del = mysqli_prepare($koneksi, $q_del);
            mysqli_stmt_bind_param($stmt_del, "i", $id);
            mysqli_stmt_execute($stmt_del);
            echo "<script>alert('Otorisasi Sukses: Data musnah!'); window.location.href='../riwayat_barang.php';</script>";
        } else {
            echo "<script>alert('Otorisasi Gagal: Sandi salah!'); window.location.href='../riwayat_barang.php';</script>";
        }
        break;

    default:
        header("Location: ../dashboard.php");
        break;
}
exit;
?>