
const tabelBody = document.querySelector("#inventoryTable tbody");
const inputSearch = document.querySelector("#searchInput");

if (inputSearch && tabelBody) {
    inputSearch.addEventListener("input", function () {
        const kataKunci = this.value.trim().toLowerCase();
        const semuaBaris = tabelBody.querySelectorAll("tr");

        
        semuaBaris.forEach((tr) => {
            const kolom = tr.querySelectorAll("td");
            // Pastikan baris memiliki data (bukan baris kosong)
            if (kolom.length < 2) return; 

            // Cari berdasarkan Serial Number (kolom 0) atau Nama (kolom 1)
            const serial = kolom[0].textContent.trim().toLowerCase();
            const nama = kolom[1].textContent.trim().toLowerCase();

            const cocok = serial.includes(kataKunci) || nama.includes(kataKunci);
            tr.style.display = cocok ? "" : "none";
        });
    });
}

const btnAdd = document.getElementById("btnTambahBarang"); 
const modalTambah = document.getElementById("modalTambah");
const btnCloseModal = document.getElementById("btnCloseModal"); 
const btnCancelModal = document.getElementById("btnCancelModal");

// Fungsi buka modal
function bukaModal() {
    if(modalTambah) modalTambah.classList.add("show");
}

// Fungsi tutup modal
function tutupModal() {
    if(modalTambah) modalTambah.classList.remove("show");
}

if(btnAdd) btnAdd.addEventListener("click", bukaModal);
if(btnCloseModal) btnCloseModal.addEventListener("click", tutupModal);
if(btnCancelModal) btnCancelModal.addEventListener("click", tutupModal);

// Tutup modal kalau user ngeklik sembarang di luar area kotak putih
window.addEventListener("click", function(e) {
    if (e.target === modalTambah) {
        tutupModal();
    }
});
// ---- FITUR MODAL POP-UP TAMBAH GUDANG ----
const btnTambahGudang = document.getElementById("btnTambahGudang");
const modalGudang = document.getElementById("modalTambahGudang");
const btnCloseGudang = document.getElementById("btnCloseGudang");
const btnCancelGudang = document.getElementById("btnCancelGudang");

function bukaModalGudang() {
    if(modalGudang) modalGudang.classList.add("show");
}

function tutupModalGudang() {
    if(modalGudang) modalGudang.classList.remove("show");
}

if(btnTambahGudang) btnTambahGudang.addEventListener("click", bukaModalGudang);
if(btnCloseGudang) btnCloseGudang.addEventListener("click", tutupModalGudang);
if(btnCancelGudang) btnCancelGudang.addEventListener("click", tutupModalGudang);

// Tutup modal kalau user ngeklik di luar area kotak putih
window.addEventListener("click", function(e) {
    if (e.target === modalGudang) {
        tutupModalGudang();
    }
});
// ---- FITUR MODAL POP-UP TAMBAH VENDOR ----
const btnTambahVendor = document.getElementById("btnTambahVendor");
const modalVendor = document.getElementById("modalTambahVendor");
const btnCloseVendor = document.getElementById("btnCloseVendor");
const btnCancelVendor = document.getElementById("btnCancelVendor");

function bukaModalVendor() {
    if(modalVendor) modalVendor.classList.add("show");
}

function tutupModalVendor() {
    if(modalVendor) modalVendor.classList.remove("show");
}

if(btnTambahVendor) btnTambahVendor.addEventListener("click", bukaModalVendor);
if(btnCloseVendor) btnCloseVendor.addEventListener("click", tutupModalVendor);
if(btnCancelVendor) btnCancelVendor.addEventListener("click", tutupModalVendor);

window.addEventListener("click", function(e) {
    if (e.target === modalVendor) {
        tutupModalVendor();
    }
});
// ---- FITUR MODAL POP-UP EDIT BARANG ----
const modalEdit = document.getElementById("modalEdit");
const btnCloseEdit = document.getElementById("btnCloseEdit");
const btnCancelEdit = document.getElementById("btnCancelEdit");

// Fungsi buka modal & isi data otomatis
function bukaModalEdit(id, sn, nama, jenis, stok, harga, id_gudang, id_vendor) {
    document.getElementById("edit_id_barang").value = id;
    document.getElementById("edit_serial").value = sn;
    document.getElementById("edit_nama").value = nama;
    document.getElementById("edit_jenis").value = jenis;
    document.getElementById("edit_stok").value = stok;
    document.getElementById("edit_harga").value = harga;
    document.getElementById("edit_gudang").value = id_gudang;
    document.getElementById("edit_vendor").value = id_vendor;
    
    if(modalEdit) modalEdit.classList.add("show");
}

function tutupModalEdit() {
    if(modalEdit) modalEdit.classList.remove("show");
}

if(btnCloseEdit) btnCloseEdit.addEventListener("click", tutupModalEdit);
if(btnCancelEdit) btnCancelEdit.addEventListener("click", tutupModalEdit);

window.addEventListener("click", function(e) {
    if (e.target === modalEdit) {
        tutupModalEdit();
    }
});
