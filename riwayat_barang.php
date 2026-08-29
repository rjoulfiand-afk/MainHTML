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
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }
        body { background-color: #f4f6f9; color: #333; padding: 40px 20px; }
        .container { max-width: 1000px; margin: 0 auto; }
        
        .header-nav { margin-bottom: 25px; }
        .btn-back { display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; background: #ffffff; color: #555; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 14px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); border: 1px solid #e5e9f2; transition: 0.3s; }
        .btn-back:hover { background: #fff0f3; color: #800020; border-color: #800020; transform: translateX(-3px); }
        
        .table-section { background: #ffffff; border-radius: 16px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #e5e9f2; }
        .table-header { margin-bottom: 25px; border-bottom: 2px solid #f4f6f9; padding-bottom: 20px; }
        .table-header h3 { color: #d63031; font-size: 22px; margin-bottom: 5px; display: flex; align-items: center; gap: 10px; }
        .table-header p { color: #888; font-size: 14px; }
        
        table { width: 100%; border-collapse: collapse; }
        th { color: #a098ae; font-size: 12px; text-transform: uppercase; padding: 15px; border-bottom: 2px solid #f4f6f9; letter-spacing: 0.5px; }
        td { padding: 18px 15px; border-bottom: 1px solid #f8f9fa; font-size: 14px; color: #3b3544; }
        
        .badge-hapus { background: #fff0f0; color: #d63031; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; }
        
        .btn-action { padding: 8px 14px; border: none; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: 0.2s; text-decoration: none; font-family: 'Outfit'; }
        .btn-restore { background: #e5f8ed; color: #27ae60; margin-right: 8px; }
        .btn-restore:hover { background: #d1f0df; transform: translateY(-2px); }
        .btn-delete { background: #d63031; color: white; }
        .btn-delete:hover { background: #b02728; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(214, 48, 49, 0.2); }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-nav">
            <a href="dashboard.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard</a>
        </div>

        <section class="table-section">
            <div class="table-header">
                <h3><i class="fa-solid fa-trash-can-arrow-up"></i> Keranjang Sampah (History)</h3>
                <p>Data yang dihapus (Soft Delete) masuk ke sini. Butuh otorisasi Super Admin untuk memusnahkan data permanen.</p>
            </div>
            
            <table style="text-align: left;">
                <thead>
                    <tr>
                        <th>Serial Number</th>
                        <th>Nama Barang</th>
                        <th>Status Saat Ini</th>
                        <th>Aksi Pemulihan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $q_history = mysqli_query($koneksi, "SELECT * FROM inventory WHERE status_hapus = 1");
                    
                    if (mysqli_num_rows($q_history) > 0) {
                        while($row = mysqli_fetch_assoc($q_history)) {
                            echo "<tr>
                                <td style='font-family: monospace; color: #6a6175;'>{$row['serial_number']}</td>
                                <td style='font-weight: 600;'>{$row['nama_barang']}</td>
                                <td><span class='badge-hapus'>Menunggu Dihapus</span></td>
                                <td>
                                    <!-- JALUR RESTORE SUDAH BENAR -->
                                    <a href='actions/proses_barang.php?aksi=restore&id={$row['id_barang']}' class='btn-action btn-restore' title='Kembalikan Data'><i class='fa-solid fa-rotate-left'></i> Restore</a>
                                    
                                    <button onclick='bukaModalAuth({$row['id_barang']}, \"{$row['nama_barang']}\")' class='btn-action btn-delete'><i class='fa-solid fa-skull'></i> Hapus Permanen</button>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' style='text-align:center; padding: 40px; color: #999;'>Yeay! Keranjang sampah kosong.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </div>

    <!-- MODAL AUTH DELETE -->
    <div id="modalAuth" style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(26, 21, 37, 0.6); backdrop-filter: blur(4px); z-index:999; justify-content:center; align-items:center;">
        <div style="background: white; padding: 30px; border-radius: 16px; width: 420px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <i class="fa-solid fa-triangle-exclamation" style="font-size: 45px; color: #d63031; margin-bottom: 15px;"></i>
            <h3 style="margin-bottom: 8px; color: #1a1525;">Otorisasi Super Admin</h3>
            <p style="font-size: 13px; color: #6a6175; margin-bottom: 25px; line-height: 1.5;">Masukkan sandi admin untuk menghapus <b id="namaBarangHapus" style="color:#d63031;"></b> secara permanen. Data ini tidak bisa dikembalikan lagi!</p>
            
            <!-- JALUR HAPUS PERMANEN SUDAH BENAR -->
            <form action="actions/proses_barang.php?aksi=hapus_permanen" method="POST">
                <input type="hidden" name="id_barang_permanen" id="idBarangPermanen">
                <input type="password" name="password_admin" placeholder="Masukkan Sandi Admin (kg2026)" required style="width: 100%; padding: 12px 15px; border: 1px solid #e5e9f2; border-radius: 10px; margin-bottom: 20px; outline: none; font-size: 14px; font-family: 'Outfit';">
                
                <div style="display: flex; gap: 12px; justify-content: center;">
                    <button type="button" onclick="document.getElementById('modalAuth').style.display='none'" style="padding: 10px 20px; background: #f4f6f9; color: #6a6175; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-family: 'Outfit'; transition: 0.2s;">Batal</button>
                    <button type="submit" style="padding: 10px 20px; background: #d63031; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-family: 'Outfit'; transition: 0.2s;">Musnahkan Data</button>
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