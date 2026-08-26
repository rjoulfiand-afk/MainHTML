/*CATATAN UNTUK FAUZAN
   Logika CRUD (Tambah / Edit / Hapus barang) BELUM ada di sini.
   Silakan lanjutkan dengan menambahkan:
   - Fungsi untuk menambah baris baru ke tabel #inventoryTable
   - Fungsi untuk mengedit data pada baris yang tombol .edit-nya diklik
   - Fungsi untuk menghapus baris saat tombol .delete diklik
   - (Opsional) simpan datanya ke localStorage atau backend
     supaya tidak hilang saat halaman di-refresh */

// ---- 1. PROTEKSI HALAMAN: HARUS LOGIN DULU ----
if (sessionStorage.getItem("kg_isLoggedIn") !== "true") {
    // Kalau belum login, tendang balik ke halaman utama
    window.location.href = "index.html";
}

// Tampilkan nama admin yang login di topbar (kalau ada elemennya)
window.addEventListener("DOMContentLoaded", () => {
    const namaAdmin = sessionStorage.getItem("kg_namaAdmin");
    const elemenNama = document.querySelector(".profile-info h4");
    if (namaAdmin && elemenNama) {
        elemenNama.textContent = namaAdmin;
    }
});

// ---- 2. AMBIL ELEMEN UTAMA ----
const tabelBody = document.querySelector("#inventoryTable tbody");
const inputSearch = document.querySelector("#searchInput");

<<<<<<< HEAD
// ---- 3. SEARCH / FILTER TABEL ----
// Filter langsung ke baris <tr> yang sudah ada di HTML,
// berdasarkan kolom Serial Number dan Nama Barang.
inputSearch.addEventListener("input", function () {
    const kataKunci = this.value.trim().toLowerCase();
    const semuaBaris = tabelBody.querySelectorAll("tr");

    semuaBaris.forEach((tr) => {
        const kolom = tr.querySelectorAll("td");
        if (kolom.length < 2) return; // lewati baris "tidak ada data" dsb.

        const serial = kolom[0].textContent.trim().toLowerCase();
        const nama = kolom[1].textContent.trim().toLowerCase();

        const cocok = serial.includes(kataKunci) || nama.includes(kataKunci);
        tr.style.display = cocok ? "" : "none";
    });
});

=======
// ---- 2. DATA BARANG (LocalStorage) ----
let daftarBarang = [];
let idBerikutnya = 1;
const KUNCI_LOCALSTORAGE = "kg_daftarBarang";

function simpanKeLocalStorage() {
    localStorage.setItem(KUNCI_LOCALSTORAGE, JSON.stringify(daftarBarang));
}

function muatDariLocalStorage() {
    const dataTersimpan = localStorage.getItem(KUNCI_LOCALSTORAGE);
    if (!dataTersimpan) return false;

    try {
        const hasilParse = JSON.parse(dataTersimpan);
        if (Array.isArray(hasilParse) && hasilParse.length > 0) {
            daftarBarang = hasilParse;
            idBerikutnya = Math.max(...daftarBarang.map((b) => b.id)) + 1;
            return true;
        }
    } catch (error) {
        console.error("[script.js] Gagal membaca data:", error);
    }
    return false;
}

function bacaDataAwalDariTabel() {
    const baris = tabelBody.querySelectorAll("tr");
    baris.forEach((tr) => {
        const kolom = tr.querySelectorAll("td");
        if (kolom.length < 6) return;

        const kuantitas = parseInt(kolom[3].querySelector(".badge").textContent.trim(), 10);
        daftarBarang.push({
            id: idBerikutnya++,
            serial: kolom[0].textContent.trim(),
            nama: kolom[1].textContent.trim(),
            jenis: kolom[2].textContent.trim(),
            kuantitas: kuantitas,
            lokasi: kolom[4].textContent.trim(),
            vendor: kolom[5].textContent.trim()
        });
    });
}

// ---- 3. FUNGSI BANTUAN ----
function kelasBadgeStok(jumlah) {
    if (jumlah === 0) return "danger";
    if (jumlah <= 5) return "warning";
    return "safe";
}

