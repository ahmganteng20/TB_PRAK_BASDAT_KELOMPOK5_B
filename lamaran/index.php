<?php
include "../config/koneksi.php";

if(!isset($_SESSION['id_user'])){
    header("Location: ../auth/login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

// Ambil data riwayat lamaran khusus milik user yang sedang login
$data = mysqli_query(
    $conn,
    "SELECT lamaran.*, lowongan.judul_lowongan
     FROM lamaran
     JOIN pelamar ON lamaran.id_pelamar=pelamar.id_pelamar
     JOIN lowongan ON lamaran.id_lowongan=lowongan.id_lowongan
     WHERE pelamar.id_user='$id_user'
     ORDER BY lamaran.tanggal_lamar DESC"
);

// 1. PANGGIL HEADER GLOBAL
include "../components/header.php"; 
?>

<span class="h1-tema">Lamaran Saya</span>
<p style="color: #666; margin-bottom: 20px;">Berikut adalah status dan riwayat lowongan kerja yang telah Anda lamar:</p>

<table class="table-tema">
    <thead>
        <tr>
            <th>Judul Lowongan Kerja</th>
            <th>Tanggal Melamar</th>
            <th>Status Kelulusan</th>
        </tr>
    </thead>
    <tbody>
        <?php if(mysqli_num_rows($data) == 0): ?>
            <tr>
                <td colspan="3" style="text-align: center; color: #999; padding: 30px;">
                    Anda belum pernah melamar lowongan pekerjaan apa pun.
                </td>
            </tr>
        <?php endif; ?>

        <?php while($row = mysqli_fetch_assoc($data)){ ?>
            <tr>
                <td style="font-weight: bold; color: #333;"><?= htmlspecialchars($row['judul_lowongan']); ?></td>
                <td><?= date('d M Y, H:i', strtotime($row['tanggal_lamar'])); ?> WIB</td>
                <td>
                    <?php
                    // Pengondisian warna badge berdasarkan status lamaran pelamar
                    if(strtolower($row['status_lamaran']) == "pending"){
                        echo "<span style='background: #fff3cd; color: #856404; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; border: 1px solid #ffeeba;'>Pending</span>";
                    }
                    elseif(strtolower($row['status_lamaran']) == "diterima"){
                        echo "<span style='background: #d4edda; color: #155724; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; border: 1px solid #c3e6cb;'>Diterima</span>";
                    }
                    else{
                        echo "<span style='background: #f8d7da; color: #721c24; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; border: 1px solid #f5c6cb;'>Ditolak</span>";
                    }
                    ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<?php 
// 3. PANGGIL FOOTER GLOBAL
include "../components/footer.php"; 
?>