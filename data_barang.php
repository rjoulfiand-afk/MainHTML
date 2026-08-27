<?php
session_start();
include "config/koneksi.php";

// Proteksi Halaman Login
if (!isset($_SESSION["kg_isLoggedIn"]) || $_SESSION["kg_isLoggedIn"] !== true) {
    header("Location: index.html?error=unauthorized");
    exit;
}

// Ambil data terbaru dari tabel inventory
$query = "SELECT * FROM inventory";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Barang - KelolaStok</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }
        .app-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }
        .sidebar {
            width: 260px;
            background: #ffffff;
            padding: 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 1px solid #eef2f5;
            flex-shrink: 0;
        }
        .main-content {
            flex: 1;
            padding: 30px;
            display: flex;
            flex-direction: column;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div>
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 30px;">
                    <div style="background: #800020; color: #fff; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 700;">KG</div>
                    <div>
                        <h3 style="margin: 0; font-size: 16px; font-weight: 700;">KelolaStok</h3>
                        <p style="margin: 0; font-size: 12px; color: #888;">Kelompok Ganteng</p>
                    </div>
                </div>

                <div style="font-size: 11px; font-weight: 700; color: #aaa; letter-spacing: 0.5px; margin-bottom: 15px;">MENU UTAMA</div>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 8px;">
                        <a href="dashboard.php" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 10px; color: #555; text-decoration: none; font-weight: 500;"><i class="fa-solid fa-border-all"></i> Dashboard Utama</a>
                    </li>
                    <li style="margin-bottom: 8px;">
                        <a href="data_barang.php" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 10px; background: #fff0f3; color: #800020; text-decoration: none; font-weight: 600;"><i class="fa-solid fa-box-archive"></i> Data Barang</a>
                    </li>
                    <li style="margin-bottom: 8px;">
                        <a href="lokasi_gudang.php" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 10px; color: #555; text-decoration: none; font-weight: 500;"><i class="fa-solid fa-warehouse"></i> Lokasi Gudang</a>
                    </li>
                    <li style="margin-bottom: 8px;">
                        <a href="data_vendor.php" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 10px; color: #555; text-decoration: none; font-weight: 500;"><i class="fa-solid fa-truck-field"></i> Data Vendor</a>
                    </li>
                </ul>
            </div>

            <div>
                <a href="actions/logout.php" style="display: flex; align-items: center; gap: 10px; color: #555; text-decoration: none; font-weight: 500; padding: 10px;"><i class="fa-solid fa-right-from-bracket"></i> Keluar Sistem</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Topbar -->
            <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <div style="background: #fff; padding: 12px 20px; border-radius: 12px; width: 360px; display: flex; align-items: center; gap: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
                    <i class="fa-solid fa-magnifying-glass" style="color: #aaa;"></i>
                    <input type="text" placeholder="Cari Serial Number atau Nama Barang..." style="border: none; outline: none; width: 100%; background: transparent;">
                </div>
                <div style="display: flex; align-items: center; gap: 15px; background: #fff; padding: 8px 16px; border-radius: 12px;">
                    <i class="fa-regular fa-bell" style="color: #666; font-size: 18px;"></i>
                    <div style="text-align: right;">
                        <h4 style="margin: 0; font-size: 14px;"><?= htmlspecialchars($_SESSION["kg_namaAdmin"] ?? 'Admin Utama'); ?></h4>
                        <p style="margin: 0; font-size: 11px; color: #888;">Super Admin</p>
                    </div>
                    <div style="width: 36px; height: 36px; background: #eee; border-radius: 50%; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-user" style="color: #666;"></i></div>
                </div>
            </header>

            <!-- Table Card -->
            <section style="background: #fff; border-radius: 16px; padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); flex: 1;">
                <div style="margin-bottom: 24px;">
                    <h2 style="margin: 0; font-size: 20px; color: #222;">Daftar Data Barang</h2>
                    <p style="margin: 4px 0 0 0; color: #888; font-size: 13px;">Manajemen seluruh produk dan stok barang.</p>
                </div>

                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 2px solid #f4f6f9; color: #999; font-size: 12px; letter-spacing: 0.5px;">
                            <th style="padding: 14px 12px;">SERIAL NUMBER</th>
                            <th style="padding: 14px 12px;">NAMA BARANG</th>
                            <th style="padding: 14px 12px;">KATEGORI</th>
                            <th style="padding: 14px 12px;">KUANTITAS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr style="border-bottom: 1px solid #f8f9fa;">
                                    <td style="padding: 16px 12px; font-weight: 600; color: #555;"><?= htmlspecialchars($row['serial_number'] ?? '-'); ?></td>
                                    <td style="padding: 16px 12px; font-weight: 600; color: #222;"><?= htmlspecialchars($row['nama_barang'] ?? $row['nama'] ?? '-'); ?></td>
                                    <td style="padding: 16px 12px;"><span style="background: #f4f6f9; padding: 4px 10px; border-radius: 6px; font-size: 12px; color: #666; font-weight: 500;"><?= htmlspecialchars($row['kategori'] ?? 'Umum'); ?></span></td>
                                    <td style="padding: 16px 12px; font-weight: 700; color: <?= ($row['kuantitas'] ?? 0) > 0 ? '#2e7d32' : '#d32f2f'; ?>;"><?= htmlspecialchars($row['kuantitas'] ?? $row['stok'] ?? 0); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 40px; color: #999;">Belum ada data barang tersedia.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>