// ---- 4. RENDER TABEL & ALERT STOK HABIS ----
function renderTabel(data = daftarBarang) {
    tabelBody.innerHTML = "";

    if (data.length === 0) {
        tabelBody.innerHTML = `
            <tr>
                <td colspan="7" style="text-align:center; padding: 20px; color:#888;">
                    Tidak ada data barang yang cocok.
                </td>
            </tr>`;
        return;
    }

    let barangHabis = []; // Array untuk nampung barang yang stoknya 0

    data.forEach((barang) => {
        const tr = document.createElement("tr");
        tr.dataset.id = barang.id;
        
        // Logika warna baris kalau stok 0 (Fitur buatan lu)
        if(barang.kuantitas === 0) {
            tr.classList.add('row-danger');
            barangHabis.push(barang.nama);
        }

        tr.innerHTML = `
            <td class="font-mono">${barang.serial}</td>
            <td class="fw-600">${barang.nama}</td>
            <td><span class="tag">${barang.jenis}</span></td>
            <td class="stok-angka"><span class="badge ${kelasBadgeStok(barang.kuantitas)}">${barang.kuantitas}</span></td>
            <td>${barang.lokasi}</td>
            <td>${barang.vendor}</td>
            <td class="action-cell">
                <button class="btn-icon edit" title="Edit"><i class="fa-solid fa-pen"></i></button>
                <button class="btn-icon delete" title="Hapus"><i class="fa-solid fa-trash"></i></button>
            </td>
        `;
        tabelBody.appendChild(tr);
    });

    perbaruiStatistik();
    simpanKeLocalStorage();

    // Munculkan Alert kalau ada barang habis (Fitur buatan lu)
    if (barangHabis.length > 0 && !window.alertMute) {
        setTimeout(() => {
            alert("⚠️ PERINGATAN SISTEM: KEKURANGAN STOK!\n\nBarang berikut telah habis stoknya:\n- " + barangHabis.join("\n- ") + "\n\nMohon segera hubungi Vendor untuk restock!");
            window.alertMute = true; // Biar alert gak spam berkali-kali pas ngetik di search
        }, 500);
    }
}

// ---- 5. UPDATE STATISTIK ----
function perbaruiStatistik() {
    const totalStok = daftarBarang.reduce((total, b) => total + b.kuantitas, 0);
    const perluRestock = daftarBarang.filter((b) => b.kuantitas === 0).length;

    const statTotalStok = document.querySelector(".stat-card:nth-child(1) h3");
    const statRestock = document.querySelector(".stat-card:nth-child(4) h3");

    if (statTotalStok) statTotalStok.innerHTML = `${totalStok.toLocaleString("id-ID")} <span>Unit</span>`;
    if (statRestock) {
        statRestock.innerHTML = `${perluRestock} <span>Barang</span>`;
        statRestock.classList.toggle("text-danger", perluRestock > 0);
    }
}

// ---- 6. PENCARIAN (SEARCH) ----
inputSearch.addEventListener("input", function () {
    const kataKunci = this.value.trim().toLowerCase();
    const hasilFilter = daftarBarang.filter((barang) =>
        barang.serial.toLowerCase().includes(kataKunci) ||
        barang.nama.toLowerCase().includes(kataKunci)
    );
    // Matikan pop-up alert saat lagi searching biar gak mengganggu
    window.alertMute = true; 
    
    tabelBody.innerHTML = "";
    if (hasilFilter.length === 0) {
        tabelBody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding: 20px; color:#888;">Tidak ada data barang yang cocok.</td></tr>`;
        return;
    }
    
    hasilFilter.forEach((barang) => {
        const tr = document.createElement("tr");
        tr.dataset.id = barang.id;
        if(barang.kuantitas === 0) tr.classList.add('row-danger');
        
        tr.innerHTML = `
            <td class="font-mono">${barang.serial}</td>
            <td class="fw-600">${barang.nama}</td>
            <td><span class="tag">${barang.jenis}</span></td>
            <td class="stok-angka"><span class="badge ${kelasBadgeStok(barang.kuantitas)}">${barang.kuantitas}</span></td>
            <td>${barang.lokasi}</td>
            <td>${barang.vendor}</td>
            <td class="action-cell">
                <button class="btn-icon edit" title="Edit"><i class="fa-solid fa-pen"></i></button>
                <button class="btn-icon delete" title="Hapus"><i class="fa-solid fa-trash"></i></button>
            </td>
        `;
        tabelBody.appendChild(tr);
    });
});

