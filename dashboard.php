<?php

session_start();

if (
    !isset($_SESSION["kg_isLoggedIn"]) ||
    $_SESSION["kg_isLoggedIn"] !== true
) {
    header("Location: index.html");
    exit;
}

include "config/koneksi.php";

$namaAdmin = $_SESSION["kg_namaAdmin"] ?? "Admin";

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
            <li class="active"><a href="#"><i class="fa-solid fa-border-all"></i> Dashboard Utama</a></li>
           <a href="data_barang.php"><i class="fa-solid fa-box-archive"></i> Data Barang</a>
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
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="searchInput" placeholder="Cari Serial Number atau Nama Barang...">
            </div>
            
            <div class="topbar-right">
                <div class="notification">
                    <i class="fa-regular fa-bell"></i>
                    <span class="dot"></span>
                </div>
                <div class="profile-area">
                    <div class="profile-info">
                        <h4><?php echo htmlspecialchars($namaAdmin); ?></h4>
                        <p>Super Admin</p>
                    </div>
                    <div class="profile-pic">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                </div>
            </div>
        </header>

<?php
        // JURUS NGAMBIL TOTAL ANGKA DARI DATABASE
        $q_stok = mysqli_query($koneksi, "SELECT SUM(kuantitas_stok) AS total_stok FROM inventory");
        $tot_stok = mysqli_fetch_assoc($q_stok)['total_stok'] ?? 0;

        $q_gudang = mysqli_query($koneksi, "SELECT COUNT(*) AS total_gudang FROM storage_unit");
        $tot_gudang = mysqli_fetch_assoc($q_gudang)['total_gudang'] ?? 0;

        $q_vendor = mysqli_query($koneksi, "SELECT COUNT(*) AS total_vendor FROM vendor");
        $tot_vendor = mysqli_fetch_assoc($q_vendor)['total_vendor'] ?? 0;

        $q_restock = mysqli_query($koneksi, "SELECT COUNT(*) AS total_habis FROM inventory WHERE kuantitas_stok = 0");
        $tot_restock = mysqli_fetch_assoc($q_restock)['total_habis'] ?? 0;
        ?>

        <!-- KARTU STATISTIK YANG UDAH HIDUP -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon safe-bg"><i class="fa-solid fa-cubes"></i></div>
                <div class="stat-info">
                    <p>Total Stok Barang</p>
                    <h3><?php echo $tot_stok; ?> <span>Unit</span></h3>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon primary-bg"><i class="fa-solid fa-building"></i></div>
                <div class="stat-info">
                    <p>Gudang Aktif</p>
                    <h3><?php echo $tot_gudang; ?> <span>Cabang</span></h3>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon primary-bg"><i class="fa-solid fa-handshake"></i></div>
                <div class="stat-info">
                    <p>Total Vendor</p>
                    <h3><?php echo $tot_vendor; ?> <span>Mitra</span></h3>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon danger-bg"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div class="stat-info">
                    <p>Perlu Restock</p>
                    <h3 class="text-danger"><?php echo $tot_restock; ?> <span>Barang</span></h3>
                </div>
            </div>
        </div>

<section class="table-section">
            
            <!-- BAGIAN HEADER (JUDUL & TOMBOL) -->
            <div class="table-header">
                <div>
                    <h3>Pergerakan Inventory</h3>
                    <p>Update terakhir stok barang masuk dan keluar.</p>
                </div>
                
                <!-- BUNGKUS DUA TOMBOL -->
