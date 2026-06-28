<?php
include "../config/koneksi.php";
if(!isset($_SESSION)) session_start();
if(!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../auth/login.php"); exit;
}

$data = mysqli_query($conn, "SELECT l.*, pel.nama_lengkap, low.judul_lowongan, u.email AS pel_email, p.nama_perusahaan FROM lamaran l JOIN pelamar pel ON l.id_pelamar=pel.id_pelamar JOIN lowongan low ON l.id_lowongan=low.id_lowongan LEFT JOIN users u ON pel.id_user=u.id_user LEFT JOIN perusahaan p ON low.id_perusahaan=p.id_perusahaan ORDER BY l.tanggal_lamar DESC");
include "../components/header.php";
?>
<span class="h1-tema">Daftar Lamaran</span>
<table class="table-tema">
    <thead><tr><th>ID</th><th>Pelamar</th><th>Posisi</th><th>Perusahaan</th><th>Tanggal</th><th>Status</th><th>Action</th></tr></thead>
    <tbody>
    <?php if(mysqli_num_rows($data)==0): ?>
        <tr><td colspan="7" style="text-align:center;color:#999;padding:30px;">Belum ada lamaran.</td></tr>
    <?php endif; ?>
    <?php while($r=mysqli_fetch_assoc($data)): ?>
        <tr>
            <td><?= $r['id_lamaran']; ?></td>
            <td><?= htmlspecialchars($r['nama_lengkap']); ?> <br><small><?= htmlspecialchars($r['pel_email'] ?? '-'); ?></small></td>
            <td><?= htmlspecialchars($r['judul_lowongan']); ?></td>
            <td><?= htmlspecialchars($r['nama_perusahaan'] ?? '-'); ?></td>
            <td><?= date('d M Y, H:i', strtotime($r['tanggal_lamar'])); ?></td>
            <td><?= htmlspecialchars($r['status_lamaran']); ?></td>
            <td>
                <a class="btn-biru" href="../lamaran/update_status.php?id=<?= $r['id_lamaran']; ?>">Ubah Status</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php include "../components/footer.php"; ?>
