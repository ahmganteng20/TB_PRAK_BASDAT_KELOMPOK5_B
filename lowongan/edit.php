<?php

include "../config/koneksi.php";

$id = $_GET['id'];

$data = mysqli_query(
    $conn,
    "SELECT *
    FROM lowongan
    WHERE id_lowongan='$id'"
);

$row = mysqli_fetch_assoc($data);

if(isset($_POST['update'])){

    $judul = $_POST['judul_lowongan'];
    $deskripsi = $_POST['deskripsi'];
    $kuota = $_POST['kuota'];
    $status = $_POST['status'];

    mysqli_query(
        $conn,
        "UPDATE lowongan
        SET
        judul_lowongan='$judul',
        deskripsi='$deskripsi',
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