<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "job_portal";

// Menggunakan error reporting yang senyap saat production untuk keamanan
mysqli_report(MYSQLI_REPORT_OFF);

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    // Di masa production, simpan eror asli ke log server, lalu tampilkan pesan ramah ke user
    error_log("Database Connection Error: " . mysqli_connect_error());
    die("Maaf, terjadi gangguan pada sistem. Silakan coba beberapa saat lagi.");
}

// WAJIB DI PRAKTIKUM: Set charset ke utf8mb4 agar mendukung semua karakter teks modern
mysqli_set_charset($conn, "utf8mb4");

// Memulai session dengan aman
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>