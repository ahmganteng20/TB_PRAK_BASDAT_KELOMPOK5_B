<?php
include "../config/koneksi.php";

// Proteksi halaman: jika belum login, kembalikan ke login
if(!isset($_SESSION['id_user'])){
    header("Location: ../auth/login.php");
    exit;
}

// OPTIMASI: Langsung ambil role dari session login, tidak perlu query ulang ke database
$role = $_SESSION['role'];

// Ambil semua data lowongan diurutkan dari yang terbaru
if($role == 'pelamar'){
    $data = mysqli_query($conn, "SELECT * FROM lowongan WHERE (LOWER(status)='open' OR status='Open') AND kuota>0 ORDER BY id_lowongan DESC");
} elseif($role == 'perusahaan'){
    $user_id = mysqli_real_escape_string($conn, $_SESSION['id_user']);
    $company = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_perusahaan FROM perusahaan WHERE id_user='$user_id' LIMIT 1"));
    if($company){
        $company_id = (int)$company['id_perusahaan'];
        $data = mysqli_query($conn, "SELECT * FROM lowongan WHERE id_perusahaan='$company_id' ORDER BY id_lowongan DESC");
    } else {
        $data = mysqli_query($conn, "SELECT * FROM lowongan WHERE 1=0");
    }
} else {
    $data = mysqli_query($conn, "SELECT * FROM lowongan ORDER BY id_lowongan DESC");
}

// 1. PANGGIL HEADER GLOBAL
include "../components/header.php"; 
?>

<span class="h1-tema">Data Lowongan Kerja</span>

<?php if($role == "perusahaan"){ ?>
    <div style="margin-bottom: 20px;">
        <a class="btn-biru" href="tambah.php">+ Tambah Lowongan Baru</a>
    </div>
<?php } ?>

<table class="table-tema">
    <thead>
        <tr>
            <th>ID</th>
            <th>Judul Lowongan</th>
            <th>Kuota Terbuka</th>
            <th>Status</th>
            <th>Tanggal Posting</th>
            <?php if($role == "pelamar"){ ?>
                <th>Aksi</th>
            <?php } ?>
            <?php if($role == "perusahaan"){ ?>
                <th>Manajemen Aksi</th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <?php while($row = mysqli_fetch_assoc($data)){ ?>
            <tr>
                <td><?= $row['id_lowongan']; ?></td>
                <td style="font-weight: bold; color: #333;"><?= htmlspecialchars($row['judul_lowongan']); ?></td>
                <td><?= $row['kuota']; ?> Orang</td>
                <td>
                    <?php if(strtolower($row['status']) == 'open' || $row['status'] == 'Buka'): ?>
                        <span style="background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">Buka</span>
                    <?php else: ?>
                        <span style="background: #f8d7da; color: #721c24; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">Tutup</span>
                    <?php endif; ?>
                </td>
                <td><?= date('d M Y', strtotime($row['tanggal_posting'])); ?></td>
                
                <?php if($role == "pelamar"){ ?>
                    <td>
                        <?php if(strtolower($row['status']) == 'open' || $row['status'] == 'Buka'){ ?>
                            <a class="btn-biru" href="../lamaran/lamar.php?id=<?= $row['id_lowongan']; ?>">
                                Lamar Pekerjaan
                            </a>
                        <?php } else { ?>
                            <button class="btn-bahaya" style="background-color: #6c757d; cursor: not-allowed;" disabled>
                                Kuota Penuh
                            </button>
                        <?php } ?>
                    </td>
                <?php } ?>
                
                <?php if($role == "perusahaan"){ ?>
                    <td>
                        <a class="btn-biru" style="background-color: #ffc107; color: #212529;" href="edit.php?id=<?= $row['id_lowongan']; ?>">Edit</a>
                        <a class="btn-bahaya" href="hapus.php?id=<?= $row['id_lowongan']; ?>" onclick="return confirm('Yakin ingin menghapus lowongan ini?')">Hapus</a>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
    </tbody>
</table>

<?php 
// 3. PANGGIL FOOTER GLOBAL
include "../components/footer.php"; 
?>