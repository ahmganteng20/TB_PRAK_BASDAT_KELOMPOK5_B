<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['id_user'])){
    header("Location: /LokerIn/auth/login.php");
    exit;
}

// Pastikan status suspensi selalu diperiksa untuk session aktif
include __DIR__ . "/../config/koneksi.php";
$user_id = mysqli_real_escape_string($conn, $_SESSION['id_user']);
$q = mysqli_query($conn, "SELECT is_suspended FROM users WHERE id_user='$user_id' LIMIT 1");
if($q){
    $user_status = mysqli_fetch_assoc($q);
    if(!empty($user_status['is_suspended'])){
        session_unset();
        session_destroy();
        header("Location: /LokerIn/auth/login.php?error=suspended");
        exit;
    }
}

// Sinkronisasi dengan potongan email depan dari sistem login baru
$username_tampil = $_SESSION['username'] ?? $_SESSION['nama'] ?? 'User';
$role_user = $_SESSION['role'] ?? 'pelamar';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sistem Lowongan Kerja</title>
    <style>
        body {
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .navbar-biru {
            background-color: #007bff;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .navbar-biru a {
            color: white;
            text-decoration: none;
            font-size: 14px;
        }
        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .card-tema {
            background: #ffffff;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
            min-height: 400px;
            box-sizing: border-box;
        }
        .h1-tema {
            color: #1e3d73;
            font-size: 28px;
            margin-top: 0;
            margin-bottom: 25px;
            font-weight: bold;
            display: block;
        }
        .table-tema {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .table-tema th {
            background-color: #007bff;
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 14px;
        }
        .table-tema td {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
            font-size: 14px;
            color: #333;
        }
        .btn-biru {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }
        .btn-biru:hover {
            background-color: #0056b3;
        }
        .btn-bahaya {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }
    </style>
</head>
<body>

<div class="navbar-biru">
    <a href="/LokerIn/dashboard/index.php" style="display: inline-flex; align-items: center; text-decoration: none; font-size: 26px; font-weight: bold;">
        <span style="color: white; padding-right: 1px;">Loker</span>
        <span style="background-color: white; color: #007bff; padding: 2px 6px; border-radius: 3px; margin-left: 2px; display: inline-block; line-height: 1;">in</span>
    </a>
    
    <div style="display: flex; align-items: center; gap: 20px;">
        
        <?php if($role_user == "pelamar"){ ?>
            <a href="/LokerIn/pelamar/profil.php">Profil Pelamar</a>
            <span style="color: rgba(255,255,255,0.5);">|</span>
            <a href="/LokerIn/lowongan/index.php">Lowongan</a>
            <span style="color: rgba(255,255,255,0.5);">|</span>
            <a href="/LokerIn/lamaran/index.php">Lamaran Saya</a>
        <?php } elseif($role_user == "perusahaan") { ?>
            <a href="/LokerIn/perusahaan/profil.php">Profil Perusahaan</a>
            <span style="color: rgba(255,255,255,0.5);">|</span>
            <a href="/LokerIn/lowongan/index.php">Daftar Lowongan</a>
            <span style="color: rgba(255,255,255,0.5);">|</span>
            <a href="/LokerIn/lamaran/daftar_pelamar.php">Daftar Pelamar</a>
        <?php } else { ?>
            <a href="/LokerIn/users/dashboard.php">Dashboard</a>
            <span style="color: rgba(255,255,255,0.5);">|</span>
            <a href="/LokerIn/users/index.php">Kelola User</a>
            <span style="color: rgba(255,255,255,0.5);">|</span>
            <a href="/LokerIn/users/all_pelamar.php">Daftar Pelamar</a>
            <span style="color: rgba(255,255,255,0.5);">|</span>
            <a href="/LokerIn/users/all_lamaran.php">Daftar Lamaran</a>
            <span style="color: rgba(255,255,255,0.5);">|</span>
            <a href="/LokerIn/users/all_lowongan.php">Daftar Lowongan</a>
        <?php } ?>

        <span style="color: rgba(255,255,255,0.5);">|</span>
        <span style="color: white; font-weight: bold; background-color: rgba(255,255,255,0.2); padding: 4px 8px; border-radius: 4px; font-size: 14px;">
            <?= htmlspecialchars($username_tampil); ?>
        </span>
        <span style="color: rgba(255,255,255,0.5);">|</span>
        <a href="/LokerIn/auth/logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?')">Logout</a>
    </div>
</div>

<div class="container">
    <div class="card-tema">