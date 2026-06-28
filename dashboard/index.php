<?php
include "../config/koneksi.php";

// Proteksi halaman: jika belum login, tendang balik ke gerbang login
if(!isset($_SESSION['id_user'])){
    header("Location: ../auth/login.php");
    exit;
}

$role = $_SESSION['role'];

// Ambil nama user dengan fallback (jika session nama kosong/belum tersinkronisasi)
$nama_user = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Pengguna';

// 1. PANGGIL HEADER GLOBAL
include "../components/header.php"; 
?>

<span class="h1-tema">Dashboard</span>

<p style="font-size: 16px; color: #333;">
    Selamat datang kembali, <b><?= htmlspecialchars($nama_user); ?></b>
</p>

<p style="font-size: 14px; color: #666; margin-bottom: 25px;">
    Anda masuk ke sistem dengan hak akses: <span style="background: #e9ecef; padding: 4px 10px; border-radius: 4px; font-weight: bold; color: #495057;"><?= ucfirst($role); ?></span>
</p>

<hr style="border: 0; border-top: 1px solid #e9ecef; margin-bottom: 25px;">

<?php if($role == "pelamar"){ ?>
    <h3 style="color: #1e3d73; margin-bottom: 15px;">Menu Utama Anda:</h3>
    <ul style="line-height: 2.2; color: #495057; font-size: 15px; padding-left: 20px;">
        <li>Lengkapi data diri Anda di menu <b>Profil Pelamar</b></li>
        <li>Cari pekerjaan impian di menu <b>Lowongan</b></li>
        <li>Kirim berkas digital Anda lewat tombol <b>Lamar Pekerjaan</b></li>
        <li>Pantau kelulusan berkas secara *real-time* di menu <b>Lamaran Saya</b></li>
    </ul>
<?php } ?>

<?php if($role == "perusahaan"){ ?>
    <h3 style="color: #1e3d73; margin-bottom: 15px;">Menu Perusahaan Anda:</h3>
    <ul style="line-height: 2.2; color: #495057; font-size: 15px; padding-left: 20px;">
        <li>Kelola informasi data kantor di menu <b>Profil Perusahaan</b></li>
        <li>Buka lowongan baru melalui tombol <b>Posting Lowongan</b></li>
        <li>Pantau sisa kuota pencari kerja di menu <b>Kelola Lowongan</b></li>
        <li>Saring dan seleksi berkas kandidat di menu <b>Melihat Daftar Pelamar</b></li>
    </ul>
<?php } ?>

<?php if($role == "admin"){ ?>
    <h3 style="color: #1e3d73; margin-bottom: 15px;">Menu Manajemen Administrator:</h3>
    <ul style="line-height: 2.2; color: #495057; font-size: 15px; padding-left: 20px;">
        <li>Kelola semua akun pengguna di menu <b>Kelola User</b></li>
        <li>Lihat dan moderasi daftar pelamar di menu <b>Daftar Pelamar</b></li>
        <li>Lihat, edit, dan hapus daftar lowongan di menu <b>Daftar Lowongan</b></li>
        <li>Lihat daftar lamaran dan ubah status di menu <b>Daftar Lamaran</b></li>
        <li>Pantau statistik platform di menu <b>Laporan & Dashboard Analitik</b></li>
    </ul>
<?php } ?>

<?php 
// 3. PANGGIL FOOTER GLOBAL
include "../components/footer.php"; 
?>