<?php
include "../config/koneksi.php";

if(!isset($_SESSION['id_user']) || ($_SESSION['role'] ?? '') !== 'perusahaan'){
    header("Location: ../auth/login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

$perusahaan = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT * FROM perusahaan WHERE id_user='$id_user'"
    )
);

if(!$perusahaan){
    echo "<script>
    alert('Silakan lengkapi profil perusahaan terlebih dahulu.');
    window.location='../perusahaan/profil.php';
    </script>";
    exit;
}

$id_perusahaan = $perusahaan['id_perusahaan'];

// Ensure optional columns exist
$cv_column = mysqli_query($conn, "SHOW COLUMNS FROM lamaran LIKE 'cv_path'");
if(!$cv_column || mysqli_num_rows($cv_column) === 0){
    mysqli_query($conn, "ALTER TABLE lamaran ADD COLUMN cv_path VARCHAR(255) DEFAULT NULL");
}
$note_column = mysqli_query($conn, "SHOW COLUMNS FROM lamaran LIKE 'catatan_hr'");
if(!$note_column || mysqli_num_rows($note_column) === 0){
    mysqli_query($conn, "ALTER TABLE lamaran ADD COLUMN catatan_hr TEXT DEFAULT NULL");
}

$data = mysqli_query(
    $conn,
    "SELECT
        lamaran.id_lamaran,
        pelamar.nama_lengkap,
        pelamar.no_hp,
        pelamar.alamat AS alamat_pelamar,
        lamaran.cv_path,
        lamaran.catatan_hr,
        lowongan.judul_lowongan,
        lamaran.tanggal_lamar,
        lamaran.status_lamaran
    FROM lamaran
    JOIN pelamar ON lamaran.id_pelamar = pelamar.id_pelamar
    JOIN lowongan ON lamaran.id_lowongan = lowongan.id_lowongan
    WHERE lowongan.id_perusahaan='$id_perusahaan'
    ORDER BY lamaran.id_lamaran DESC"
);

if(!$data){
    echo "<div style='padding:20px; background:#f8d7da; color:#721c24; border-radius:8px; margin:20px;'>";
    echo "<strong>Terjadi kesalahan query:</strong> " . htmlspecialchars(mysqli_error($conn));
    echo "</div>";
    include "../components/footer.php";
    exit;
}

include "../components/header.php";
?>

<span class="h1-tema">Daftar Pelamar Masuk</span>
<p style="color: #666; margin-bottom: 25px;">Berikut adalah daftar kandidat yang melamar pada lowongan pekerjaan di perusahaan Anda.</p>

<table class="table-tema">
    <thead>
        <tr>
            <th>Nama Pelamar</th>
            <th>No. HP / WhatsApp</th>
            <th>CV / Catatan</th>
            <th>Posisi Lowongan</th>
            <th>Tanggal Melamar</th>
            <th>Status Saat Ini</th>
            <th style="text-align: center;">Tindakan HRD</th>
        </tr>
    </thead>
    <tbody>
        <?php if(mysqli_num_rows($data) == 0): ?>
            <tr>
                <td colspan="7" style="text-align: center; color: #999; padding: 30px;">Belum ada pelamar yang masuk untuk lowongan perusahaan Anda.</td>
            </tr>
        <?php endif; ?>

        <?php while($row = mysqli_fetch_assoc($data)): ?>
        <tr>
            <td style="font-weight: bold; color: #333;"><?= htmlspecialchars($row['nama_lengkap']); ?></td>
            <td><?= htmlspecialchars($row['no_hp']); ?><br><small style="color:#666;"><?= htmlspecialchars($row['alamat_pelamar'] ?? '-'); ?></small></td>
            <td>
                <?php if(!empty($row['cv_path'])): ?>
                    <a href="/LokerIn/<?= htmlspecialchars($row['cv_path']); ?>" target="_blank">Lihat CV</a><br>
                <?php else: ?>
                    -
                <?php endif; ?>
                <br>
                <small style="color:#666;"><?= htmlspecialchars($row['catatan_hr'] ?? '-'); ?></small>
            </td>
            <td><span style="background: #e9ecef; padding: 4px 8px; border-radius: 4px; font-weight: 500;"><?= htmlspecialchars($row['judul_lowongan']); ?></span></td>
            <td><?= date('d M Y, H:i', strtotime($row['tanggal_lamar'])); ?> WIB</td>
            <td>
                <?php $s = strtolower($row['status_lamaran'] ?? ''); ?>
                <?php if(in_array($s, ['pending','applied','screened'])): ?>
                    <span class="badge pending">Pending</span>
                <?php elseif($s === 'interview'): ?>
                    <span class="badge interview">Interview</span>
                <?php elseif($s === 'offered'): ?>
                    <span class="badge offered">Offered</span>
                <?php elseif(in_array($s, ['hired','diterima'])): ?>
                    <span class="badge diterima">Hired</span>
                <?php elseif(in_array($s, ['rejected','ditolak'])): ?>
                    <span class="badge ditolak">Rejected</span>
                <?php else: ?>
                    <span class="badge" style="background:#e2e3e5;color:#383d41;"><?= htmlspecialchars($s ? ucfirst($s) : 'Unknown'); ?></span>
                <?php endif; ?>
            </td>
            <td style="text-align: center;"><a href="update_status.php?id=<?= $row['id_lamaran']; ?>" class="btn-biru" style="padding:6px 12px;">Ubah Status</a></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php
include "../components/footer.php";
?>