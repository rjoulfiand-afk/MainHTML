<?php
session_start();
include "config/koneksi.php";

// Proteksi Halaman Login
if (!isset($_SESSION["kg_isLoggedIn"]) || $_SESSION["kg_isLoggedIn"] !== true) {
    header("Location: index.html?error=unauthorized");
    exit;
}

// Mengambil data dari tabel vendor (menyesuaikan struktur database umum)
$query = "SELECT * FROM vendor ORDER BY id_vendor ASC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Vendor - KelolaStok</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Memanggil CSS sesuai struktur folder -->
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

    <!-- SIDEBAR -->
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
                <!-- Menu Data Vendor Aktif -->
                <li class="active"><a href="data_vendor.php"><i class="fa-solid fa-truck-field"></i> Data Vendor</a></li>
            </ul>
        </div>
        
        <!-- Footer Sidebar (Keluar Sistem di bawah) -->
        <div class="sidebar-footer">
            <a href="actions/logout.php" class="btn-logout">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar Sistem
            </a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <!-- TOPBAR -->
        <header class="topbar">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <!-- Input pencarian diberi id 'searchInput' -->
                <input type="text" id="searchInput" placeholder="Cari nama vendor atau kontak...">
            </div>
            <div class="topbar-right">
                <div class="notification">
                    <i class="fa-regular fa-bell"></i>
                    <span class="dot"></span>
                </div>
                <div class="profile-area">
                    <div class="profile-info">
                        <h4><?= htmlspecialchars($_SESSION["kg_namaAdmin"] ?? 'Super Admin'); ?></h4>
                        <p>Super Admin</p>
                    </div>
                    <div class="profile-pic">
                        <i class="fa-solid fa-user"></i>
                    </div>
                </div>
            </div>
        </header>

        <!-- TABLE SECTION -->
        <section class="table-section">
            <div class="table-header">
                <div>
                    <h3>Manajemen Data Vendor</h3>
                    <p>Kontrol penuh (CRUD) dan daftar vendor/supplier barang.</p>
                </div>
                <!-- Tombol Tambah Vendor memicu modal -->
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

    <!-- MODAL POP-UP TAMBAH VENDOR -->
    <div class="modal-overlay" id="modalTambah">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Tambah Vendor Baru</h3>
                <button class="btn-close" id="btnCloseModal"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <!-- Form mengarah ke folder actions/tambah_vendor.php sesuai struktur foldermu -->
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

    <!-- SCRIPT INTERAKTIF (PENCARIAN & MODAL) -->
    <script>
        // 1. Logika Modal Pop-up Tambah Vendor
        const modalTambah = document.getElementById('modalTambah');
        const btnTambahVendor = document.getElementById('btnTambahVendor');
        const btnCloseModal = document.getElementById('btnCloseModal');
        const btnCancelModal = document.getElementById('btnCancelModal');

        btnTambahVendor.addEventListener('click', () => {
            modalTambah.classList.add('show');
        });

        const tutupModal = () => {
            modalTambah.classList.remove('show');
        };

        btnCloseModal.addEventListener('click', tutupModal);
        btnCancelModal.addEventListener('click', tutupModal);
        
        window.addEventListener('click', (e) => {
            if (e.target === modalTambah) {
                tutupModal();
            }
        });

        // 2. Logika Live Search Tabel Vendor
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