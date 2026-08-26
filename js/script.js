document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const tableRows = document.querySelectorAll('tbody tr');

    searchInput.addEventListener('keyup', function(e) {
        const term = e.target.value.toLowerCase();
        tableRows.forEach(row => {
            const rowData = row.textContent.toLowerCase();
            
            if(rowData.includes(term)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    function checkLowStock() {
        let barangHabis = [];

        tableRows.forEach(row => {
            
            const stokElement = row.querySelector('.stok-angka .badge');
            
            if (stokElement) {
                const stokValue = parseInt(stokElement.textContent);

                if (stokValue === 0) {
                    const namaBarang = row.cells[1].textContent;
                    barangHabis.push(namaBarang);
                    
                    row.classList.add('row-danger');
                }
            }
        });
        if (barangHabis.length > 0) {
            setTimeout(() => {
                alert("⚠️ PERINGATAN SISTEM: KEKURANGAN STOK!\n\nBarang berikut telah habis stoknya:\n- " + barangHabis.join("\n- ") + "\n\nMohon segera hubungi Vendor untuk restock!");
            }, 500);
        }
    }
    checkLowStock();
});