// ---- 0. PROTEKSI HALAMAN: HARUS LOGIN DULU ----
if (sessionStorage.getItem("kg_isLoggedIn") !== "true") {
    window.location.href = "index.html";
}

window.addEventListener("DOMContentLoaded", () => {
    const namaAdmin = sessionStorage.getItem("kg_namaAdmin");
    const elemenNama = document.querySelector(".profile-info h4");
    if (namaAdmin && elemenNama) {
        elemenNama.textContent = namaAdmin;
    }
});

// ---- 1. AMBIL ELEMEN UTAMA ----
const tabelBody = document.querySelector("#inventoryTable tbody");
const inputSearch = document.querySelector("#searchInput");
const tombolTambah = document.querySelector(".btn-add");

// ---- 2. DATA BARANG ----
// Data disimpan di localStorage supaya tidak hilang saat refresh.

let daftarBarang = [];
let idBerikutnya = 1;

const KUNCI_LOCALSTORAGE = "kg_daftarBarang";

// Simpan array daftarBarang ke localStorage (dipanggil tiap ada perubahan)
function simpanKeLocalStorage() {
    localStorage.setItem(KUNCI_LOCALSTORAGE, JSON.stringify(daftarBarang));
}

// Coba muat data dari localStorage. Return true kalau berhasil ada datanya.
function muatDariLocalStorage() {
    const dataTersimpan = localStorage.getItem(KUNCI_LOCALSTORAGE);
    if (!dataTersimpan) return false;

    try {
        const hasilParse = JSON.parse(dataTersimpan);
        if (Array.isArray(hasilParse) && hasilParse.length > 0) {
            daftarBarang = hasilParse;
            // Pastikan id berikutnya selalu lebih besar dari id yang sudah ada
            idBerikutnya = Math.max(...daftarBarang.map((b) => b.id)) + 1;
            return true;
        }
    } catch (error) {
        console.error("[script.js] Gagal membaca data tersimpan:", error);
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

// ---- 3. FUNGSI BANTUAN: STATUS BADGE STOK ----
function kelasBadgeStok(jumlah) {
    if (jumlah === 0) return "danger";
    if (jumlah <= 5) return "warning"; // stok menipis
    return "safe";
}

// ---- 4. RENDER TABEL DARI ARRAY ----
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

    data.forEach((barang) => {
        const tr = document.createElement("tr");
        tr.dataset.id = barang.id;
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
}

// ---- 5. UPDATE ANGKA DI STAT CARDS (Total Stok & Perlu Restock) ----
function perbaruiStatistik() {
    const totalStok = daftarBarang.reduce((total, b) => total + b.kuantitas, 0);
    const perluRestock = daftarBarang.filter((b) => b.kuantitas === 0).length;

    const statTotalStok = document.querySelector(".stat-card:nth-child(1) h3");
    const statRestock = document.querySelector(".stat-card:nth-child(4) h3");

    if (statTotalStok) {
        statTotalStok.innerHTML = `${totalStok.toLocaleString("id-ID")} <span>Unit</span>`;
    }
    if (statRestock) {
        statRestock.innerHTML = `${perluRestock} <span>Barang</span>`;
        statRestock.classList.toggle("text-danger", perluRestock > 0);
    }
}

// ---- 6. SEARCH / FILTER TABEL ----
inputSearch.addEventListener("input", function () {
    const kataKunci = this.value.trim().toLowerCase();

    const hasilFilter = daftarBarang.filter((barang) =>
        barang.serial.toLowerCase().includes(kataKunci) ||
        barang.nama.toLowerCase().includes(kataKunci)
    );

    renderTabelTanpaUbahStatistik(hasilFilter);
});

// Render khusus hasil pencarian (statistik atas tetap merujuk ke total keseluruhan)
function renderTabelTanpaUbahStatistik(data) {
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
    data.forEach((barang) => {
        const tr = document.createElement("tr");
        tr.dataset.id = barang.id;
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
}

// ---- 7. MODAL FORM (dibuat lewat JS, tidak perlu ubah HTML) ----
const modalOverlay = document.createElement("div");
modalOverlay.id = "modalOverlay";
modalOverlay.style.cssText = `
    display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5);
    z-index:999; align-items:center; justify-content:center;
`;
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
                <button type="submit" style="padding:8px 16px; border:none; background:#4f46e5; color:#fff; border-radius:6px; cursor:pointer;">Simpan</button>
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

// ---- 8. TAMBAH BARANG ----
tombolTambah.addEventListener("click", () => bukaModal("tambah"));

// ---- 9. SIMPAN (TAMBAH ATAU EDIT) ----
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
        // MODE EDIT: cari data lama, update
        const index = daftarBarang.findIndex((b) => b.id === parseInt(idField, 10));
        if (index !== -1) {
            daftarBarang[index] = { ...daftarBarang[index], ...dataBaru };
        }
    } else {
        // MODE TAMBAH: buat data baru
        daftarBarang.push({ id: idBerikutnya++, ...dataBaru });
    }

    renderTabel();
    tutupModal();
    inputSearch.value = "";
});

tabelBody.addEventListener("click", function (e) {
    const tombolEdit = e.target.closest(".btn-icon.edit");
    const tombolHapus = e.target.closest(".btn-icon.delete");

    if (tombolEdit) {
        const tr = tombolEdit.closest("tr");
        const id = parseInt(tr.dataset.id, 10);
        const barang = daftarBarang.find((b) => b.id === id);
        if (barang) bukaModal("edit", barang);
    }

    if (tombolHapus) {
        const tr = tombolHapus.closest("tr");
        const id = parseInt(tr.dataset.id, 10);
        const barang = daftarBarang.find((b) => b.id === id);

        const konfirmasi = confirm(`Yakin mau hapus "${barang ? barang.nama : "barang ini"}"?`);
        if (konfirmasi) {
            daftarBarang = daftarBarang.filter((b) => b.id !== id);
            renderTabel();
        }
    }
});

// ---- 11. INISIALISASI SAAT HALAMAN DIBUKA ----
const berhasilMuatDariLocalStorage = muatDariLocalStorage();
if (!berhasilMuatDariLocalStorage) {
    bacaDataAwalDariTabel();
}
renderTabel();