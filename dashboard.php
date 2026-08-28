<?php
session_start();
if (!isset($_SESSION["kg_isLoggedIn"]) || $_SESSION["kg_isLoggedIn"] !== true) {
    header("Location: index.php");
    exit;
}

include "config/koneksi.php";
$namaAdmin = $_SESSION["kg_namaAdmin"] ?? "Admin";

// --- AMBIL SEMUA DATA STATISTIK DI AWAL ---
$q_stok = mysqli_query($koneksi, "SELECT SUM(kuantitas_stok) AS total_stok FROM inventory WHERE status_hapus = 0");
$tot_stok = mysqli_fetch_assoc($q_stok)['total_stok'] ?? 0;

$q_gudang = mysqli_query($koneksi, "SELECT COUNT(*) AS total_gudang FROM storage_unit");
$tot_gudang = mysqli_fetch_assoc($q_gudang)['total_gudang'] ?? 0;

$q_vendor = mysqli_query($koneksi, "SELECT COUNT(*) AS total_vendor FROM vendor");
$tot_vendor = mysqli_fetch_assoc($q_vendor)['total_vendor'] ?? 0;

$q_restock = mysqli_query($koneksi, "SELECT COUNT(*) AS total_habis FROM inventory WHERE kuantitas_stok = 0 AND status_hapus = 0");
$tot_restock = mysqli_fetch_assoc($q_restock)['total_habis'] ?? 0;

