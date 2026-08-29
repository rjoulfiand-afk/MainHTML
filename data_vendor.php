<?php
session_start();
include "config/koneksi.php";

if (!isset($_SESSION["kg_isLoggedIn"]) || $_SESSION["kg_isLoggedIn"] !== true) {
    header("Location: index.html?error=unauthorized");
    exit;
}

$query = "SELECT * FROM vendor ORDER BY id_vendor ASC";
$result = mysqli_query($koneksi, $query);

// AMBIL DATA BARANG HABIS BUAT LONCENG NOTIF
$barang_habis = [];
$q_habis = mysqli_query($koneksi, "SELECT nama_barang FROM inventory WHERE kuantitas_stok = 0 AND status_hapus = 0");
if($q_habis) {
    while($b = mysqli_fetch_assoc($q_habis)) {
        $barang_habis[] = $b['nama_barang'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Vendor - KelolaStok</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

    <aside class="sidebar">
        <div>
            <div class="sidebar-header">
                <div class="logo-box">KG</div>
                <div class="logo-text">
                    <h2>KelolaStok</h2>
                    <p>Kelompok Ganteng</p>
                </div>
            </div>

            <div class="menu-label">MENU UTAMA</div>
            <ul class="nav-links">
                <li><a href="dashboard.php"><i class="fa-solid fa-border-all"></i> Dashboard Utama</a></li>
                <li><a href="data_barang.php"><i class="fa-solid fa-box-archive"></i> Data Barang</a></li>
                <li><a href="lokasi_gudang.php"><i class="fa-solid fa-warehouse"></i> Lokasi Gudang</a></li>
                <li class="active"><a href="data_vendor.php"><i class="fa-solid fa-truck-field"></i> Data Vendor</a></li>
            </ul>
        </div>
        
        <div class="sidebar-footer">
            <a href="actions/logout.php" class="btn-logout">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar Sistem
            </a>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="searchInput" placeholder="Cari nama vendor atau kontak...">
            </div>
            
            <div class="topbar-right">
                <!-- FITUR LONCENG NOTIFIKASI -->
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

                <!-- FITUR PROFIL DROPDOWN -->
                <div class="profile-area" id="btnProfile" style="position: relative; cursor: pointer;">
                    <div class="profile-info">
                        <h4><?= htmlspecialchars($_SESSION["kg_namaAdmin"] ?? 'Super Admin'); ?></h4>
                        <p>Super Admin</p>
                    </div>
                    <div class="profile-pic"><i class="fa-solid fa-user-tie"></i></div>
                    
                    <div id="kotakProfile" style="display: none; position: absolute; right: 0; top: 50px; background: white; width: 200px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); border: 1px solid #ddd; z-index: 100; overflow: hidden;">
                        <a href="riwayat_barang.php" style="display: block; padding: 12px 15px; color: #333; text-decoration: none; border-bottom: 1px solid #eee; font-size: 14px;">
                            <i class="fa-solid fa-clock-rotate-left" style="margin-right: 8px;"></i> Riwayat Hapus
                        </a>
                        <a href="actions/logout.php" style="display: block; padding: 12px 15px; color: #d63031; text-decoration: none; font-size: 14px;">
                            <i class="fa-solid fa-arrow-right-from-bracket" style="margin-right: 8px;"></i> Keluar Sistem
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <section class="table-section">
            <div class="table-header">
                <div>
                    <h3>Manajemen Data Vendor</h3>
                </div>
                <button class="btn-add" id="btnTambahVendor">
                    <i class="fa-solid fa-plus"></i> Tambah Vendor
                </button>
            </div>

            <table id="tabelVendor">
                <thead>
                    <tr>
                        <th>ID VENDOR</th>
                        <th>NAMA VENDOR</th>
                        <th>KONTAK / TELEPON</th>
                        <th>ALAMAT</th>
                        <th class="text-center">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><span class="font-mono">VND-00<?= htmlspecialchars($row['id_vendor']); ?></span></td>
                                <td class="fw-600"><?= htmlspecialchars($row['nama_vendor']); ?></td>
                                <td><?= htmlspecialchars($row['telepon'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['alamat'] ?? '-'); ?></td>
                                <td class="text-center">
                                    <span class="badge safe">Aktif</span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr id="emptyRow">
                            <td colspan="5" class="text-center" style="padding: 40px; color: #a098ae;">Belum ada data vendor tersedia.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>

    <div class="modal-overlay" id="modalTambah">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Tambah Vendor Baru</h3>
                <button class="btn-close" id="btnCloseModal"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="actions/tambah_vendor.php" method="POST">
                <div class="input-group">
                    <label for="nama_vendor">Nama Vendor</label>
                    <input type="text" id="nama_vendor" name="nama_vendor" required placeholder="Contoh: PT Sumber Makmur">
                </div>
                <div class="input-group">
                    <label for="telepon">Kontak / Telepon</label>
                    <input type="text" id="telepon" name="telepon" required placeholder="Contoh: 081234567890">
                </div>
                <div class="input-group">
                    <label for="alamat">Alamat</label>
                    <input type="text" id="alamat" name="alamat" required placeholder="Contoh: Jl. Sudirman No. 10, Jakarta">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" id="btnCancelModal">Batal</button>
                    <button type="submit" class="btn-save">Simpan Vendor</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // SCRIPT PENGENDALI KLIK LONCENG & PROFIL
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

        const modalTambah = document.getElementById('modalTambah');
        const btnTambahVendor = document.getElementById('btnTambahVendor');
        const btnCloseModal = document.getElementById('btnCloseModal');
        const btnCancelModal = document.getElementById('btnCancelModal');

        btnTambahVendor.addEventListener('click', () => { modalTambah.classList.add('show'); });
        const tutupModal = () => { modalTambah.classList.remove('show'); };
        btnCloseModal.addEventListener('click', tutupModal);
        btnCancelModal.addEventListener('click', tutupModal);
        
        window.addEventListener('click', (e) => {
            if (e.target === modalTambah) { tutupModal(); }
        });

        // Logika Live Search Tabel
        const searchInput = document.getElementById('searchInput');
        const tabelVendor = document.getElementById('tabelVendor');
        const trs = tabelVendor.getElementsByTagName('tr');

        searchInput.addEventListener('keyup', function() {
            const filter = searchInput.value.toLowerCase();
            for (let i = 1; i < trs.length; i++) {
                const tr = trs[i];
                if (tr.id === 'emptyRow') continue;
                
                const tdId = tr.getElementsByTagName('td')[0];
                const tdNama = tr.getElementsByTagName('td')[1];
                const tdTelepon = tr.getElementsByTagName('td')[2];
                const tdAlamat = tr.getElementsByTagName('td')[3];

                if (tdNama || tdTelepon || tdAlamat || tdId) {
                    const textId = tdId.textContent || tdId.innerText;
                    const textNama = tdNama.textContent || tdNama.innerText;
                    const textTelepon = tdTelepon.textContent || tdTelepon.innerText;
                    const textAlamat = tdAlamat.textContent || tdAlamat.innerText;

                    if (textNama.toLowerCase().indexOf(filter) > -1 || 
                        textTelepon.toLowerCase().indexOf(filter) > -1 || 
                        textAlamat.toLowerCase().indexOf(filter) > -1 ||
                        textId.toLowerCase().indexOf(filter) > -1) {
                        tr.style.display = "";
                    } else {
                        tr.style.display = "none";
                    }
                }
            }
        });
    </script>
</body>
</html>