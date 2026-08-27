document.addEventListener("DOMContentLoaded", function () {
    try {
        iniBerjalan();
    } catch (error) {
        alert(
            "Terjadi error di login.js: " +
            error.message +
            "\n\nCek Console (F12) untuk detail."
        );

        console.error("LOGIN.JS ERROR:", error);
    }
});


function iniBerjalan() {

    console.log("[login.js] File berhasil dimuat dan dijalankan.");

    const formLogin = document.querySelector("form");
    const inputId = document.querySelector("#admin_id");
    const inputPassword = document.querySelector("#password");

    const checkboxIngat = document.querySelector(
        '.checkbox-container input[type="checkbox"]'
    );

    const tombolSubmit = document.querySelector(".btn-submit");


    // ==========================================
    // CEK ELEMEN HTML
    // ==========================================

    const elemenHilang = [];

    if (!formLogin) {
        elemenHilang.push("form (<form>)");
    }

    if (!inputId) {
        elemenHilang.push("#admin_id");
    }

    if (!inputPassword) {
        elemenHilang.push("#password");
    }

    if (!tombolSubmit) {
        elemenHilang.push(".btn-submit");
    }


    if (elemenHilang.length > 0) {

        alert(
            "login.js dimuat, TAPI ada elemen HTML yang tidak ditemukan:\n\n" +
            elemenHilang.join("\n") +
            "\n\nCek kembali index.html."
        );

        return;
    }


    // ==========================================
    // PESAN ERROR
    // ==========================================

    let pesanError = document.querySelector("#loginError");

    if (!pesanError) {

        pesanError = document.createElement("p");

        pesanError.id = "loginError";

        pesanError.style.color = "#e74c3c";
        pesanError.style.fontSize = "13px";
        pesanError.style.marginTop = "-10px";
        pesanError.style.marginBottom = "10px";
        pesanError.style.display = "none";

        formLogin.insertBefore(
            pesanError,
            tombolSubmit
        );
    }


    // ==========================================
    // INGAT SAYA
    // ==========================================

    const idTersimpan =
        localStorage.getItem("kg_remember_id");

    if (idTersimpan && checkboxIngat) {

        inputId.value = idTersimpan;

        checkboxIngat.checked = true;
    }

    
    const parameterURL =
        new URLSearchParams(window.location.search);

    const error =
        parameterURL.get("error");


    if (error === "kosong") {

        pesanError.textContent =
            "ID Admin dan Kata Sandi wajib diisi.";

        pesanError.style.display = "block";
    }


    if (error === "salah") {

        pesanError.textContent =
            "ID Admin atau Kata Sandi salah.";

        pesanError.style.display = "block";
    }


    // ==========================================
    // PROSES SUBMIT LOGIN
    // ==========================================

    formLogin.addEventListener(
        "submit",
        function (e) {

            const idInput =
                inputId.value
                    .trim()
                    .replace(/\s+/g, "");

            const passwordInput =
                inputPassword.value.trim();


            // ======================================
            // CEK INPUT KOSONG
            // ======================================

            if (
                idInput === "" ||
                passwordInput === ""
            ) {

                e.preventDefault();

                pesanError.textContent =
                    "ID Admin dan Kata Sandi wajib diisi.";

                pesanError.style.display =
                    "block";

                return;
            }


            // ======================================
            // INGAT SAYA
            // ======================================

            if (
                checkboxIngat &&
                checkboxIngat.checked
            ) {

                localStorage.setItem(
                    "kg_remember_id",
                    idInput
                );

            } else {

                localStorage.removeItem(
                    "kg_remember_id"
                );
            }


            // ======================================
            // KIRIM KE login.php
            // ======================================

            tombolSubmit.textContent =
                "Memproses...";

            tombolSubmit.disabled = true;

            // Tidak menggunakan e.preventDefault()
            // supaya form dikirim ke login.php
        }
    );
}