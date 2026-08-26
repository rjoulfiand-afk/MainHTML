<?php

session_start();

include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $admin_id = trim($_POST["admin_id"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($admin_id === "" || $password === "") {
        header("Location: index.html?error=kosong");
        exit;
    }

    $query = "SELECT * FROM admin WHERE admin_id = ? AND password = ?";

    $stmt = mysqli_prepare($koneksi, $query);

    mysqli_stmt_bind_param($stmt, "ss", $admin_id, $password);

    mysqli_stmt_execute($stmt);

    $hasil = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($hasil) === 1) {

        $admin = mysqli_fetch_assoc($hasil);

        $_SESSION["kg_isLoggedIn"] = true;
        $_SESSION["kg_namaAdmin"] = $admin["nama"];

        header("Location: dashboard.html");
        exit;

    } else {

        header("Location: index.html?error=salah");
        exit;
    }

    mysqli_stmt_close($stmt);
}

?>