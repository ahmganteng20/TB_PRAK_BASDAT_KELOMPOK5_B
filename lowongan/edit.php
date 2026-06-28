<?php

include "../config/koneksi.php";

if(!isset($_SESSION)) session_start();
if(!isset($_SESSION['id_user'])){ header("Location: ../auth/login.php"); exit; }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$data = mysqli_query($conn, "SELECT * FROM lowongan WHERE id_lowongan='$id'");
$row = mysqli_fetch_assoc($data);

// Check permission: admin or owner company
$is_admin = ($_SESSION['role'] ?? '') === 'admin';
$id_user = $_SESSION['id_user'];
$can_edit = false;
if($is_admin) $can_edit = true;
else if($row){
    $per = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_user FROM perusahaan WHERE id_perusahaan='".$row['id_perusahaan']."'"));
    if($per && $per['id_user'] == $id_user) $can_edit = true;
}
if(!$can_edit){ header("Location: index.php"); exit; }

if(isset($_POST['update'])){

    $judul = $_POST['judul_lowongan'];
    $deskripsi = $_POST['deskripsi'];
    $kuota = $_POST['kuota'];
    $status = $_POST['status'];
    $pendidikan = $_POST['pendidikan'] ?? '';
    $pengalaman = $_POST['pengalaman'] ?? '';
    $skill = $_POST['skill'] ?? '';
    $usia = $_POST['usia'] ?? '';
    $lokasi = $_POST['lokasi'] ?? '';

    // Ensure columns exist
    mysqli_query($conn, "ALTER TABLE lowongan ADD COLUMN IF NOT EXISTS pendidikan VARCHAR(255) DEFAULT NULL");
    mysqli_query($conn, "ALTER TABLE lowongan ADD COLUMN IF NOT EXISTS pengalaman VARCHAR(100) DEFAULT NULL");
    mysqli_query($conn, "ALTER TABLE lowongan ADD COLUMN IF NOT EXISTS skill TEXT DEFAULT NULL");
    mysqli_query($conn, "ALTER TABLE lowongan ADD COLUMN IF NOT EXISTS usia VARCHAR(50) DEFAULT NULL");
    mysqli_query($conn, "ALTER TABLE lowongan ADD COLUMN IF NOT EXISTS lokasi VARCHAR(255) DEFAULT NULL");

    mysqli_query(
        $conn,
        "UPDATE lowongan
        SET
        judul_lowongan='$judul',
        deskripsi='$deskripsi',
        pendidikan='$pendidikan',
        pengalaman='$pengalaman',
        skill='$skill',
        usia='$usia',
        lokasi='$lokasi',
        kuota='$kuota',
        status='$status'
        WHERE id_lowongan='$id'"
    );

    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Lowongan</title>
<link rel="stylesheet"
href="../assets/css/style.css">
</head>

<body>

<div class="container">

<h1>Edit Lowongan</h1>

<div class="card">

<form method="POST">

<label>Judul</label>
<br>

<input
type="text"
name="judul_lowongan"
value="<?= $row['judul_lowongan']; ?>"
style="width:100%;padding:10px;">

<br><br>

<label>Deskripsi</label>
<br>

<textarea
name="deskripsi"
rows="5"
style="width:100%;padding:10px;"><?= $row['deskripsi']; ?></textarea>

<br><br>
<label>Pendidikan (minimal)</label>
<br>
<input type="text" name="pendidikan" value="<?= htmlspecialchars($row['pendidikan'] ?? '') ?>" style="width:100%;padding:10px;">

<br><br>
<label>Pengalaman (tahun)</label>
<br>
<input type="text" name="pengalaman" value="<?= htmlspecialchars($row['pengalaman'] ?? '') ?>" style="width:100%;padding:10px;">

<br><br>
<label>Skill yang Dibutuhkan</label>
<br>
<input type="text" name="skill" value="<?= htmlspecialchars($row['skill'] ?? '') ?>" style="width:100%;padding:10px;">

<br><br>
<label>Batas Usia (opsional)</label>
<br>
<input type="text" name="usia" value="<?= htmlspecialchars($row['usia'] ?? '') ?>" style="width:100%;padding:10px;">

<br><br>
<label>Lokasi Penempatan</label>
<br>
<input type="text" name="lokasi" value="<?= htmlspecialchars($row['lokasi'] ?? '') ?>" style="width:100%;padding:10px;">

<br><br>

<label>Kuota</label>
<br>

<input
type="number"
name="kuota"
value="<?= $row['kuota']; ?>"
style="width:100%;padding:10px;">

<br><br>

<label>Status</label>
<br>

<select
name="status"
style="width:100%;padding:10px;">

<option value="open"
<?= ($row['status']=='open')?'selected':''; ?>>
Open
</option>

<option value="closed"
<?= ($row['status']=='closed')?'selected':''; ?>>
Closed
</option>

</select>

<br><br>

<button name="update">
Update
</button>

</form>

</div>

</div>

</body>
</html>