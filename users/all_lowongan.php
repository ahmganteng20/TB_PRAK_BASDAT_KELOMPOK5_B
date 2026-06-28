<?php
include "../config/koneksi.php";
if(!isset($_SESSION)) session_start();
if(!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../auth/login.php"); exit;
}

$data = mysqli_query($conn, "SELECT low.*, p.nama_perusahaan, u.email FROM lowongan low LEFT JOIN perusahaan p ON low.id_perusahaan=p.id_perusahaan LEFT JOIN users u ON p.id_user=u.id_user ORDER BY low.id_lowongan DESC");
include "../components/header.php";
?>
<span class="h1-tema">Daftar Lowongan</span>
<table class="table-tema">
    <thead><tr><th>ID</th><th>Judul</th><th>Perusahaan</th><th>Kuota</th><th>Status</th><th>Action</th></tr></thead>
    <tbody>
    <?php if(mysqli_num_rows($data)==0): ?>
        <tr><td colspan="6" style="text-align:center;color:#999;padding:30px;">Belum ada lowongan.</td></tr>
    <?php endif; ?>
    <?php while($r=mysqli_fetch_assoc($data)): ?>
        <tr>
            <td><?= $r['id_lowongan']; ?></td>
            <td><?= htmlspecialchars($r['judul_lowongan']); ?></td>
            <td><?= htmlspecialchars($r['nama_perusahaan'] ?? '-'); ?> <br><small><?= htmlspecialchars($r['email'] ?? '-'); ?></small></td>
            <td><?= (int)$r['kuota']; ?></td>
            <td><?= htmlspecialchars($r['status']); ?></td>
            <td>
                <a class="btn-biru" href="../lowongan/edit.php?id=<?= $r['id_lowongan']; ?>">Edit</a>
                <a class="btn-bahaya" href="../lowongan/hapus.php?id=<?= $r['id_lowongan']; ?>" onclick="return confirm('Hapus lowongan?')">Hapus</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php include "../components/footer.php"; ?>
