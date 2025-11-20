<?php
function otorisasi($peran_yang_diizinkan) {
    // Pastikan session sudah dimulai. Jika belum, mulai.
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Cek apakah pengguna sudah login dan memiliki peran yang diizinkan
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $peran_yang_diizinkan)) {
        // Jika tidak, redirect ke landingpage.php
        header("Location: http://localhost/APK_KAS/landingpage.php");
        exit(); // Penting untuk menghentikan eksekusi skrip lebih lanjut
    }
}
?>