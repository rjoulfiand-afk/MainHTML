<?php
session_start();
include "config/koneksi.php";

if (!isset($_SESSION["kg_isLoggedIn"])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Riwayat Hapus - KelolaStok</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <!-- SIDEBAR (Sama kayak biasa, tambahin menu Riwayat) -->
    <aside class="sidebar">
        <div class="sidebar-header"><div class="logo-box">KG</div><div class="logo-text"><h2>KelolaStok</h2><p>Kelompok Ganteng</p></div></div>
        <ul class="nav-links">
            <li><a href="dashboard.php"><i class="fa-solid fa-border-all"></i> Dashboard Utama</a></li>
            <li><a href="data_barang.php"><i class="fa-solid fa-box-archive"></i> Data Barang</a></li>
            <li class="active"><a style="background:#fff0f3; color:#800020; font-weight:bold;" href="riwayat_barang.php"><i class="fa-solid fa-trash-can-arrow-up"></i> Riwayat Hapus</a></li>
        </ul>
        <div class="sidebar-footer"><a href="actions/logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar</a></div>
    </aside>

    <main class="main-content">
        <section class="table-section">
            <div class="table-header">
                <div>
                    <h3 style="color: #d63031;">Keranjang Sampah (History)</h3>
                    <p>Data yang dihapus ada di sini. Butuh akses Super Admin untuk hapus permanen.</p>
                </div>
            </div>
            
            <table style="width: 100%; text-align: left; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #ddd; padding: 10px;">
                        <th>SN</th><th>Nama Barang</th><th>Status</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // AMBIL DATA YANG STATUS HAPUS = 1
                    $q_history = mysqli_query($koneksi, "SELECT * FROM inventory WHERE status_hapus = 1");
                    while($row = mysqli_fetch_assoc($q_history)) {
                        echo "<tr style='border-bottom: 1px solid #eee;'>
                            <td style='padding: 15px;'>{$row['serial_number']}</td>
                            <td>{$row['nama_barang']}</td>
                            <td><span style='background:#fcebeb; color:#d63031; padding:5px; border-radius:5px; font-size:12px;'>Menunggu Dihapus</span></td>
                            <td>
                                <!-- Tombol Restore (Kembalikan) -->
                                <a href='actions/restore_barang.php?id={$row['id_barang']}' style='padding:8px 12px; background:#e5f8ed; color:#27ae60; text-decoration:none; border-radius:6px; margin-right:5px;' title='Kembalikan Data'><i class='fa-solid fa-rotate-left'></i></a>
                                
                                <!-- Tombol Hapus Permanen (Memicu JS) -->
                                <button onclick='bukaModalAuth({$row['id_barang']}, \"{$row['nama_barang']}\")' style='padding:8px 12px; background:#d63031; color:white; border:none; border-radius:6px; cursor:pointer;'><i class='fa-solid fa-skull'></i> Hapus Permanen</button>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </main>

    <!-- MODAL AUTH DELETE (VERIFIKASI ADMIN) -->
    <div id="modalAuth" class="modal-overlay" style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:999; justify-content:center; align-items:center;">
        <div style="background: white; padding: 25px; border-radius: 12px; width: 400px; text-align: center;">
            <i class="fa-solid fa-triangle-exclamation" style="font-size: 40px; color: #d63031; margin-bottom: 15px;"></i>
            <h3>Otorisasi Super Admin</h3>
            <p style="font-size: 13px; color: #666; margin-bottom: 20px;">Masukkan sandi admin untuk menghapus <b id="namaBarangHapus"></b> secara permanen. Data tidak bisa dikembalikan!</p>
            
            <form action="actions/hapus_permanen.php" method="POST">
                <input type="hidden" name="id_barang_permanen" id="idBarangPermanen">
                <input type="password" name="password_admin" placeholder="Masukkan Sandi Admin (kg2026)" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 15px;">
                
                <div style="display: flex; gap: 10px; justify-content: center;">
                    <button type="button" onclick="document.getElementById('modalAuth').style.display='none'" style="padding: 10px 15px; background: #eee; border: none; border-radius: 6px; cursor: pointer;">Batal</button>
                    <button type="submit" style="padding: 10px 15px; background: #d63031; color: white; border: none; border-radius: 6px; cursor: pointer;">Musnahkan Data</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function bukaModalAuth(id, nama) {
            document.getElementById('idBarangPermanen').value = id;
            document.getElementById('namaBarangHapus').innerText = nama;
            document.getElementById('modalAuth').style.display = 'flex';
        }
    </script>
</body>
</html>