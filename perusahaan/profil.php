<?php
include "../config/koneksi.php";

if(!isset($_SESSION['id_user'])){
    header("Location: ../auth/login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

if(isset($_POST['simpan'])){
    $nama = mysqli_real_escape_string($conn, $_POST['nama_perusahaan']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $telepon = mysqli_real_escape_string($conn, $_POST['telepon']);

        // Validasi nomor telepon perusahaan: hanya angka, spasi, + dan -
        if(!preg_match('/^[0-9+\s-]+$/', $telepon)){
            echo "<script>alert('Nomor telepon tidak valid. Gunakan hanya angka, spasi, + atau -'); window.location='profil.php';</script>";
            exit;
        }

    $cek = mysqli_query($conn, "SELECT * FROM perusahaan WHERE id_user='$id_user'");

    if(mysqli_num_rows($cek) > 0){
        mysqli_query($conn, "UPDATE perusahaan SET nama_perusahaan='$nama', alamat='$alamat', telepon='$telepon' WHERE id_user='$id_user'");
    } else {
        mysqli_query($conn, "INSERT INTO perusahaan (id_user, nama_perusahaan, alamat, telepon) VALUES ('$id_user', '$nama', '$alamat', '$telepon')");
    }

    header("Location: profil.php?status=sukses");
    exit;
}

$query_data = mysqli_query($conn, "SELECT * FROM perusahaan WHERE id_user='$id_user'");
$data = mysqli_fetch_assoc($query_data);

// 1. PANGGIL HEADER TEMA GLOBAL
include "../components/header.php"; 
?>

<span class="h1-tema">Profil Perusahaan</span>
<p style="color: #666; margin-bottom: 25px;">Lengkapi informasi detail perusahaan Anda agar para pelamar dapat mengetahui profil instansi Anda.</p>

<?php if(isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
    <div style="background-color: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #c3e6cb; font-size: 14px;">
        ✓ Profil perusahaan Anda berhasil diperbarui!
    </div>
<?php endif; ?>

<form method="POST">
    <div style="margin-bottom: 15px;">
        <label style="font-weight: bold; color: #333; display: block; margin-bottom: 8px;">Nama Perusahaan</label>
        <input type="text" name="nama_perusahaan" class="form-control" value="<?= htmlspecialchars($data['nama_perusahaan'] ?? ''); ?>" required>
    </div>

    <div style="margin-bottom: 15px;">
        <label style="font-weight: bold; color: #333; display: block; margin-bottom: 8px;">No. Telepon Instansi</label>
        <input type="text" name="telepon" class="form-control" value="<?= htmlspecialchars($data['telepon'] ?? ''); ?>" required>
    </div>

    <div style="margin-bottom: 25px;">
        <label style="font-weight: bold; color: #333; display: block; margin-bottom: 8px;">Alamat Kantor Pusat</label>
        <textarea name="alamat" rows="4" class="form-control" required><?= htmlspecialchars($data['alamat'] ?? ''); ?></textarea>
    </div>

    <button type="submit" name="simpan" class="btn-biru">
        Simpan Profil Perusahaan
    </button>
</form>

<?php 
// 3. PANGGIL FOOTER TEMA GLOBAL
include "../components/footer.php"; 
?>