<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "../config/koneksi.php";

// Jika user sudah login, langsung lempar ke dashboard
if(isset($_SESSION['id_user'])){
    header("Location: ../dashboard/index.php");
    exit;
}

if(isset($_POST['login'])){
    // Menangkap email dan mengamankannya dari SQL Injection
    $email_input = mysqli_real_escape_string($conn, $_POST['username']);
    
    // PERBAIKAN: Menangkap password sebagai teks biasa (TANPA MD5)
    $password = mysqli_real_escape_string($conn, $_POST['password']); 

    // Mencari user berdasarkan email dan password teks biasa
    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email_input' AND password='$password'");
    
$login_error = '';
if(mysqli_num_rows($query) > 0){
        $user = mysqli_fetch_assoc($query);
        
        if(isset($user['is_suspended']) && (int)$user['is_suspended'] === 1){
            $login_error = 'Akun Anda saat ini sedang dinonaktifkan demi keamanan. Silakan hubungi tim layanan pelanggan kami untuk melakukan verifikasi.';
        } else {
            $_SESSION['id_user']      = $user['id_user'];
            $_SESSION['role']         = $user['role'];
            $_SESSION['is_suspended'] = (int)($user['is_suspended'] ?? 0);
            
            // ====================================================================
            // TRIK MEMOTONG EMAIL UNTUK NAMA TAMPILAN NAVBAR & DASHBOARD
            // ====================================================================
            // Mengambil email asli dari database (contoh: faufau@gmail.com)
            $email_user = $user['email']; 
            
            // Memotong teks dan mengambil karakter HANYA sebelum tanda '@' (menjadi: faufau)
            $nama_dari_email = explode('@', $email_user)[0]; 
            
            // Daftarkan hasil potongan email tersebut ke dalam session nama & username
            $_SESSION['nama']     = $nama_dari_email;
            $_SESSION['username'] = $nama_dari_email;
            // ====================================================================

            header("Location: ../dashboard/index.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - LokerIn</title>
    <style>
        body { background-color: #f8f9fa; font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card-login { background: #ffffff; border-radius: 8px; padding: 30px; width: 350px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: 1px solid #e9ecef; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 8px; color: #333; font-size: 14px;}
        .form-control { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn-biru { background-color: #007bff; color: white; border: none; padding: 12px; width: 100%; border-radius: 4px; font-weight: bold; cursor: pointer; margin-top: 10px; }
        .btn-biru:hover { background-color: #0056b3; }
    </style>
</head>
<body>

<div class="card-login">
    <div style="display: flex; justify-content: center; font-size: 28px; font-weight: bold; margin-bottom: 25px; letter-spacing: -0.5px;">
        <span style="color: #007bff; padding-right: 2px;">Loker</span>
        <span style="background-color: #007bff; color: white; padding: 1px 6px; border-radius: 3px; margin-left: 2px; display: inline-block; line-height: 1.1;">in</span>
    </div>

    <form method="POST">
        <div class="form-group">
            <label>Email / Username</label>
            <input type="text" name="username" class="form-control" placeholder="Masukkan email Anda" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" placeholder="Masukkan password Anda" required>
        </div>

        <button type="submit" name="login" class="btn-biru">Masuk / Login</button>
        
        <?php if(!empty($login_error)): ?>
            <p style="text-align: center; color: #d9534f; font-weight: bold; margin-top: 12px;"><?= htmlspecialchars($login_error); ?></p>
        <?php endif; ?>

        <p style="text-align: center; font-size: 13px; color: #666; margin-top: 20px;">
            Belum punya akun? <a href="register.php" style="color: #007bff; text-decoration: none; font-weight: bold;">Daftar di sini</a>
        </p>
    </form>
</div>

</body>
</html>