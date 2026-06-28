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
    $kabupaten = mysqli_real_escape_string($conn, $_POST['kabupaten_kota']);
    $kode_pos = mysqli_real_escape_string($conn, $_POST['kode_pos']);
    $pendidikan = mysqli_real_escape_string($conn, $_POST['pendidikan']);
    $pengalaman = mysqli_real_escape_string($conn, $_POST['pengalaman']);
    $keahlian = mysqli_real_escape_string($conn, $_POST['keahlian']);

    // Validasi nomor telepon: hanya angka, spasi, plus dan minus
    if(!preg_match('/^[0-9+\s-]+$/', $hp)){
        echo "<script>alert('Nomor HP tidak valid. Gunakan hanya angka, spasi, + atau -'); window.location='profil.php';</script>";
        exit;
    }

    mysqli_query($conn, "ALTER TABLE pelamar ADD COLUMN IF NOT EXISTS kabupaten_kota VARCHAR(255) DEFAULT NULL");
    mysqli_query($conn, "ALTER TABLE pelamar ADD COLUMN IF NOT EXISTS kode_pos VARCHAR(20) DEFAULT NULL");
    mysqli_query($conn, "ALTER TABLE pelamar ADD COLUMN IF NOT EXISTS pendidikan VARCHAR(255) DEFAULT NULL");
    mysqli_query($conn, "ALTER TABLE pelamar ADD COLUMN IF NOT EXISTS pengalaman TEXT DEFAULT NULL");
    mysqli_query($conn, "ALTER TABLE pelamar ADD COLUMN IF NOT EXISTS keahlian TEXT DEFAULT NULL");
    mysqli_query($conn, "ALTER TABLE pelamar ADD COLUMN IF NOT EXISTS is_complete TINYINT(1) NOT NULL DEFAULT 0");

    $cek_data = mysqli_query($conn, "SELECT * FROM pelamar WHERE id_user='$id_user'");
    
    if(mysqli_num_rows($cek_data) > 0){
        mysqli_query($conn, "UPDATE pelamar SET nama_lengkap='$nama', no_hp='$hp', alamat='$alamat', kabupaten_kota='$kabupaten', kode_pos='$kode_pos', pendidikan='$pendidikan', pengalaman='$pengalaman', keahlian='$keahlian' WHERE id_user='$id_user'");
    } else {
        mysqli_query($conn, "INSERT INTO pelamar (id_user, nama_lengkap, no_hp, alamat, kabupaten_kota, kode_pos, pendidikan, pengalaman, keahlian) VALUES ('$id_user', '$nama', '$hp', '$alamat', '$kabupaten', '$kode_pos', '$pendidikan', '$pengalaman', '$keahlian')");
    }

    $is_complete = (!empty($nama) && !empty($hp) && !empty($alamat) && !empty($kabupaten) && !empty($kode_pos) && !empty($pendidikan) && !empty($pengalaman) && !empty($keahlian)) ? 1 : 0;
    mysqli_query($conn, "UPDATE pelamar SET is_complete='$is_complete' WHERE id_user='$id_user'");

    header("Location: profil.php?status=sukses");
    exit;
}

$data = mysqli_query($conn, "SELECT pel.*, u.email FROM users u LEFT JOIN pelamar pel ON u.id_user=pel.id_user WHERE u.id_user='$id_user'");
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
        <label style="font-weight: bold; color: #333; display: block; margin-bottom: 8px;">Kabupaten/Kota</label>
        <input type="text" name="kabupaten_kota" class="form-control" value="<?= htmlspecialchars($cek['kabupaten_kota'] ?? '') ?>" required>
    </div>

    <div style="margin-bottom: 15px;">
        <label style="font-weight: bold; color: #333; display: block; margin-bottom: 8px;">Kode Pos</label>
        <input type="text" name="kode_pos" class="form-control" value="<?= htmlspecialchars($cek['kode_pos'] ?? '') ?>" required>
    </div>

    <div style="margin-bottom: 15px;">
        <label style="font-weight: bold; color: #333; display: block; margin-bottom: 8px;">Pendidikan</label>
        <input type="text" name="pendidikan" class="form-control" value="<?= htmlspecialchars($cek['pendidikan'] ?? '') ?>" required>
    </div>

    <div style="margin-bottom: 15px;">
        <label style="font-weight: bold; color: #333; display: block; margin-bottom: 8px;">Pengalaman Kerja</label>
        <textarea name="pengalaman" rows="4" class="form-control" required><?= htmlspecialchars($cek['pengalaman'] ?? '') ?></textarea>
    </div>

    <div class="input-group-row">
        <div class="input-group-item">
            <label style="font-weight: bold; color: #333; display: block; margin-bottom: 8px;">Email</label>
            <input type="email" name="email" class="form-control readonly-input" value="<?= htmlspecialchars($cek['email'] ?? '') ?>" readonly>
            <div class="form-note">Email terhubung dengan akun Anda. Untuk mengubahnya, edit di halaman profil pengguna.</div>
        </div>
        <div class="input-group-item">
            <label style="font-weight: bold; color: #333; display: block; margin-bottom: 8px;">No HP</label>
            <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($cek['no_hp'] ?? '') ?>" required>
            <div class="form-note">Gunakan format angka, spasi, + atau -.</div>
        </div>
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