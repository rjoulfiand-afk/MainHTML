<?php
session_start();
include "config/koneksi.php";

if (!isset($_SESSION["kg_isLoggedIn"]) || $_SESSION["kg_isLoggedIn"] !== true) {
    header("Location: index.html?error=unauthorized");
    exit;
}
$namaAdmin = $_SESSION["kg_namaAdmin"] ?? "Admin Utama";
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
            <li><a href="dashboard.php"><i class="fa-solid fa-border-all"></i> Dashboard Utama</a></li>
            <li class="active"><a href="data_barang.php"><i class="fa-solid fa-box-archive"></i> Data Barang</a></li>
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
                <input type="text" id="pencarianDetail" placeholder="Cari Serial Number atau Nama...">
            </div>
            
            <div class="topbar-right">
                <div class="profile-area">
                    <div class="profile-info">
                        <h4><?php echo htmlspecialchars($namaAdmin); ?></h4>
                        <p>Super Admin</p>
                    </div>
                    <div class="profile-pic"><i class="fa-solid fa-user-tie"></i></div>
                </div>
            </div>
        </header>

        <section class="table-section">
            <div class="table-header">
                <div>
                    <h3>Manajemen Data Barang</h3>
                    <p>Kontrol penuh (CRUD) dan detail rinci produk.</p>
                </div>
                <div style="display: flex; gap: 12px; align-items: center;">
                    <!-- Filter A-Z Sederhana -->
                    <select id="filterSort" style="padding: 10px; border-radius: 8px; border: 1px solid #ddd; outline: none;">
                        <option value="default">Urutkan: Default</option>
                        <option value="az">Nama: A - Z</option>
                        <option value="za">Nama: Z - A</option>
                    </select>

                    <button class="btn-add" id="btnTambahDataBarang">
                        <i class="fa-solid fa-plus"></i> Tambah Barang
                    </button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table id="tabelDataBarang">
                    <thead>
                        <tr>
                            <th>Serial Number</th>
                            <th>Nama Barang</th>
                            <th>Jenis</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Gudang</th>
                            <th>Vendor</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT inventory.*, storage_unit.nama_gudang, vendor.nama_vendor 
                                    FROM inventory 
                                    LEFT JOIN storage_unit ON inventory.id_gudang = storage_unit.id_gudang 
                                    LEFT JOIN vendor ON inventory.id_vendor = vendor.id_vendor 
                                    WHERE inventory.status_hapus = 0"; 
                        $result = mysqli_query($koneksi, $query);

                        while($row = mysqli_fetch_assoc($result)) {
                            $stok = $row['kuantitas_stok'];
                            $hargaRp = "Rp " . number_format($row['harga'], 0, ',', '.');
                            $id_brg = $row['id_barang'];
                            $sn = $row['serial_number'];
                            $nama = $row['nama_barang'];
                            $jenis = $row['jenis_barang'];
                            $harga = $row['harga'];
                            $id_g = $row['id_gudang'] ?? '';
                            $id_v = $row['id_vendor'] ?? '';

                            echo "<tr>
                                <td class='font-mono'>" . htmlspecialchars($sn) . "</td>
                                <td class='fw-600 nama-item'>" . htmlspecialchars($nama) . "</td>
                                <td><span class='tag'>" . htmlspecialchars($jenis) . "</span></td>
                                <td>$hargaRp</td>
                                <td><span class='badge'>" . $stok . "</span></td>
                                <td>" . htmlspecialchars($row['nama_gudang'] ?? '-') . "</td>
                                <td>" . htmlspecialchars($row['nama_vendor'] ?? '-') . "</td>
                                <td class='action-cell'>
                                    <button class='btn-icon edit' onclick='bukaEdit($id_brg, \"$sn\", \"$nama\", \"$jenis\", $stok, $harga, \"$id_g\", \"$id_v\")'><i class='fa-solid fa-pen'></i></button>
                                    <a href='actions/hapus_barang.php?id=$id_brg' class='btn-icon delete' onclick='return confirm(\"Yakin hapus $nama?\")'><i class='fa-solid fa-trash'></i></a>
                                </td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- MODAL TAMBAH BARANG -->
    <div id="modalTambah" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Tambah Barang Baru</h3>
                <button class="btn-close" id="btnCloseAdd"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="actions/tambah_barang.php" method="POST">
                <div class="input-group"><label>Serial Number</label><input type="text" name="serial_number" required></div>
                <div class="input-group"><label>Nama Barang</label><input type="text" name="nama_barang" required></div>
                <div class="input-group"><label>Jenis Barang</label><input type="text" name="jenis_barang" required></div>
                <div class="input-group"><label>Stok</label><input type="number" name="stok" min="0" required></div>
                <div class="input-group"><label>Harga (Rp)</label><input type="number" name="harga" min="0" required></div>
                <div class="input-group">
                    <label>Gudang</label>
                    <select name="id_gudang" required style="width:100%; padding:8px;">
                        <?php
                        $q_gudang = mysqli_query($koneksi, "SELECT * FROM storage_unit");
                        while($g = mysqli_fetch_assoc($q_gudang)) echo "<option value='{$g['id_gudang']}'>{$g['nama_gudang']}</option>";
                        ?>
                    </select>
                </div>
                <div class="input-group">
                    <label>Vendor</label>
                    <select name="id_vendor" required style="width:100%; padding:8px;">
                        <?php
                        $q_vendor = mysqli_query($koneksi, "SELECT * FROM vendor");
                        while($v = mysqli_fetch_assoc($q_vendor)) echo "<option value='{$v['id_vendor']}'>{$v['nama_vendor']}</option>";
                        ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" id="btnCancelAdd">Batal</button>
                    <button type="submit" class="btn-save">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT BARANG -->
    <div id="modalEdit" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Edit Data Barang</h3>
                <button class="btn-close" id="btnCloseEdit"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="actions/edit_barang.php" method="POST">
                <input type="hidden" name="id_barang" id="edit_id">
                <div class="input-group"><label>Serial Number</label><input type="text" name="serial_number" id="edit_sn" required></div>
                <div class="input-group"><label>Nama Barang</label><input type="text" name="nama_barang" id="edit_nama" required></div>
                <div class="input-group"><label>Jenis Barang</label><input type="text" name="jenis_barang" id="edit_jenis" required></div>
                <div class="input-group"><label>Stok</label><input type="number" name="stok" id="edit_stok" min="0" required></div>
                <div class="input-group"><label>Harga (Rp)</label><input type="number" name="harga" id="edit_harga" min="0" required></div>
                <div class="input-group">
                    <label>Gudang</label>
                    <select name="id_gudang" id="edit_gudang" required style="width:100%; padding:8px;">
                        <?php
                        $q_gudang = mysqli_query($koneksi, "SELECT * FROM storage_unit");
                        while($g = mysqli_fetch_assoc($q_gudang)) echo "<option value='{$g['id_gudang']}'>{$g['nama_gudang']}</option>";
                        ?>
                    </select>
                </div>
                <div class="input-group">
                    <label>Vendor</label>
                    <select name="id_vendor" id="edit_vendor" required style="width:100%; padding:8px;">
                        <?php
                        $q_vendor = mysqli_query($koneksi, "SELECT * FROM vendor");
                        while($v = mysqli_fetch_assoc($q_vendor)) echo "<option value='{$v['id_vendor']}'>{$v['nama_vendor']}</option>";
                        ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" id="btnCancelEditBtn">Batal</button>
                    <button type="submit" class="btn-save">Update Data</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // FITUR SEARCH BARANG
        const searchInput = document.getElementById("pencarianDetail");
        const tbody = document.querySelector("#tabelDataBarang tbody");
        
        searchInput.addEventListener("keyup", function() {
            let filter = this.value.toLowerCase();
            let rows = tbody.querySelectorAll("tr");
            rows.forEach(row => {
                let text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? "" : "none";
            });
        });

        // FITUR SORTING A-Z
        const filterSort = document.getElementById("filterSort");
        filterSort.addEventListener("change", function() {
            let rows = Array.from(tbody.querySelectorAll("tr"));
            let sortType = this.value;

            if(sortType === 'az' || sortType === 'za') {
                rows.sort((a, b) => {
                    let nameA = a.querySelector(".nama-item").textContent.toLowerCase();
                    let nameB = b.querySelector(".nama-item").textContent.toLowerCase();
                    if(sortType === 'az') return nameA.localeCompare(nameB);
                    return nameB.localeCompare(nameA);
                });
                rows.forEach(row => tbody.appendChild(row)); // Re-append dengan urutan baru
            } else {
                window.location.reload(); // Balik ke urutan database default
            }
        });

        // LOGIKA MODAL POP-UP TAMBAH & EDIT (Tanpa harus otak-atik script.js)
        const modalAdd = document.getElementById("modalTambah");
        const modalEdit = document.getElementById("modalEdit");

        document.getElementById("btnTambahDataBarang").onclick = () => modalAdd.classList.add("show");
        document.getElementById("btnCloseAdd").onclick = () => modalAdd.classList.remove("show");
        document.getElementById("btnCancelAdd").onclick = () => modalAdd.classList.remove("show");

        function bukaEdit(id, sn, nama, jenis, stok, harga, gudang, vendor) {
            document.getElementById("edit_id").value = id;
            document.getElementById("edit_sn").value = sn;
            document.getElementById("edit_nama").value = nama;
            document.getElementById("edit_jenis").value = jenis;
            document.getElementById("edit_stok").value = stok;
            document.getElementById("edit_harga").value = harga;
            document.getElementById("edit_gudang").value = gudang;
            document.getElementById("edit_vendor").value = vendor;
            modalEdit.classList.add("show");
        }
        document.getElementById("btnCloseEdit").onclick = () => modalEdit.classList.remove("show");
        document.getElementById("btnCancelEditBtn").onclick = () => modalEdit.classList.remove("show");
    </script>
</body>
</html>