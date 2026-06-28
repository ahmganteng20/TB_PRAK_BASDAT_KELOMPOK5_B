<?php
include "../config/koneksi.php";

if(!isset($_SESSION['id_user'])){
    header("Location: ../auth/login.php");
    exit;
}

if(isset($_POST['simpan'])){
    $id_user = $_SESSION['id_user'];

    $perusahaan = mysqli_fetch_assoc(
        mysqli_query(
            $conn,
            "SELECT id_perusahaan, telepon, alamat FROM perusahaan WHERE id_user='$id_user'"
        )
    );

    if(!$perusahaan){
        echo "<script>
        alert('Lengkapi profil perusahaan terlebih dahulu');
        window.location='../perusahaan/profil.php';
        </script>";
        exit;
    }

    // Pastikan profil perusahaan lengkap (telepon & alamat)
    if(empty($perusahaan['telepon']) || empty($perusahaan['alamat'])){
        echo "<script>
        alert('Lengkapi data telepon dan alamat perusahaan di profil sebelum menambah lowongan.');
        window.location='../perusahaan/profil.php';
        </script>";
        exit;
    }

    $id_perusahaan = $perusahaan['id_perusahaan'];
    $judul = mysqli_real_escape_string($conn, $_POST['judul_lowongan']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $pendidikan = mysqli_real_escape_string($conn, $_POST['pendidikan']);
    $pengalaman = mysqli_real_escape_string($conn, $_POST['pengalaman']);
    $skill = mysqli_real_escape_string($conn, $_POST['skill']);
    $usia = mysqli_real_escape_string($conn, $_POST['usia']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $kuota = mysqli_real_escape_string($conn, $_POST['kuota']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Pastikan kolom terstruktur ada
    mysqli_query($conn, "ALTER TABLE lowongan ADD COLUMN IF NOT EXISTS pendidikan VARCHAR(255) DEFAULT NULL");
    mysqli_query($conn, "ALTER TABLE lowongan ADD COLUMN IF NOT EXISTS pengalaman VARCHAR(100) DEFAULT NULL");
    mysqli_query($conn, "ALTER TABLE lowongan ADD COLUMN IF NOT EXISTS skill TEXT DEFAULT NULL");
    mysqli_query($conn, "ALTER TABLE lowongan ADD COLUMN IF NOT EXISTS usia VARCHAR(50) DEFAULT NULL");
    mysqli_query($conn, "ALTER TABLE lowongan ADD COLUMN IF NOT EXISTS lokasi VARCHAR(255) DEFAULT NULL");

    $simpan = mysqli_query(
        $conn,
        "INSERT INTO lowongan (id_perusahaan, judul_lowongan, deskripsi, pendidikan, pengalaman, skill, usia, lokasi, kuota, status, tanggal_posting)
         VALUES ('$id_perusahaan', '$judul', '$deskripsi', '$pendidikan', '$pengalaman', '$skill', '$usia', '$lokasi', '$kuota', '$status', NOW())"
    );

    if($simpan){
        echo "<script>
        alert('Lowongan berhasil ditambahkan');
        window.location='index.php';
        </script>";
        exit;
    }
}

// 1. PANGGIL HEADER TEMA GLOBAL
include "../components/header.php"; 
?>

<span class="h1-tema">Tambah Lowongan Baru</span>
<p style="color: #666; margin-bottom: 25px;">Silakan isi formulir di bawah ini untuk membuka lowongan pekerjaan baru.</p>

<form method="POST">
    <div style="margin-bottom: 15px;">
        <label style="font-weight: bold; color: #333; display: block; margin-bottom: 8px;">Judul Lowongan</label>
        <input type="text" name="judul_lowongan" class="form-control" placeholder="Contoh: Web Developer" required>
    </div>

    <div style="margin-bottom: 15px;">
        <label style="font-weight: bold; color: #333; display: block; margin-bottom: 8px;">Deskripsi Pekerjaan</label>
        <textarea name="deskripsi" rows="5" class="form-control" placeholder="Tuliskan syarat dan tanggung jawab pekerjaan..." required></textarea>
    </div>

    <div style="margin-bottom: 15px;">
        <label style="font-weight: bold; color: #333; display: block; margin-bottom: 8px;">Pendidikan (minimal)</label>
        <input type="text" name="pendidikan" class="form-control" placeholder="Contoh: S1 Teknik Informatika" required>
    </div>

    <div style="margin-bottom: 15px;">
        <label style="font-weight: bold; color: #333; display: block; margin-bottom: 8px;">Pengalaman (tahun)</label>
        <input type="text" name="pengalaman" class="form-control" placeholder="Contoh: 2-3 tahun" required>
    </div>

    <div style="margin-bottom: 15px;">
        <label style="font-weight: bold; color: #333; display: block; margin-bottom: 8px;">Skill yang Dibutuhkan</label>
        <input type="text" name="skill" class="form-control" placeholder="Contoh: PHP, MySQL, Javascript" required>
    </div>

    <div style="margin-bottom: 15px;">
        <label style="font-weight: bold; color: #333; display: block; margin-bottom: 8px;">Batas Usia (opsional)</label>
        <input type="text" name="usia" class="form-control" placeholder="Contoh: 25-35">
    </div>

    <div style="margin-bottom: 15px;">
        <label style="font-weight: bold; color: #333; display: block; margin-bottom: 8px;">Lokasi Penempatan</label>
        <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Jakarta" required>
    </div>

    <div style="margin-bottom: 15px;">
        <label style="font-weight: bold; color: #333; display: block; margin-bottom: 8px;">Jumlah Kuota Pelamar</label>
        <input type="number" name="kuota" class="form-control" min="1" placeholder="Contoh: 5" required>
    </div>

    <div style="margin-bottom: 25px;">
        <label style="font-weight: bold; color: #333; display: block; margin-bottom: 8px;">Status Lowongan</label>
        <select name="status" class="form-control">
            <option value="Open">Open (Buka)</option>
            <option value="Closed">Closed (Tutup)</option>
        </select>
    </div>

    <div style="display: flex; gap: 10px;">
        <button type="submit" name="simpan" class="btn-biru">
            Simpan & Publish
        </button>
        <a href="index.php" class="btn-bahaya" style="background-color: #6c757d;">Batal</a>
    </div>
</form>

<?php 
// 3. PANGGIL FOOTER TEMA GLOBAL
include "../components/footer.php"; 
?>