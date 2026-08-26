/* CATATAN UNTUK TEMAN SEKELOMPOK:
   Logika CRUD (Tambah / Edit / Hapus barang) BELUM ada di sini.
   Silakan lanjutkan dengan menambahkan:
   - Fungsi untuk menambah baris baru ke tabel #inventoryTable
   - Fungsi untuk mengedit data pada baris yang tombol .edit-nya diklik
   - Fungsi untuk menghapus baris saat tombol .delete diklik
   - (Opsional) simpan datanya ke localStorage atau backend
     supaya tidak hilang saat halaman di-refresh */

// ---- 1. PROTEKSI HALAMAN: HARUS LOGIN DULU ----
if (sessionStorage.getItem("kg_isLoggedIn") !== "true") {
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

