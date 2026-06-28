<?php
include "../config/koneksi.php";
if(!isset($_SESSION)) session_start();

if(!isset($_SESSION['id_user'])){ header("Location: ../auth/login.php"); exit; }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil lamaran dan cek kepemilikan perusahaan
$q = mysqli_query($conn, "SELECT l.*, low.id_perusahaan FROM lamaran l JOIN lowongan low ON l.id_lowongan=low.id_lowongan WHERE l.id_lamaran='$id'");
$lam = mysqli_fetch_assoc($q);
if(!$lam){ header("Location: daftar_pelamar.php"); exit; }

$is_admin = ($_SESSION['role'] ?? '') === 'admin';
$is_owner = false;
if(!$is_admin){
    $per = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_user FROM perusahaan WHERE id_perusahaan='".$lam['id_perusahaan']."'"));
    if($per && $per['id_user'] == $_SESSION['id_user']) $is_owner = true;
}

if(!$is_admin && !$is_owner){ header("Location: daftar_pelamar.php"); exit; }

// Pastikan kolom catatan_hr ada
mysqli_query($conn, "ALTER TABLE lamaran ADD COLUMN IF NOT EXISTS catatan_hr TEXT DEFAULT NULL");

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $raw_status = mysqli_real_escape_string($conn, $_POST['status']);
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);

    // Normalize status to simplified Indonesian values for applicant view
    // Map accepted states to 'diterima', rejected states to 'ditolak', else keep 'pending'
    $s = strtolower(trim($raw_status));
    if(in_array($s, ['hired','offered','diterima','accepted'])){
        $new_status = 'diterima';
    } elseif(in_array($s, ['rejected','ditolak'])){
        $new_status = 'ditolak';
    } else {
        // treat other intermediate statuses as pending for applicant-facing view
        $new_status = 'pending';
    }

    mysqli_query($conn, "UPDATE lamaran SET status_lamaran='".mysqli_real_escape_string($conn,$new_status)."', catatan_hr='$catatan' WHERE id_lamaran='$id'");

    // If company rejects candidate, restore one kuota (applicant freed a reserved slot)
    if($new_status === 'ditolak'){
        mysqli_query($conn, "UPDATE lowongan SET kuota = kuota + 1 WHERE id_lowongan='".$lam['id_lowongan']."'");
        // Ensure lowongan marked open if kuota > 0
        mysqli_query($conn, "UPDATE lowongan SET status='Open' WHERE id_lowongan='".$lam['id_lowongan']."' AND kuota>0");
    }

    // Note: kuota is decremented upon application; do not decrement again here to avoid double-counting.

    header("Location: daftar_pelamar.php"); exit;
}

include "../components/header.php";
?>

<span class="h1-tema">Ubah Status Lamaran</span>
<form method="POST">
    <div style="margin-bottom:12px;"><label>Status</label>
        <select name="status" class="form-control">
            <option value="hired" <?= ($lam['status_lamaran']=='hired')?'selected':''; ?>>Hired</option>
            <option value="rejected" <?= ($lam['status_lamaran']=='rejected')?'selected':''; ?>>Rejected</option>
        </select>
    </div>
    <div style="margin-bottom:12px;"><label>Catatan HR</label>
        <textarea name="catatan" rows="4" class="form-control"><?= htmlspecialchars($lam['catatan_hr'] ?? '') ?></textarea>
    </div>
    <button type="submit" class="btn-biru">Simpan</button>
    <a href="daftar_pelamar.php" class="btn-bahaya" style="background-color:#6c757d; margin-left:8px;">Batal</a>
</form>

<?php include "../components/footer.php"; ?>
