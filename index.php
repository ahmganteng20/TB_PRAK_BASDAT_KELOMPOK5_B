<?php
include "config/koneksi.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>LokerIn - Sistem Lowongan Kerja</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* CSS tambahan agar navbar luar serasi dengan header global */
        .navbar-custom {
            background-color: #007bff;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .navbar-custom a.nav-link {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        .navbar-custom a.nav-link:hover {
            text-decoration: underline;
        }
        .h1-tema {
            color: #1e3d73;
            font-size: 28px;
            margin-top: 30px;
            margin-bottom: 25px;
            font-weight: bold;
            display: block;
        }
        .card-custom {
            background: #ffffff;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
            margin-top: 20px;
        }
    </style>
</head>

<body style="background-color: #f8f9fa; margin: 0; padding: 0; font-family: Arial, sans-serif;">

<div class="navbar-custom">
    
    <a href="index.php" style="display: inline-flex; align-items: center; text-decoration: none; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 26px; font-weight: bold; letter-spacing: -0.5px;">
        <span style="color: white; padding-right: 2px;">Loker</span>
        <span style="background-color: white; color: #007bff; padding: 1px 6px; border-radius: 3px; margin-left: 2px; display: inline-block; line-height: 1.1; font-weight: bold;">in</span>
    </a>

    <div>
        <a class="nav-link" href="auth/login.php">Login</a>
        <a class="nav-link" href="auth/register.php">Daftar</a>
    </div>

</div>

<div class="container" style="max-width: 1100px; margin: 0 auto; padding: 0 20px;">

    <span class="h1-tema">Selamat Datang</span>

    <div class="card-custom">
        <h2 style="color: #1e3d73; margin-top: 0;">Sistem Lowongan Kerja</h2>
        <p style="color: #666; font-size: 15px; line-height: 1.6;">
            Website pencarian lowongan kerja untuk pelamar dan perusahaan.
        </p>
    </div>

</div>

</body>
</html>