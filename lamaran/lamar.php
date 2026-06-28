<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "../config/koneksi.php";

if(!isset($_SESSION['id_user'])){
    header("Location: ../auth/login.php");
    exit;
}

$id_lowongan = mysqli_real_escape_string($conn, $_GET['id']);
$id_user = $_SESSION['id_user'];

// Ambil data lowongan untuk ditampilkan ke pelamar (deskripsi, kuota, perusahaan)
 $q = mysqli_query($conn, "SELECT l.*, p.nama_perusahaan FROM lowongan l LEFT JOIN perusahaan p ON l.id_perusahaan=p.id_perusahaan WHERE l.id_lowongan='$id_lowongan'");
 if(!$q){
     error_log('DB Error: '.mysqli_error($conn).' -- Query: SELECT l.*, p.nama_perusahaan FROM lowongan l LEFT JOIN perusahaan p ON l.id_perusahaan=p.id_perusahaan WHERE l.id_lowongan=' . $id_lowongan);
     echo "<script>alert('Terjadi kesalahan database. Silakan coba lagi nanti.'); window.location='../lowongan/index.php';</script>";
     exit;
 }

 $low = mysqli_fetch_assoc($q);
 if(!$low){
    echo "<script>alert('Lowongan tidak ditemukan'); window.location='../lowongan/index.php';</script>";
    exit;
}

// Jika kuota habis atau status closed, larikan kembali
if((int)$low['kuota'] <= 0 || in_array(strtolower($low['status']), ['closed','tutup'])){
    echo "<script>alert('Maaf, lowongan sudah ditutup atau kuota telah habis'); window.location='../lowongan/index.php';</script>";
    exit;
}

// 1. Cari data profil pelamar berdasarkan id_user
 $cari = mysqli_query($conn, "SELECT id_pelamar, nama_lengkap, no_hp, alamat FROM pelamar WHERE id_user='$id_user'");
 if(!$cari){
     error_log('DB Error: '.mysqli_error($conn).' -- Query: SELECT id_pelamar, nama_lengkap, no_hp, alamat FROM pelamar WHERE id_user=' . $id_user);
     echo "<script>alert('Terjadi kesalahan database. Silakan coba lagi nanti.'); window.location='../pelamar/profil.php';</script>";
     exit;
 }
 $pelamar = mysqli_fetch_assoc($cari);

// VALIDASI: Jika data pelamar tidak ditemukan di database
if(!$pelamar){
    echo "<script>
        alert('Gagal melamar! Akun Anda belum melengkapi profil pelamar atau role Anda bukan pelamar. Silakan lengkapi profil.');
        window.location.href = '../pelamar/profil.php';
    </script>";
    exit;
}

// Pastikan profil pelamar minimal lengkap untuk lamaran
$pelamar_lengkap = !empty(trim($pelamar['nama_lengkap'] ?? ''))
    && !empty(trim($pelamar['no_hp'] ?? ''))
    && !empty(trim($pelamar['alamat'] ?? ''));

if(!$pelamar_lengkap){
    echo "<script>
        alert('Silakan lengkapi profil Anda sebelum mengajukan lamaran (Nama, No HP, Alamat).');
        window.location.href = '../pelamar/profil.php';
    </script>";
    exit;
}

$id_pelamar = $pelamar['id_pelamar'];

// 2. CEK STATUS LOWONGAN TERLEBIH DAHULU (Mendukung validasi open / closed / Buka)
 $query_lowongan = mysqli_query($conn, "SELECT status FROM lowongan WHERE id_lowongan='$id_lowongan'");
 if(!$query_lowongan){
     error_log('DB Error: '.mysqli_error($conn).' -- Query: SELECT status FROM lowongan WHERE id_lowongan=' . $id_lowongan);
     echo "<script>alert('Terjadi kesalahan database. Silakan coba lagi nanti.'); window.location='../lowongan/index.php';</script>";
     exit;
 }
 $lowongan = mysqli_fetch_assoc($query_lowongan);

 if(!$lowongan || in_array(strtolower($lowongan['status']), ['closed', 'tutup'])) {
    echo "<script>
        alert('Maaf, kuota lowongan ini sudah penuh atau sudah ditutup!');
        window.location.href = '../lowongan/index.php';
    </script>";
    exit;
}

