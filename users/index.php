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

// Pastikan kolom is_suspended ada untuk menampilkan status suspend dan tombol unsuspend
$col_check = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'is_suspended'");
if(!$col_check || mysqli_num_rows($col_check) === 0){
    mysqli_query($conn, "ALTER TABLE users ADD COLUMN is_suspended TINYINT(1) NOT NULL DEFAULT 0");
}

// Mengambil nama tampilan dinamis dari session potongan email
$username_tampil = $_SESSION['username'] ?? 'Admin';

$status_message = '';
$error_message = '';
if(!empty($_GET['status'])){
    if($_GET['status'] === 'suspended') $status_message = 'Akun berhasil disuspend.';
    if($_GET['status'] === 'unsuspended') $status_message = 'Suspensi akun berhasil dibatalkan.';
    if($_GET['status'] === 'failed') $error_message = 'Gagal memproses aksi suspend/unsuspend. Periksa kembali data user dan parameter aksi.';
}
if(!empty($_GET['error'])){
    if($_GET['error'] === 'toggle_fail') $error_message = 'Terjadi kesalahan saat memperbarui status pengguna. Silakan coba lagi.';
    if($_GET['error'] === 'not_found') $error_message = 'Pengguna tidak ditemukan.';
}

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
        <a href="all_pelamar.php" style="color: white; text-decoration: none; font-size: 14px; font-weight: bold;">Daftar Pelamar</a>
        <span style="color: rgba(255,255,255,0.5);">|</span>
        <a href="all_lowongan.php" style="color: white; text-decoration: none; font-size: 14px; font-weight: bold;">Daftar Lowongan</a>
        <span style="color: rgba(255,255,255,0.5);">|</span>
        <a href="all_lamaran.php" style="color: white; text-decoration: none; font-size: 14px; font-weight: bold;">Daftar Lamaran</a>
        
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

    <?php if($status_message): ?>
        <div style="background: #d4edda; color: #155724; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            <?= htmlspecialchars($status_message); ?>
        </div>
    <?php endif; ?>
    <?php if($error_message): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <?= htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

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
                    $email = $row['email'] ?? '-';
                    $user = $row['username'] ?? $row['nama'] ?? null;
                    if(empty($user) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)){
                        $local = strtolower(substr($email, 0, strpos($email, '@')));
                        $local = preg_replace('/[._-]+/', ' ', $local);
                        $user = ucwords(trim($local));
                    }
                    $user = $user ?: 'User';
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
                        <?php $suspended = isset($row['is_suspended']) ? (int)$row['is_suspended'] : 0; ?>
                        <?php if($suspended): ?>
                            <span style="display:inline-flex; align-items:center; justify-content:center; min-height:28px; background:#f8d7da; color:#721c24; padding:0 12px; border-radius:12px; font-size:13px; font-weight:700;">Ditangguhkan</span>
                        <?php else: ?>
                            <span style="display:inline-flex; align-items:center; justify-content:center; min-height:28px; background:#d4edda; color:#155724; padding:0 12px; border-radius:12px; font-size:13px; font-weight:700;">Aktif</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 12px;">
                        <?php if($id == $_SESSION['id_user']): ?>
                            <span style="color: #999; font-size: 13px; font-style: italic;">Tidak bisa suspensi diri sendiri</span>
                        <?php else: ?>
                            <?php if($suspended): ?>
                                <form method="POST" action="toggle_suspend.php" style="display:inline; margin:0; padding:0;">
                                    <input type="hidden" name="id" value="<?= $id; ?>">
                                    <input type="hidden" name="action" value="unsuspend">
                                    <button type="submit" style="background:#28a745; color:#fff; padding:0 12px; min-height:28px; border-radius:12px; border:none; cursor:pointer; font-size:13px; font-weight:700; text-align:center;" onclick="return confirm('Batalkan suspensi akun ini?');">Batalkan Suspensi</button>
                                </form>
                            <?php else: ?>
                                <form method="POST" action="toggle_suspend.php" style="display:inline; margin:0; padding:0;">
                                    <input type="hidden" name="id" value="<?= $id; ?>">
                                    <input type="hidden" name="action" value="suspend">
                                    <button type="submit" style="background:#dc3545; color:#fff; padding:0 12px; min-height:28px; border-radius:12px; border:none; cursor:pointer; font-size:13px; font-weight:700; text-align:center;" onclick="return confirm('Apakah Anda yakin ingin mensuspensi akun ini?');">Suspensi Akun</button>
                                </form>
                            <?php endif; ?>
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