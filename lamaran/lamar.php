<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "../config/koneksi.php";

if(!isset($_SESSION['id_user'])){
    header("Location: ../auth/login.php");
    exit;
}

$id_lowongan = mysqli_real_escape_string($conn, $_GET['id']);
$id_user = $_SESSION['id_user'];

// 1. Cari id_pelamar berdasarkan id_user
$cari = mysqli_query($conn, "SELECT id_pelamar FROM pelamar WHERE id_user='$id_user'");
$pelamar = mysqli_fetch_assoc($cari);

// VALIDASI: Jika data pelamar tidak ditemukan di database
if(!$pelamar){
    echo "<script>
        alert('Gagal melamar! Akun Anda belum melengkapi profil pelamar atau role Anda bukan pelamar.');
        window.location.href = '../lowongan/index.php';
    </script>";
    exit;
}

$id_pelamar = $pelamar['id_pelamar'];

// 2. CEK STATUS LOWONGAN TERLEBIH DAHULU (Mendukung validasi open / closed / Buka)
$query_lowongan = mysqli_query($conn, "SELECT status FROM lowongan WHERE id_lowongan='$id_lowongan'");
$lowongan = mysqli_fetch_assoc($query_lowongan);

if(!$lowongan || in_array(strtolower($lowongan['status']), ['closed', 'tutup'])) {
    echo "<script>
        alert('Maaf, kuota lowongan ini sudah penuh atau sudah ditutup!');
        window.location.href = '../lowongan/index.php';
    </script>";
    exit;
}

// 3. VALIDASI: Cek apakah pelamar sudah pernah melamar di lowongan ini
$cek = mysqli_query(
    $conn,
    "SELECT * FROM lamaran WHERE id_pelamar='$id_pelamar' AND id_lowongan='$id_lowongan'"
);

if(mysqli_num_rows($cek) > 0){
    echo "<script>
        alert('Anda sudah pernah melamar lowongan ini');
        window.location='../lowongan/index.php';
    </script>";
    exit;
}

// 4. PROSES SIMPAN: Cukup Insert saja! 
// Sesaat setelah ini ter-insert, TRIGGER MySQL 'cek_kuota_lowongan' yang kita buat kemarin
// akan langsung otomatis menghitung sisa kuota dan menutup lowongan secara mandiri.
$insert = mysqli_query($conn, "
    INSERT INTO lamaran (id_pelamar, id_lowongan, tanggal_lamar, status_lamaran)
    VALUES ('$id_pelamar', '$id_lowongan', NOW(), 'pending')
");

if($insert){
    // Langsung arahkan ke halaman daftar lamaran saya
    header("Location: index.php");
    exit;
} else {
    echo "Error: " . mysqli_error($conn);
}
?>