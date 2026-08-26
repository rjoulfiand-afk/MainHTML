// ---- PROTEKSI HALAMAN FRONTEND ----
if (sessionStorage.getItem("kg_isLoggedIn") !== "true") {
    window.location.href = "index.html";
}

// ---- UPDATE NAMA PROFIL ----
window.addEventListener("DOMContentLoaded", () => {
    const namaAdmin = sessionStorage.getItem("kg_namaAdmin");
    const elemenNama = document.querySelector(".profile-info h4");
    if (namaAdmin && elemenNama) {
        elemenNama.textContent = namaAdmin;
    }
});

// ---- FITUR PENCARIAN LIVE ----
const tabelBody = document.querySelector("#inventoryTable tbody");
const inputSearch = document.querySelector("#searchInput");

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
// ---- FITUR MODAL POP-UP TAMBAH BARANG ----
const btnAdd = document.querySelector(".btn-add"); // Tombol di atas tabel
const modalTambah = document.getElementById("modalTambah");
const btnCloseModal = document.getElementById("btnCloseModal"); // Ikon X
const btnCancelModal = document.getElementById("btnCancelModal"); // Tombol Batal

// Fungsi buka modal
function bukaModal() {
    modalTambah.classList.add("show");
}

function tutupModal() {
    modalTambah.classList.remove("show");
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