// ---- 7. BIKIN MODAL POP-UP OTOMATIS ----
const modalOverlay = document.createElement("div");
modalOverlay.id = "modalOverlay";
modalOverlay.style.cssText = `display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;`;
modalOverlay.innerHTML = `
    <div id="modalBox" style="background:#fff; border-radius:12px; padding:24px; width:420px; max-width:90%; font-family:'Outfit', sans-serif;">
        <h3 id="modalTitle" style="margin-bottom:16px;">Tambah Barang</h3>
        <form id="formBarang">
            <input type="hidden" id="fieldId">
            <div style="margin-bottom:10px;">
                <label style="display:block; font-size:13px; margin-bottom:4px;">Serial Number</label>
                <input type="text" id="fieldSerial" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:6px;">
            </div>
            <div style="margin-bottom:10px;">
                <label style="display:block; font-size:13px; margin-bottom:4px;">Nama Barang</label>
                <input type="text" id="fieldNama" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:6px;">
            </div>
            <div style="margin-bottom:10px;">
                <label style="display:block; font-size:13px; margin-bottom:4px;">Jenis Barang</label>
                <input type="text" id="fieldJenis" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:6px;">
            </div>
            <div style="margin-bottom:10px;">
                <label style="display:block; font-size:13px; margin-bottom:4px;">Kuantitas</label>
                <input type="number" id="fieldKuantitas" min="0" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:6px;">
            </div>
            <div style="margin-bottom:10px;">
                <label style="display:block; font-size:13px; margin-bottom:4px;">Lokasi Gudang</label>
                <input type="text" id="fieldLokasi" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:6px;">
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:13px; margin-bottom:4px;">Vendor</label>
                <input type="text" id="fieldVendor" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:6px;">
            </div>
            <div style="display:flex; gap:8px; justify-content:flex-end;">
                <button type="button" id="btnBatalModal" style="padding:8px 16px; border:1px solid #ccc; background:#fff; border-radius:6px; cursor:pointer;">Batal</button>
                <button type="submit" style="padding:8px 16px; border:none; background:#8a1538; color:#fff; border-radius:6px; cursor:pointer;">Simpan</button>
            </div>
        </form>
    </div>
`;
document.body.appendChild(modalOverlay);

const formBarang = document.querySelector("#formBarang");
const modalTitle = document.querySelector("#modalTitle");
const btnBatalModal = document.querySelector("#btnBatalModal");

function bukaModal(mode, barang = null) {
    formBarang.reset();
    if (mode === "edit" && barang) {
        modalTitle.textContent = "Edit Barang";
        document.querySelector("#fieldId").value = barang.id;
        document.querySelector("#fieldSerial").value = barang.serial;
        document.querySelector("#fieldNama").value = barang.nama;
        document.querySelector("#fieldJenis").value = barang.jenis;
        document.querySelector("#fieldKuantitas").value = barang.kuantitas;
        document.querySelector("#fieldLokasi").value = barang.lokasi;
        document.querySelector("#fieldVendor").value = barang.vendor;
    } else {
        modalTitle.textContent = "Tambah Barang";
        document.querySelector("#fieldId").value = "";
    }
    modalOverlay.style.display = "flex";
}

function tutupModal() {
    modalOverlay.style.display = "none";
}

btnBatalModal.addEventListener("click", tutupModal);
modalOverlay.addEventListener("click", (e) => {
    if (e.target === modalOverlay) tutupModal();
});

// ---- 8. FUNGSI KLIK TOMBOL (TAMBAH, EDIT, HAPUS) ----
tombolTambah.addEventListener("click", () => bukaModal("tambah"));

formBarang.addEventListener("submit", function (e) {
    e.preventDefault();
    const idField = document.querySelector("#fieldId").value;
    const dataBaru = {
        serial: document.querySelector("#fieldSerial").value.trim(),
        nama: document.querySelector("#fieldNama").value.trim(),
        jenis: document.querySelector("#fieldJenis").value.trim(),
        kuantitas: parseInt(document.querySelector("#fieldKuantitas").value, 10),
        lokasi: document.querySelector("#fieldLokasi").value.trim(),
        vendor: document.querySelector("#fieldVendor").value.trim()
    };

    if (idField) {
        const index = daftarBarang.findIndex((b) => b.id === parseInt(idField, 10));
        if (index !== -1) daftarBarang[index] = { ...daftarBarang[index], ...dataBaru };
    } else {
        daftarBarang.push({ id: idBerikutnya++, ...dataBaru });
    }

    window.alertMute = false; // Reset alert biar ngecek stok lagi abis update
    renderTabel();
    tutupModal();
    inputSearch.value = "";
});

tabelBody.addEventListener("click", function (e) {
    const tombolEdit = e.target.closest(".btn-icon.edit");
    const tombolHapus = e.target.closest(".btn-icon.delete");

    if (tombolEdit) {
        const id = parseInt(tombolEdit.closest("tr").dataset.id, 10);
        const barang = daftarBarang.find((b) => b.id === id);
        if (barang) bukaModal("edit", barang);
    }

    if (tombolHapus) {
        const id = parseInt(tombolHapus.closest("tr").dataset.id, 10);
        const barang = daftarBarang.find((b) => b.id === id);
        if (confirm(`Yakin mau hapus data "${barang ? barang.nama : "barang ini"}"?`)) {
            daftarBarang = daftarBarang.filter((b) => b.id !== id);
            renderTabel();
        }
    }
});

// ---- 9. JALANKAN PROGRAM PERTAMA KALI ----
window.alertMute = false; // Status awal izinkan pop-up alert
if (!muatDariLocalStorage()) {
    bacaDataAwalDariTabel();
}
renderTabel();
>>>>>>> 15b7fb67993cab7e1294f6405989a1c829d4957a
