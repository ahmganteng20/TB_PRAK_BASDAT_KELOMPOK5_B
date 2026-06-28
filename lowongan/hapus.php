<?php

include "../config/koneksi.php";
if(!isset($_SESSION)) session_start();
if(!isset($_SESSION['id_user'])){ header("Location: ../auth/login.php"); exit; }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Hanya admin atau perusahaan pemilik lowongan yang boleh menghapus
$is_admin = ($_SESSION['role'] ?? '') === 'admin';
$id_user = $_SESSION['id_user'];

$owner_check = mysqli_query($conn, "SELECT id_perusahaan FROM lowongan WHERE id_lowongan='$id'");
$low = mysqli_fetch_assoc($owner_check);
$can_delete = false;
if($is_admin) $can_delete = true;
else if($low){
    $per = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_user FROM perusahaan WHERE id_perusahaan='".$low['id_perusahaan']."'"));
    if($per && $per['id_user'] == $id_user) $can_delete = true;
}

if($can_delete){
    mysqli_query($conn, "DELETE FROM lowongan WHERE id_lowongan='$id'");
}

header("Location: index.php");
exit;
?>