<?php
include "../config/koneksi.php";

if(!isset($_SESSION['id_user'])){
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

$data = mysqli_query(
    $conn,
    "SELECT
        lamaran.id_lamaran,
        pelamar.nama_lengkap,
        pelamar.no_hp,
        lowongan.judul_lowongan,
        lamaran.tanggal_lamar,
        lamaran.status_lamaran
    FROM lamaran
    JOIN pelamar ON lamaran.id_pelamar = pelamar.id_pelamar
    JOIN lowongan ON lamaran.id_lowongan = lowongan.id_lowongan
    WHERE lowongan.id_perusahaan='$id_perusahaan'
    ORDER BY lamaran.id_lamaran DESC"
);

// 1. PANGGIL HEADER TEMA GLOBAL
include "../components/header.php"; 
?>

<span class="h1-tema">Daftar Pelamar Masuk</span>
<p style="color: #666; margin-bottom: 25px;">Berikut adalah daftar kandidat yang melamar pada lowongan pekerjaan di perusahaan Anda.</p>

<table class="table-tema">
    <thead>
        <tr>
            <th>Nama Pelamar</th>
            <th>No. HP / WhatsApp</th>
            <th>Posisi Lowongan</th>
            <th>Tanggal Melamar</th>
            <th>Status Saat Ini</th>
            <th style="text-align: center;">Tindakan HRD</th>
        </tr>
    </thead>
    <tbody>
        <?php if(mysqli_num_rows($data) == 0): ?>
            <tr>
                <td colspan="6" style="text-align: center; color: #999; padding: 30px;">
                    Belum ada pelamar yang masuk untuk lowongan perusahaan Anda.
                </td>
            </tr>
        <?php endif; ?>

        <?php while($row = mysqli_fetch_assoc($data)){ ?>
        <tr>
            <td style="font-weight: bold; color: #333;"><?= htmlspecialchars($row['nama_lengkap']); ?></td>
            <td><?= htmlspecialchars($row['no_hp']); ?></td>
            <td><span style="background: #e9ecef; padding: 4px 8px; border-radius: 4px; font-weight: 500;"><?= htmlspecialchars($row['judul_lowongan']); ?></span></td>
            <td><?= date('d M Y, H:i', strtotime($row['tanggal_lamar'])); ?> WIB</td>
            <td>
                <?php
                if($row['status_lamaran'] == "pending"){
                    echo "<span style='background: #fff3cd; color: #856404; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: bold;'>Pending</span>";
                } elseif($row['status_lamaran'] == "diterima") {
                    echo "<span style='background: #d4edda; color: #155724; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: bold;'>Diterima</span>";
                } else {
                    echo "<span style='background: #f8d7da; color: #721c24; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: bold;'>Ditolak</span>";
                }
                ?>
            </td>
            <td style="text-align: center;">
                <?php if($row['status_lamaran'] == "pending"){ ?>
                    <a href="terima.php?id=<?= $row['id_lamaran']; ?>" class="btn-biru" style="background-color: #28a745; padding: 6px 12px; font-size: 13px;" onclick="return confirm('Terima pelamar ini?')">
                        Terima
                    </a>
                    <a href="tolak.php?id=<?= $row['id_lamaran']; ?>" class="btn-bahaya" style="padding: 6px 12px; font-size: 13px;" onclick="return confirm('Tolak pelamar ini?')">
                        Tolak
                    </a>
                <?php } else { ?>
                    <span style="color: #aaa; font-style: italic; font-size: 13px;">Selesai Diproses</span>
                <?php } ?>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<?php 
// 3. PANGGIL FOOTER TEMA GLOBAL
include "../components/footer.php"; 
?>