// Jika request POST maka proses pengiriman lamaran
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // 3. VALIDASI: Cek apakah pelamar sudah pernah melamar di lowongan ini
    $cek = mysqli_query($conn, "SELECT * FROM lamaran WHERE id_pelamar='$id_pelamar' AND id_lowongan='$id_lowongan'");
    if(!$cek){
        error_log('DB Error: '.mysqli_error($conn).' -- Query: SELECT * FROM lamaran WHERE id_pelamar=' . $id_pelamar . ' AND id_lowongan=' . $id_lowongan);
        echo "<script>alert('Terjadi kesalahan database. Silakan coba lagi nanti.'); window.location='../lowongan/index.php';</script>";
        exit;
    }
    if(mysqli_num_rows($cek) > 0){
        echo "<script>alert('Anda sudah pernah melamar lowongan ini'); window.location='../lowongan/index.php';</script>";
        exit;
    }

    // Pastikan kolom cv_path ada dan catatan/hr_note
    mysqli_query($conn, "ALTER TABLE lamaran ADD COLUMN IF NOT EXISTS cv_path VARCHAR(255) DEFAULT NULL");
    mysqli_query($conn, "ALTER TABLE lamaran ADD COLUMN IF NOT EXISTS catatan_hr TEXT DEFAULT NULL");

    // Handle file upload (CV)
    $cv_path = NULL;
    if(isset($_FILES['cv']) && $_FILES['cv']['error'] !== UPLOAD_ERR_NO_FILE){
        $allowed = ['application/pdf','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if($_FILES['cv']['error'] !== UPLOAD_ERR_OK){
            echo "<script>alert('Upload CV gagal.'); window.location='lamar.php?id=$id_lowongan';</script>"; exit;
        }
        if(!in_array($_FILES['cv']['type'], $allowed)){
            echo "<script>alert('Tipe file tidak diperbolehkan. Gunakan PDF/DOC/DOCX.'); window.location='lamar.php?id=$id_lowongan';</script>"; exit;
        }
        if($_FILES['cv']['size'] > 2 * 1024 * 1024){
            echo "<script>alert('Ukuran file maksimal 2MB.'); window.location='lamar.php?id=$id_lowongan';</script>"; exit;
        }

        $destDir = __DIR__ . '/../uploads/cv/';
        if(!is_dir($destDir)) mkdir($destDir, 0755, true);
        $ext = pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION);
        $filename = 'cv_' . $id_pelamar . '_' . time() . '.' . $ext;
        $dest = $destDir . $filename;
        if(move_uploaded_file($_FILES['cv']['tmp_name'], $dest)){
            $cv_path = 'uploads/cv/' . $filename;
        }
    }

    // Insert lamaran including CV path
    $insert = mysqli_query($conn, "INSERT INTO lamaran (id_pelamar, id_lowongan, tanggal_lamar, status_lamaran, cv_path) VALUES ('$id_pelamar', '$id_lowongan', NOW(), 'pending', '" . mysqli_real_escape_string($conn, $cv_path) . "')");

    if($insert){
        // Kurangi kuota secara eksplisit karena trigger mungkin tidak ada
        mysqli_query($conn, "UPDATE lowongan SET kuota = GREATEST(0, kuota - 1) WHERE id_lowongan='$id_lowongan'");

        // Jika kuota sekarang 0, ubah status menjadi Closed agar tidak tampil di pelamar
        mysqli_query($conn, "UPDATE lowongan SET status='Closed' WHERE id_lowongan='$id_lowongan' AND kuota<=0");

        header("Location: index.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Tampilkan halaman konfirmasi lamar dengan deskripsi lowongan
include "../components/header.php";
?>

<span class="h1-tema">Konfirmasi Lamar</span>
<div style="background: #fff; padding: 18px; border-radius: 6px; margin-bottom: 20px;">
    <h3 style="margin-top:0;"><?= htmlspecialchars($low['judul_lowongan']); ?></h3>
    <p style="color: #666;">Perusahaan: <?= htmlspecialchars($low['nama_perusahaan'] ?? ''); ?></p>
    <p style="white-space: pre-line; color: #333;"><?= htmlspecialchars($low['deskripsi']); ?></p>

    <div style="margin-top:18px; padding:16px; background:#f8f9fa; border-radius:6px;">
        <h4 style="margin:0 0 12px; color:#1e3d73;">Persyaratan Pekerjaan</h4>
        <p style="margin:0 0 8px;"><strong>Pendidikan minimal:</strong> <?= htmlspecialchars($low['pendidikan'] ?? '-'); ?></p>
        <p style="margin:0 0 8px;"><strong>Pengalaman:</strong> <?= htmlspecialchars($low['pengalaman'] ?? '-'); ?></p>
        <p style="margin:0 0 8px;"><strong>Skill yang dibutuhkan:</strong> <?= nl2br(htmlspecialchars($low['skill'] ?? '-')); ?></p>
        <p style="margin:0 0 8px;"><strong>Batas usia:</strong> <?= htmlspecialchars($low['usia'] ?? '-'); ?></p>
        <p style="margin:0 0 8px;"><strong>Lokasi penempatan:</strong> <?= htmlspecialchars($low['lokasi'] ?? '-'); ?></p>
        
    </div>

    <form method="POST" enctype="multipart/form-data" style="margin-top:20px;">
        <p style="color: #666;">Klik tombol "Kirim Lamaran" untuk mengajukan lamaran Anda.</p>
        <div style="margin-bottom:12px;">
            <label style="font-weight:bold;">Unggah CV (PDF/DOC/DOCX, max 2MB)</label>
            <input type="file" name="cv" accept=".pdf,.doc,.docx" required>
        </div>
        <button type="submit" class="btn-biru" style="padding:10px 16px;">Kirim Lamaran</button>
        <a href="../lowongan/index.php" class="btn-bahaya" style="background-color:#6c757d; margin-left:10px; padding:10px 16px;">Batal</a>
    </form>
</div>

<?php
include "../components/footer.php";
?>