$barang_habis = [];
$q_habis = mysqli_query($koneksi, "SELECT nama_barang FROM inventory WHERE kuantitas_stok = 0 AND status_hapus = 0");
while($b = mysqli_fetch_assoc($q_habis)) {
    $barang_habis[] = $b['nama_barang'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Inventory Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo-box">KG</div>
            <div class="logo-text">
                <h2>KelolaStok</h2>
                <p>Kelompok Ganteng</p>
            </div>
        </div>
        
        <p class="menu-label">MENU UTAMA</p>
        <ul class="nav-links">
            <li class="active"><a href="dashboard.php"><i class="fa-solid fa-border-all"></i> Dashboard Utama</a></li>
            <li><a href="data_barang.php"><i class="fa-solid fa-box-archive"></i> Data Barang</a></li>
            <li><a href="#"><i class="fa-solid fa-warehouse"></i> Lokasi Gudang</a></li>
            <li><a href="#"><i class="fa-solid fa-truck-fast"></i> Data Vendor</a></li>

        </ul>

        <div class="sidebar-footer">
            <a href="actions/logout.php" class="btn-logout">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar Sistem
            </a>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <!-- SEARCH BAR AKTIF -->
            <form action="data_barang.php" method="GET" class="search-box" style="margin: 0;">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" placeholder="Cari barang atau serial number..." style="background: transparent; border: none; outline: none; width: 100%;">
            </form>
            
            <div class="topbar-right">
                <div class="notification" id="btnNotif" style="position: relative; cursor: pointer;">
                    <i class="fa-regular fa-bell"></i>
                    <?php if (!empty($barang_habis)) { echo '<span class="dot"></span>'; } ?>
                    
                    <div id="kotakNotif" style="display: none; position: absolute; right: 0; top: 40px; background: white; width: 280px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); padding: 15px; border: 1px solid #ddd; z-index: 100;">
                        <h4 style="margin: 0 0 10px 0; font-size: 14px; border-bottom: 1px solid #eee; padding-bottom: 10px; color: #333;">Notifikasi Sistem</h4>
                        <?php if(empty($barang_habis)): ?>
                            <p style="font-size: 12px; color: #888; margin: 0;">Aman cuy, tidak ada peringatan restock.</p>
                        <?php else: ?>
                            <?php foreach($barang_habis as $b): ?>
                                <div style="font-size: 12px; color: #d63031; margin-bottom: 8px; background: #fff0f0; padding: 8px; border-radius: 6px;">
                                    <i class="fa-solid fa-circle-exclamation"></i> Stok <b><?= htmlspecialchars($b) ?></b> Habis!
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="profile-area" id="btnProfile" style="position: relative; cursor: pointer;">
                    <div class="profile-info">
                        <h4><?php echo htmlspecialchars($namaAdmin); ?></h4>
                        <p>Super Admin</p>
                    </div>
                    <div class="profile-pic"><i class="fa-solid fa-user-tie"></i></div>
                    
                    <!-- KOTAK MENU DROPDOWN (Awalnya Sembunyi) -->
                    <div id="kotakProfile" style="display: none; position: absolute; right: 0; top: 50px; background: white; width: 200px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); border: 1px solid #ddd; z-index: 100; overflow: hidden;">
                        <a href="riwayat_barang.php" style="display: block; padding: 12px 15px; color: #333; text-decoration: none; border-bottom: 1px solid #eee; font-size: 14px;">
                            <i class="fa-solid fa-clock-rotate-left" style="margin-right: 8px;"></i> Riwayat Hapus
                        </a>
                        <a href="actions/logout.php" style="display: block; padding: 12px 15px; color: #d63031; text-decoration: none; font-size: 14px;">
                            <i class="fa-solid fa-arrow-right-from-bracket" style="margin-right: 8px;"></i> Keluar Sistem
                        </a>
                    </div>
                </div>
            </div> <!-- NAH INI PENUTUP TOPBAR-RIGHT YANG TADI ILANG -->
        </header> <!-- DAN INI PENUTUP HEADERNYA -->

        <script>
            document.getElementById('btnNotif').addEventListener('click', function() {
                var kotak = document.getElementById('kotakNotif');
                kotak.style.display = (kotak.style.display === 'none') ? 'block' : 'none';
                document.getElementById('kotakProfile').style.display = 'none';
            });
            document.getElementById('btnProfile').addEventListener('click', function() {
                var kotakProf = document.getElementById('kotakProfile');
                kotakProf.style.display = (kotakProf.style.display === 'none') ? 'block' : 'none';
                document.getElementById('kotakNotif').style.display = 'none';
            });
        </script>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon safe-bg"><i class="fa-solid fa-cubes"></i></div>
                <div class="stat-info"><p>Total Stok Barang</p><h3><?php echo $tot_stok; ?> <span>Unit</span></h3></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon primary-bg"><i class="fa-solid fa-building"></i></div>
                <div class="stat-info"><p>Gudang Aktif</p><h3><?php echo $tot_gudang; ?> <span>Cabang</span></h3></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon primary-bg"><i class="fa-solid fa-handshake"></i></div>
                <div class="stat-info"><p>Total Vendor</p><h3><?php echo $tot_vendor; ?> <span>Mitra</span></h3></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon danger-bg"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div class="stat-info"><p>Perlu Restock</p><h3 class="text-danger"><?php echo $tot_restock; ?> <span>Barang</span></h3></div>
            </div>
        </div>
        <section class="table-section">
            <div class="table-header">
                <div>
                    <h3>Ringkasan Inventory</h3>
                    <p>Status ketersediaan barang saat ini (Hanya Tampilan).</p>
                </div>
            </div>
            
            <div class="table-responsive">
                <table id="inventoryTable">
                    <thead>
                        <tr>
                            <th>Serial Number</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Kuantitas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query_inventory = mysqli_query($koneksi, "SELECT * FROM inventory WHERE status_hapus = 0 ORDER BY id_barang DESC LIMIT 5");
                        
                        if (mysqli_num_rows($query_inventory) > 0) {
                            while($data = mysqli_fetch_assoc($query_inventory)) {
                                $stok = $data['kuantitas_stok'];
                                $class_baris = ($stok == 0) ? "row-danger" : "";
                                $badge_warna = ($stok == 0) ? "danger" : (($stok <= 5) ? "warning" : "safe");

                                echo "<tr class='$class_baris'>
                                    <td class='font-mono'>" . htmlspecialchars($data['serial_number']) . "</td>
                                    <td class='fw-600'>" . htmlspecialchars($data['nama_barang']) . "</td>
                                    <td><span class='tag'>" . htmlspecialchars($data['jenis_barang']) . "</span></td>
                                    <td><span class='badge $badge_warna'>$stok</span></td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' style='text-align:center; padding:20px; color:#999;'>Belum ada data barang aktif.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <?php
    if (!empty($barang_habis)) {
        $daftar_habis = implode(", ", $barang_habis);
        echo "<script>
            window.addEventListener('DOMContentLoaded', (event) => {
                alert('⚠️ PERINGATAN SISTEM! ⚠️\\n\\nStok untuk barang berikut telah habis:\\n- $daftar_habis\\n\\nMohon segera restock.');
            });
        </script>";
    }
    ?>
    <script src="js/script.js"></script>
</body>
</html>