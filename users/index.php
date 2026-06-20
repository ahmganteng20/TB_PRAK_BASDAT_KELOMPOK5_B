<?php
// Pastikan session dimulai paling atas
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "../config/koneksi.php";

// Proteksi halaman: Hanya boleh diakses oleh Admin
if(!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

// Mengambil nama tampilan dinamis dari session potongan email
$username_tampil = $_SESSION['username'] ?? 'Admin';

// =========================================================================
// TRY-CATCH QUERY: Jika error, tampilkan detail pesan error dari MySQL kamu
// =========================================================================
$query = mysqli_query($conn, "SELECT * FROM users ORDER BY id_user DESC");

if (!$query) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 20px; border-radius: 5px; border: 1px solid #f5c6cb; margin: 20px 0; font-family: Arial;'>";
    echo "<h3 style='margin-top:0;'>⚠️ Terjadi Ketidakcocokan Database!</h3>";
    echo "Pesan Error MySQL: <b>" . mysqli_error($conn) . "</b><br><br>";
    echo "<i>Tips Solusi: Pastikan nama tabel kamu di phpMyAdmin benar-benar bernama <b>users</b> dan memiliki kolom bernama <b>id_user</b>. Jika berbeda, sesuaikan query di file users/index.php baris 19.</i>";
    echo "</div>";
    include "../components/footer.php";
    exit;
}
// =========================================================================
?>

<div style="background-color: #007bff; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; font-family: Arial, sans-serif; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 30px;">
    <a href="../dashboard/index.php" style="color: white; text-decoration: none; font-size: 24px; font-weight: bold;">
        LokerIn
    </a>
    
    <div style="display: flex; align-items: center; gap: 20px;">
        <a href="../users/index.php" style="color: white; text-decoration: none; font-size: 14px; font-weight: bold;">Kelola User</a>
        
        <span style="color: rgba(255,255,255,0.5);">|</span>
        <span style="background: rgba(255,255,255,0.2); color: white; padding: 4px 12px; border-radius: 4px; font-size: 14px; font-weight: bold;">
            <?= htmlspecialchars($username_tampil); ?>
        </span>
        
        <span style="color: rgba(255,255,255,0.5);">|</span>
        <a href="../auth/logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?')" style="color: white; text-decoration: none; font-size: 14px;">Logout</a>
    </div>
</div>
<div style="padding: 0 40px; font-family: Arial, sans-serif;">
    <span style="font-size: 28px; font-weight: bold; color: #333; display: block; margin-bottom: 10px;">Manajemen Pengguna (Kelola User)</span>
    <p style="color: #666; margin-bottom: 20px;">
        Halaman khusus Administrator untuk memantau status, melakukan verifikasi berkas, atau menangguhkan akun pengguna yang melanggar ketentuan platform.
    </p>

    <table class="table-tema" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="background-color: #007bff; color: white; text-align: left;">
                <th style="padding: 12px;">ID User</th>
                <th style="padding: 12px;">Username</th>
                <th style="padding: 12px;">Alamat Email</th>
                <th style="padding: 12px;">Hak Akses (Role)</th>
                <th style="padding: 12px;">Status Akun</th>
                <th style="padding: 12px;">Tindakan Moderator</th>
            </tr>
        </thead>
        <tbody>
            <?php if(mysqli_num_rows($query) == 0): ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #999; padding: 30px;">
                        Belum ada data pengguna di dalam sistem.
                    </td>
                </tr>
            <?php endif; ?>

            <?php while($row = mysqli_fetch_assoc($query)){ ?>
                <?php 
                    $id = $row['id_user'] ?? $row['id'] ?? '0';
                    $user = $row['username'] ?? $row['nama'] ?? 'User';
                    $email = $row['email'] ?? '-';
                    $role_user = $row['role'] ?? 'pelamar';
                ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px;"><?= $id; ?></td>
                    <td style="padding: 12px; font-weight: bold; color: #333;"><?= htmlspecialchars($user); ?></td>
                    <td style="padding: 12px;"><?= htmlspecialchars($email); ?></td>
                    <td style="padding: 12px;">
                        <?php if($role_user == 'admin'): ?>
                            <span style="background: #e2e3e5; color: #383d41; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">Admin</span>
                        <?php elseif($role_user == 'perusahaan'): ?>
                            <span style="background: #cce5ff; color: #004085; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">Perusahaan</span>
                        <?php else: ?>
                            <span style="background: #d1ecf1; color: #0c5460; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">Pelamar</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 12px;">
                        <span style="background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">Aktif</span>
                    </td>
                    <td style="padding: 12px;">
                        <?php if($role_user !== 'admin'): ?>
                            <button style="background: #dc3545; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 13px;" onclick="alert('Simulasi: Akun <?= htmlspecialchars($user); ?> berhasil ditangguhkan sementara demi keamanan.')">
                                Suspensi Akun
                            </button>
                        <?php else: ?>
                            <span style="color: #999; font-size: 13px; font-style: italic;">Utama</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php 
// 3. PANGGIL FOOTER GLOBAL
include "../components/footer.php"; 
?>