<?php
include "../config/koneksi.php";

if(!isset($_SESSION['id_user'])){
    header("Location: ../auth/login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

if(isset($_POST['simpan'])){
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

    $cek_data = mysqli_query($conn, "SELECT * FROM pelamar WHERE id_user='$id_user'");
    
    if(mysqli_num_rows($cek_data) > 0){
        mysqli_query($conn, "UPDATE pelamar SET nama_lengkap='$nama', no_hp='$hp', alamat='$alamat' WHERE id_user='$id_user'");
    } else {
        mysqli_query($conn, "INSERT INTO pelamar (id_user, nama_lengkap, no_hp, alamat) VALUES ('$id_user', '$nama', '$hp', '$alamat')");
    }

    header("Location: profil.php?status=sukses");
    exit;
}

$data = mysqli_query($conn, "SELECT * FROM pelamar WHERE id_user='$id_user'");
$cek = mysqli_fetch_assoc($data);

// 1. MEMANGGIL HEADER GLOBAL (Otomatis load CSS Tema, Navbar, Container, dan Card Putih)
include "../components/header.php"; 
?>

<span class="h1-tema">Profil Pelamar</span>

<?php if(isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
    <div style="background-color: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 15px; border: 1px solid #c3e6cb;">
        ✓ Profil Anda berhasil diperbarui!
    </div>
<?php endif; ?>

<form method="POST">
    <div style="margin-bottom: 15px;">
        <label style="font-weight: bold; color: #333; display: block; margin-bottom: 8px;">Nama Lengkap</label>
        <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($cek['nama_lengkap'] ?? '') ?>" required>
    </div>

    <div style="margin-bottom: 15px;">
        <label style="font-weight: bold; color: #333; display: block; margin-bottom: 8px;">No HP</label>
        <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($cek['no_hp'] ?? '') ?>" required>
    </div>

    <div style="margin-bottom: 20px;">
        <label style="font-weight: bold; color: #333; display: block; margin-bottom: 8px;">Alamat</label>
        <textarea name="alamat" rows="4" class="form-control" required><?= htmlspecialchars($cek['alamat'] ?? '') ?></textarea>
    </div>

    <button type="submit" name="simpan" style="background-color: #007bff; color: white; border: none; padding: 10px 20px; font-size: 14px; border-radius: 4px; cursor: pointer; font-weight: bold;">
        Simpan Perubahan
    </button>
</form>

<?php 
// 3. MEMANGGIL FOOTER GLOBAL (Menutup Card & Container secara otomatis)
include "../components/footer.php"; 
?>