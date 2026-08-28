<?php
session_start();
include "config/koneksi.php";

if (!isset($_SESSION["kg_isLoggedIn"]) || $_SESSION["kg_isLoggedIn"] !== true) {
    header("Location: index.html?error=unauthorized");
    exit;
}

$query = "SELECT * FROM storage_unit ORDER BY id_gudang ASC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lokasi Gudang - KelolaStok</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   
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
                <li class="active"><a href="lokasi_gudang.php"><i class="fa-solid fa-warehouse"></i> Lokasi Gudang</a></li>
                <li><a href="data_vendor.php"><i class="fa-solid fa-truck-field"></i> Data Vendor</a></li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <a href="actions/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Keluar Sistem</a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <!-- TOPBAR -->
        <header class="topbar">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <!-- Input pencarian diberi id 'searchInput' -->
                <input type="text" id="searchInput" placeholder="Cari nama gudang atau lokasi...">
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

        <section class="table-section">
            <div class="table-header">
                <div>
                    <h3>Manajemen Lokasi Gudang</h3>
                    <p>Kontrol penuh (CRUD) dan detail lokasi cabang penyimpanan.</p>
                </div>
                <button class="btn-add" id="btnTambahGudang">
                    <i class="fa-solid fa-plus"></i> Tambah Gudang
                </button>
            </div>

            <table id="tabelGudang">
                <thead>
                    <tr>
                        <th>KODE GUDANG</th>
                        <th>NAMA GUDANG</th>
                        <th>ALAMAT LOKASI</th>
                        <th class="text-center">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><span class="font-mono">GDG-00<?= htmlspecialchars($row['id_gudang']); ?></span></td>
                                <td class="fw-600"><?= htmlspecialchars($row['nama_gudang']); ?></td>
                                <td><?= htmlspecialchars($row['lokasi']); ?></td>
                                <td class="text-center">
                                    <span class="badge safe">Aktif</span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr id="emptyRow">
                            <td colspan="4" class="text-center" style="padding: 40px; color: #a098ae;">Belum ada data lokasi gudang tersedia.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>

    <div class="modal-overlay" id="modalTambah">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Tambah Lokasi Gudang Baru</h3>
                <button class="btn-close" id="btnCloseModal"><i class="fa-solid fa-xmark"></i></button>
            </div>
         
            <form action="actions/tambah_gudang.php" method="POST">
                <div class="input-group">
                    <label for="nama_gudang">Nama Gudang</label>
                    <input type="text" id="nama_gudang" name="nama_gudang" required placeholder="Contoh: Gudang Cabang Timur">
                </div>
                <div class="input-group">
                    <label for="lokasi">Alamat Lokasi</label>
                    <input type="text" id="lokasi" name="lokasi" required placeholder="Contoh: Jl. Raya Industri No. 45">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" id="btnCancelModal">Batal</button>
                    <button type="submit" class="btn-save">Simpan Gudang</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modalTambah = document.getElementById('modalTambah');
        const btnTambahGudang = document.getElementById('btnTambahGudang');
        const btnCloseModal = document.getElementById('btnCloseModal');
        const btnCancelModal = document.getElementById('btnCancelModal');

        btnTambahGudang.addEventListener('click', () => {
            modalTambah.classList.add('show');
        });

        const tutupModal = () => {
            modalTambah.classList.remove('show');
        };

        btnCloseModal.addEventListener('click', tutupModal);
        btnCancelModal.addEventListener('click', tutupModal);
        
        // Tutup modal jika klik di luar box modal
        window.addEventListener('click', (e) => {
            if (e.target === modalTambah) {
                tutupModal();
            }
        });

        //Logika Live 
        const searchInput = document.getElementById('searchInput');
        const tabelGudang = document.getElementById('tabelGudang');
        const trs = tabelGudang.getElementsByTagName('tr');

        searchInput.addEventListener('keyup', function() {
            const filter = searchInput.value.toLowerCase();
            
            for (let i = 1; i < trs.length; i++) {
                const tr = trs[i];
                if (tr.id === 'emptyRow') continue;
                
                const tdNama = tr.getElementsByTagName('td')[1];
                const tdLokasi = tr.getElementsByTagName('td')[2];
                const tdKode = tr.getElementsByTagName('td')[0];

                if (tdNama || tdLokasi || tdKode) {
                    const textNama = tdNama.textContent || tdNama.innerText;
                    const textLokasi = tdLokasi.textContent || tdLokasi.innerText;
                    const textKode = tdKode.textContent || tdKode.innerText;

                    if (textNama.toLowerCase().indexOf(filter) > -1 || 
                        textLokasi.toLowerCase().indexOf(filter) > -1 || 
                        textKode.toLowerCase().indexOf(filter) > -1) {
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