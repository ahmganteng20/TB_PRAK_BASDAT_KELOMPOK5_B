<?php
include "../config/koneksi.php";

if(isset($_POST['register'])){

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Menyimpan password sebagai teks biasa (TANPA MD5)
    $password = mysqli_real_escape_string($conn, $_POST['password']); 
    
    // Mengambil role secara dinamis dari input form select
    $role = mysqli_real_escape_string($conn, $_POST['role']); 

    // Cek apakah email sudah digunakan
    $cek = mysqli_query(
        $conn,
        "SELECT * FROM users WHERE email='$email'"
    );

    if(mysqli_num_rows($cek) > 0){
        echo "<script>alert('Email sudah terdaftar!');</script>";
    } else {

        // ====================================================================
        // PERBAIKAN: Hanya memasukkan kolom yang benar-benar ada di database (4/4)
        // ====================================================================
        $simpan = mysqli_query(
            $conn,
            "INSERT INTO users (email, password, role)
             VALUES ('$email', '$password', '$role')"
        );

        if($simpan){
            echo "<script>
            alert('Registrasi berhasil! Silakan login.');
            window.location='login.php';
            </script>";
            exit;
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrasi - LokerIn</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background-color: #f8f9fa; font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card-register { background: #ffffff; border-radius: 8px; padding: 30px; width: 350px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: 1px solid #e9ecef; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; color: #333; font-size: 14px;}
        .form-control { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn-biru { background-color: #007bff; color: white; border: none; padding: 12px; width: 100%; border-radius: 4px; font-weight: bold; cursor: pointer; margin-top: 10px; }
        .btn-biru:hover { background-color: #0056b3; }
    </style>
</head>
<body>

<div class="card-register">
    <div style="display: flex; justify-content: center; font-size: 28px; font-weight: bold; margin-bottom: 20px; letter-spacing: -0.5px;">
        <span style="color: #007bff; padding-right: 2px;">Loker</span>
        <span style="background-color: #007bff; color: white; padding: 1px 6px; border-radius: 3px; margin-left: 2px; display: inline-block; line-height: 1.1;">in</span>
    </div>

    <form method="POST">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" placeholder="Masukkan email Anda" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" placeholder="Buat password Anda" required>
        </div>

        <div class="form-group">
            <label>Daftar Sebagai</label>
            <select name="role" class="form-control" required>
                <option value="">-- Pilih Role --</option>
                <option value="pelamar">Pelamar</option>
                <option value="perusahaan">Perusahaan</option>
            </select>
        </div>

        <button type="submit" name="register" class="btn-biru">Daftar Akun</button>
        
        <p style="text-align: center; font-size: 13px; color: #666; margin-top: 15px; margin-bottom: 0;">
            Sudah punya akun? <a href="login.php" style="color: #007bff; text-decoration: none; font-weight: bold;">Login di sini</a>
        </p>
    </form>
</div>

</body>
</html>