<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek alternatif semua kata kunci session yang mungkin kamu gunakan di file login.php
if (!empty($_SESSION['username'])) {
    $username_tampil = $_SESSION['username'];
} elseif (!empty($_SESSION['nama'])) {
    $username_tampil = $_SESSION['nama'];
} elseif (!empty($_SESSION['nama_user'])) {
    $username_tampil = $_SESSION['nama_user'];
} else {
    $username_tampil = 'Pengguna Aktif'; // Cadangan terakhir jika benar-benar kosong
}

// Pastikan pembacaan role sinkron dengan session login
$role_nav = $_SESSION['role'] ?? 'pelamar'; 
?>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek alternatif semua kata kunci session yang digunakan saat login
if (!empty($_SESSION['nama'])) {
    $username_tampil = $_SESSION['nama'];
} elseif (!empty($_SESSION['username'])) {
    $username_tampil = $_SESSION['username'];
} elseif (!empty($_SESSION['nama_user'])) {
    $username_tampil = $_SESSION['nama_user'];
} else {
    $username_tampil = 'Pengguna'; // Cadangan terakhir jika session nama kosong
}

// Pastikan pembacaan role sinkron dengan session login
$role_nav = $_SESSION['role'] ?? 'pelamar'; 
?>

<div style="background-color: #007bff; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; font-family: Arial, sans-serif; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 30px;">
    <a href="../dashboard/index.php" style="color: white; text-decoration: none; font-size: 24px; font-weight: bold;">
        LokerIn
    </a>
    
    <div style="display: flex; align-items: center; gap: 20px;">
        
        <?php if($role_nav == "admin"): ?>
            <a href="../users/index.php" style="color: white; text-decoration: none; font-size: 14px; font-weight: bold;">Kelola User</a>
            
        <?php elseif($role_nav == "perusahaan"): ?>
            <a href="../perusahaan/profil.php" style="color: white; text-decoration: none; font-size: 14px;">Profil Perusahaan</a>
            <span style="color: rgba(255,255,255,0.5);">|</span>
            <a href="../lowongan/index.php" style="color: white; text-decoration: none; font-size: 14px;">Kelola Lowongan</a>
            <span style="color: rgba(255,255,255,0.5);">|</span>
            <a href="../perusahaan/pelamar.php" style="color: white; text-decoration: none; font-size: 14px;">Melihat Daftar Pelamar</a>
            
        <?php else: ?>
            <a href="../pelamar/profil.php" style="color: white; text-decoration: none; font-size: 14px;">Profil Pelamar</a>
            <span style="color: rgba(255,255,255,0.5);">|</span>
            <a href="../lowongan/index.php" style="color: white; text-decoration: none; font-size: 14px;">Lowongan</a>
            <span style="color: rgba(255,255,255,0.5);">|</span>
            <a href="../lamaran/index.php" style="color: white; text-decoration: none; font-size: 14px;">Lamaran Saya</a>
        <?php endif; ?>
        
        <span style="color: rgba(255,255,255,0.5);">|</span>
        <span style="background: rgba(255,255,255,0.2); color: white; padding: 4px 12px; border-radius: 4px; font-size: 14px; font-weight: bold;">
            <?= htmlspecialchars($username_tampil); ?>
        </span>
        
        <span style="color: rgba(255,255,255,0.5);">|</span>
        <a href="../auth/logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?')" style="color: white; text-decoration: none; font-size: 14px;">Logout</a>
    </div>
</div>