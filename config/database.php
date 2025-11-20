<?php
$host = "localhost";
$user = "root"; // ganti sesuai username MySQL
$pass = "";     // ganti sesuai password MySQL
$db   = "db_kas_kelas"; // sesuai database

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
