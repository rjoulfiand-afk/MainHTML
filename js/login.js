document.addEventListener("DOMContentLoaded", function () {
  try {
    iniBerjalan(); // penanda: kalau ini tidak muncul di console, file ini tidak ke-load
  } catch (error) {
    alert(
      "Terjadi error di login.js: " +
        error.message +
        "\n\nCek Console (F12) untuk detail.",
    );
    console.error("LOGIN.JS ERROR:", error);
  }
});

function iniBerjalan() {
  console.log("[login.js] File berhasil dimuat dan dijalankan.");

  const DAFTAR_ADMIN = [
    { id: "admin", password: "admin123", nama: "Admin" },
    { id: "kelompokganteng", password: "kg2026", nama: "Super Admin" },
  ];

  //AMBIL ELEMEN FORM (dengan pengecekan)
  const formLogin = document.querySelector("form");
  const inputId = document.querySelector("#admin_id");
  const inputPassword = document.querySelector("#password");
  const checkboxIngat = document.querySelector(
    '.checkbox-container input[type="checkbox"]',
  );
  const tombolSubmit = document.querySelector(".btn-submit");

  // Kalau salah satu elemen penting tidak ketemu, kasih tahu jelas lewat alert
  const elemenHilang = [];
  if (!formLogin) elemenHilang.push("form (<form>)");
  if (!inputId) elemenHilang.push("#admin_id");
  if (!inputPassword) elemenHilang.push("#password");
  if (!tombolSubmit) elemenHilang.push(".btn-submit");

  if (elemenHilang.length > 0) {
    alert(
      "login.js dimuat, TAPI ada elemen HTML yang tidak ditemukan:\n\n" +
        elemenHilang.join("\n") +
        "\n\nKemungkinan file index.html/git.html yang dipakai berbeda dari yang seharusnya.",
    );
    return;
  }

  let pesanError = document.querySelector("#loginError");
  if (!pesanError) {
    pesanError = document.createElement("p");
    pesanError.id = "loginError";
    pesanError.style.color = "#e74c3c";
    pesanError.style.fontSize = "13px";
    pesanError.style.marginTop = "-10px";
    pesanError.style.marginBottom = "10px";
    pesanError.style.display = "none";
    formLogin.insertBefore(pesanError, tombolSubmit);
  }

  // ---- Isi ulang form kalau "Ingat Saya" aktif ----
  const idTersimpan = localStorage.getItem("kg_remember_id");
  if (idTersimpan && checkboxIngat) {
    inputId.value = idTersimpan;
    checkboxIngat.checked = true;
  }

  function tampilkanError(pesan) {
    pesanError.textContent = pesan;
    pesanError.style.display = "block";
  }

  function sembunyikanError() {
    pesanError.style.display = "none";
  }

  formLogin.addEventListener("submit", function (e) {
    e.preventDefault();
    console.log("[login.js] Form submit ditangkap, memproses login...");
    sembunyikanError();

    const idInput = inputId.value.trim().replace(/\s+/g, "");
    const passwordInput = inputPassword.value.trim();

    if (idInput === "" || passwordInput === "") {
      tampilkanError("ID Admin dan Kata Sandi wajib diisi.");
      return;
    }

    const adminDitemukan = DAFTAR_ADMIN.find(
      (admin) => admin.id.toLowerCase() === idInput.toLowerCase(),
    );

    if (!adminDitemukan) {
      console.log(
        "[login.js] ID yang diketik (raw):",
        JSON.stringify(idInput),
        "| panjang:",
        idInput.length,
      );
      tampilkanError(
        'ID Admin tidak ditemukan. Yang terbaca: "' +
          idInput +
          '" (panjang: ' +
          idInput.length +
          " karakter)",
      );
      return;
    }

    if (adminDitemukan.password !== passwordInput) {
      tampilkanError("Kata sandi salah. Coba lagi.");
      return;
    }

    sessionStorage.setItem("kg_isLoggedIn", "true");
    sessionStorage.setItem("kg_namaAdmin", adminDitemukan.nama);

    if (checkboxIngat && checkboxIngat.checked) {
      localStorage.setItem("kg_remember_id", idInput);
    } else {
      localStorage.removeItem("kg_remember_id");
    }

    tombolSubmit.textContent = "Berhasil masuk...";
    tombolSubmit.disabled = true;

    setTimeout(() => {
      window.location.href = "dashboard.html";
    }, 400);
  });
}
