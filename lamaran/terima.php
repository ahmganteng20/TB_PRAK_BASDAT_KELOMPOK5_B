<?php
include "../config/koneksi.php";

$id = $_GET['id'];

mysqli_query(
    $conn,
    "UPDATE lamaran
    SET status_lamaran='diterima'
    WHERE id_lamaran='$id'"
);

header("Location: daftar_pelamar.php");
exit;
?>