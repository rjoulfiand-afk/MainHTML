document.addEventListener('DOMContentLoaded', function() {
    
    // ==========================================
    // 1. FITUR PENCARIAN (LIVE FILTERING)
    // ==========================================
    const searchInput = document.getElementById('searchInput');
    const tableRows = document.querySelectorAll('tbody tr');

    searchInput.addEventListener('keyup', function(e) {
        // Ambil teks yang diketik admin
        const term = e.target.value.toLowerCase();
        
        // Cek satu-satu setiap baris di tabel
        tableRows.forEach(row => {
            const rowData = row.textContent.toLowerCase();
            // Kalau kata cocok, tampilkan. Kalau tidak, sembunyikan.
            if(rowData.includes(term)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // ==========================================
    // 2. SISTEM ALERT STOK HABIS (OTOMATIS)
    // ==========================================
    function checkLowStock() {
        let barangHabis = [];

        tableRows.forEach(row => {
            // Ambil angka stok dari elemen yang punya class .badge
            const stokElement = row.querySelector('.stok-angka .badge');
            
            if (stokElement) {
                const stokValue = parseInt(stokElement.textContent);

                if (stokValue === 0) {
                    const namaBarang = row.cells[1].textContent;
                    barangHabis.push(namaBarang);
                    
                    // Kasih efek visual warna merah muda ke baris tabelnya
                    row.classList.add('row-danger');
                }
            }
        });
        if (barangHabis.length > 0) {
            // Delay sedikit biar halaman ke-load dulu baru alert muncul
            setTimeout(() => {
                alert("⚠️ PERINGATAN SISTEM: KEKURANGAN STOK!\n\nBarang berikut telah habis stoknya:\n- " + barangHabis.join("\n- ") + "\n\nMohon segera hubungi Vendor untuk restock!");
            }, 500);
        }
    }

    // Jalankan sistem pengecekan stok otomatis saat web dibuka
    checkLowStock();
});