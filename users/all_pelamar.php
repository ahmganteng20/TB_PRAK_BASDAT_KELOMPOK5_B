<?php
include "../config/koneksi.php";
if(!isset($_SESSION)) session_start();
if(!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../auth/login.php"); exit;
}

$data = mysqli_query($conn, "SELECT p.*, u.email FROM pelamar p LEFT JOIN users u ON p.id_user=u.id_user ORDER BY p.id_pelamar DESC");
include "../components/header.php";
?>
<span class="h1-tema">Daftar Pelamar</span>
<table class="table-tema">
    <thead><tr><th>ID</th><th>Nama</th><th>No HP</th><th>Alamat</th><th>Email</th></tr></thead>
    <tbody>
    <?php if(mysqli_num_rows($data)==0): ?>
        <tr><td colspan="5" style="text-align:center;color:#999;padding:30px;">Belum ada pelamar.</td></tr>
    <?php endif; ?>
    <?php while($r=mysqli_fetch_assoc($data)): ?>
        <tr>
            <td><?= $r['id_pelamar']; ?></td>
            <td><?= htmlspecialchars($r['nama_lengkap']); ?></td>
            <td><?= htmlspecialchars($r['no_hp']); ?></td>
            <td><?= htmlspecialchars($r['alamat']); ?></td>
            <td><?= htmlspecialchars($r['email'] ?? '-'); ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php include "../components/footer.php"; ?>