<div style="display: flex; gap: 12px;">
                    <button class="btn-add" id="btnTambahGudang" style="background-color: #34495e;">
                        <i class="fa-solid fa-warehouse"></i> Tambah Gudang
                    </button>
                    <!-- Ini Tombol Tambah Vendor Baru -->
                    <button class="btn-add" id="btnTambahVendor" style="background-color: #27ae60;">
                        <i class="fa-solid fa-truck"></i> Tambah Vendor
                    </button>
                    <button class="btn-add" id="btnTambahBarang">
                        <i class="fa-solid fa-plus"></i> Tambah Barang
                    </button>
                </div>
            </div> <!-- NAH! INI TAG PENUTUP YANG HILANG TADI CUY -->
            
            <div class="table-responsive">
                <table id="inventoryTable">
                    <thead>
                        <tr>
                            <th>Serial Number</th>
                            <th>Nama Barang</th>
                            <th>Jenis Barang</th>
                            <th>Kuantitas</th>
                            <th>Lokasi Gudang</th>
                            <th>Vendor</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // JURUS SQL DITAMBAH AMBIL 'id_barang'
                        $query_inventory = mysqli_query($koneksi, "
                            SELECT 
                                inventory.id_barang,
                                inventory.serial_number, 
                                inventory.nama_barang, 
                                inventory.jenis_barang, 
                                inventory.kuantitas_stok, 
                                inventory.harga,
                                inventory.id_gudang,
                                inventory.id_vendor,
                                storage_unit.nama_gudang, 
                                vendor.nama_vendor 
                            FROM inventory 
                            JOIN storage_unit ON inventory.id_gudang = storage_unit.id_gudang 
                            JOIN vendor ON inventory.id_vendor = vendor.id_vendor
                        ");
                        
                        $barang_habis = [];

                        while($data = mysqli_fetch_assoc($query_inventory)) {
                            
                            $stok = $data['kuantitas_stok'];
                            $class_baris = ($stok == 0) ? "row-danger" : "";
                            $badge_warna = ($stok == 0) ? "danger" : (($stok <= 5) ? "warning" : "safe");

                            if ($stok == 0) {
                                $barang_habis[] = $data['nama_barang'];
                            }
                            $id_brg = $data['id_barang'];
                            $sn = $data['serial_number'];
                            $nama = $data['nama_barang'];
                            $jenis = $data['jenis_barang'];
                            $harga = $data['harga'];
                            $id_g = $data['id_gudang'];
                            $id_v = $data['id_vendor'];

                            echo "<tr class='$class_baris'>
                                <td class='font-mono'>" . htmlspecialchars($sn) . "</td>
                                <td class='fw-600'>" . htmlspecialchars($nama) . "</td>
                                <td><span class='tag'>" . htmlspecialchars($jenis) . "</span></td>
                                <td class='stok-angka'><span class='badge $badge_warna'>$stok</span></td>
                                <td>" . htmlspecialchars($data['nama_gudang']) . "</td>
                                <td>" . htmlspecialchars($data['nama_vendor']) . "</td>
                                <td class='action-cell'>
                                    <!-- Tombol Edit yang ngirim data ke JS -->
                                    <button class='btn-icon edit' title='Edit' onclick='bukaModalEdit($id_brg, \"$sn\", \"$nama\", \"$jenis\", $stok, $harga, $id_g, $id_v)'><i class='fa-solid fa-pen'></i></button>
                                    
                                    <!-- Tombol Hapus dengan pop-up konfirmasi -->
                                    <a href='actions/hapus_barang.php?id=$id_brg' class='btn-icon delete' title='Hapus' onclick='return confirm(\"Yakin mau hapus barang $nama cuy?\")' style='text-decoration: none; display: flex;'><i class='fa-solid fa-trash'></i></a>
                                </td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
</main>

    <?php
    // FITUR ALERT STOK HABIS (Sesuai Permintaan Guru)
    if (!empty($barang_habis)) {
        // Gabungin nama-nama barang jadi satu kalimat
        $daftar_habis = implode(", ", $barang_habis);
        echo "<script>
            window.addEventListener('DOMContentLoaded', (event) => {
                alert('⚠️ PERINGATAN SISTEM! ⚠️\\n\\nStok untuk barang berikut telah habis:\\n- $daftar_habis\\n\\nMohon segera hubungi vendor terkait untuk restock.');
            });
        </script>";
    }
    ?>

    <div id="modalTambah" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Tambah Barang Baru</h3>
                <button class="btn-close" id="btnCloseModal"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            <!-- Form terintegrasi PHP -->
            <form action="actions/tambah_barang.php" method="POST">
                <div class="input-group">
                    <label>Serial Number</label>
                    <input type="text" name="serial_number" required placeholder="Contoh: SN-003">
                </div>
                <div class="input-group">
                    <label>Nama Barang</label>
                    <input type="text" name="nama_barang" required placeholder="Masukkan nama barang">
                </div>
                <div class="input-group">
                    <label>Jenis Barang</label>
                    <input type="text" name="jenis_barang" required placeholder="Elektronik, Hardware, dll">
                </div>
                <div class="input-group">
                    <label>Kuantitas / Stok</label>
                    <input type="number" name="stok" min="0" required placeholder="0">
                </div>
                <div class="input-group">
                    <label>Harga Barang (Rp)</label>
                    <input type="number" name="harga" min="0" required placeholder="Contoh: 1500000">
                </div>
                
                <div class="input-group">
                    <label>Lokasi Gudang</label>
                    <select name="id_gudang" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                        <option value="" disabled selected>-- Pilih Gudang --</option>
                        <?php
                        // Ambil pilihan gudang langsung dari database
                        $q_gudang = mysqli_query($koneksi, "SELECT * FROM storage_unit");
                        while($gudang = mysqli_fetch_assoc($q_gudang)) {
                            echo "<option value='" . $gudang['id_gudang'] . "'>" . $gudang['nama_gudang'] . " (" . $gudang['lokasi'] . ")</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="input-group">
                    <label>Vendor / Supplier</label>
                    <select name="id_vendor" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                        <option value="" disabled selected>-- Pilih Vendor --</option>
                        <?php
                        // Ambil pilihan vendor langsung dari database
                        $q_vendor = mysqli_query($koneksi, "SELECT * FROM vendor");
                        while($vendor = mysqli_fetch_assoc($q_vendor)) {
                            echo "<option value='" . $vendor['id_vendor'] . "'>" . $vendor['nama_vendor'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" id="btnCancelModal">Batal</button>
                    <button type="submit" class="btn-save">Simpan ke Database</button>
                </div>
            </form>
        </div>
    </div>
    <!-- MODAL POP-UP TAMBAH GUDANG -->
    <div id="modalTambahGudang" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Tambah Cabang Gudang Baru</h3>
                <button class="btn-close" id="btnCloseGudang"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            <form action="actions/tambah_gudang.php" method="POST">
                <div class="input-group">
                    <label>Nama Gudang</label>
                    <input type="text" name="nama_gudang" required placeholder="Contoh: Gudang Sidoarjo">
                </div>
                <div class="input-group">
                    <label>Lokasi / Alamat</label>
                    <input type="text" name="lokasi" required placeholder="Contoh: Jl. Pahlawan No.10">
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" id="btnCancelGudang">Batal</button>
                    <button type="submit" class="btn-save">Simpan Gudang</button>
                </div>
            </form>
        </div>
    </div>
    <!-- MODAL POP-UP TAMBAH VENDOR -->
    <div id="modalTambahVendor" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Tambah Vendor Baru</h3>
                <button class="btn-close" id="btnCloseVendor"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            <form action="actions/tambah_vendor.php" method="POST">
                <div class="input-group">
                    <label>Nama Perusahaan Vendor</label>
                    <input type="text" name="nama_vendor" required placeholder="Contoh: PT Elektronik Maju">
                </div>
                <div class="input-group">
                    <label>Kontak / No. Telepon</label>
                    <input type="text" name="kontak" required placeholder="Contoh: 0812-3456-7890">
                </div>
                <div class="input-group">
                    <label>Barang yang Disediakan</label>
                    <input type="text" name="nama_barang_supply" required placeholder="Contoh: Kabel, Monitor, dll">
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" id="btnCancelVendor">Batal</button>
                    <button type="submit" class="btn-save">Simpan Vendor</button>
                </div>
            </form>
        </div>
    </div>
    <!-- MODAL POP-UP EDIT BARANG -->
    <div id="modalEdit" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Edit Data Barang</h3>
                <button class="btn-close" id="btnCloseEdit"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            <form action="actions/sedit_barang.php" method="POST">
                <!-- Input rahasia (hidden) buat nyimpen ID barang -->
                <input type="hidden" name="id_barang" id="edit_id_barang">
                
                <div class="input-group">
                    <label>Serial Number</label>
                    <input type="text" name="serial_number" id="edit_serial" required>
                </div>
                <div class="input-group">
                    <label>Nama Barang</label>
                    <input type="text" name="nama_barang" id="edit_nama" required>
                </div>
                <div class="input-group">
                    <label>Jenis Barang</label>
                    <input type="text" name="jenis_barang" id="edit_jenis" required>
                </div>
                <div class="input-group">
                    <label>Kuantitas / Stok</label>
                    <input type="number" name="stok" id="edit_stok" min="0" required>
                </div>
                <div class="input-group">
                    <label>Harga Barang (Rp)</label>
                    <input type="number" name="harga" id="edit_harga" min="0" required>
                </div>
                
                <div class="input-group">
                    <label>Lokasi Gudang</label>
                    <select name="id_gudang" id="edit_gudang" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                        <?php
                        $q_gudang = mysqli_query($koneksi, "SELECT * FROM storage_unit");
                        while($gudang = mysqli_fetch_assoc($q_gudang)) {
                            echo "<option value='" . $gudang['id_gudang'] . "'>" . $gudang['nama_gudang'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="input-group">
                    <label>Vendor / Supplier</label>
                    <select name="id_vendor" id="edit_vendor" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                        <?php
                        $q_vendor = mysqli_query($koneksi, "SELECT * FROM vendor");
                        while($vendor = mysqli_fetch_assoc($q_vendor)) {
                            echo "<option value='" . $vendor['id_vendor'] . "'>" . $vendor['nama_vendor'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" id="btnCancelEdit">Batal</button>
                    <button type="submit" class="btn-save">Update Data</button>
                </div>
            </form>
        </div>
    </div>
    <script src="js/script.js"></script>
</body>
</html>