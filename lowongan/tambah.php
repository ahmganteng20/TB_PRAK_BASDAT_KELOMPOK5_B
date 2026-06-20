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
            "SELECT id_perusahaan FROM perusahaan WHERE id_user='$id_user'"
        )
    );

    if(!$perusahaan){
        echo "<script>
        alert('Lengkapi profil perusahaan terlebih dahulu');
        window.location='../perusahaan/profil.php';
        </script>";
        exit;
    }

    $id_perusahaan = $perusahaan['id_perusahaan'];
    $judul = mysqli_real_escape_string($conn, $_POST['judul_lowongan']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $kuota = mysqli_real_escape_string($conn, $_POST['kuota']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $simpan = mysqli_query(
        $conn,
        "INSERT INTO lowongan (id_perusahaan, judul_lowongan, deskripsi, kuota, status, tanggal_posting)
         VALUES ('$id_perusahaan', '$judul', '$deskripsi', '$kuota', '$status', NOW())"
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