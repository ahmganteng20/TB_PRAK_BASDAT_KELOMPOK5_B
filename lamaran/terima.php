<?php
include "../config/koneksi.php";

$id = $_GET['id'];

// Pastikan pelamar memiliki persyaratan (contoh: pelamar harus mengisi profil)
$cek = mysqli_query($conn, "SELECT l.id_lamaran, p.id_pelamar, pel.nama_lengkap, pel.no_hp, pel.alamat FROM lamaran l JOIN pelamar pel ON l.id_pelamar=pel.id_pelamar WHERE l.id_lamaran='$id'");
$row = mysqli_fetch_assoc($cek);
if(!$row){
    header("Location: daftar_pelamar.php");
    exit;
}

// Contoh pemeriksaan persyaratan: pastikan pelamar punya nama dan no_hp
if(empty($row['nama_lengkap']) || empty($row['no_hp'])){
    echo "<script>alert('Tidak dapat diterima: pelamar belum melengkapi data profil.'); window.location='daftar_pelamar.php';</script>";
    exit;
}

mysqli_query($conn, "UPDATE lamaran SET status_lamaran='diterima' WHERE id_lamaran='$id'");

// Ketika pelamar diterima, kurangi kuota tambahan jika perlu
$low = mysqli_query($conn, "SELECT id_lowongan FROM lamaran WHERE id_lamaran='$id'");
$l = mysqli_fetch_assoc($low);
if($l){
    mysqli_query($conn, "UPDATE lowongan SET kuota = GREATEST(0, kuota - 1) WHERE id_lowongan='".$l['id_lowongan']."'");
    mysqli_query($conn, "UPDATE lowongan SET status='Closed' WHERE id_lowongan='".$l['id_lowongan']."' AND kuota<=0");
}

header("Location: daftar_pelamar.php");
exit;
?>