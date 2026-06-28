<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "../config/koneksi.php";

if(!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

$id = 0;
$action = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $action = $_POST['action'] ?? '';
} else {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $action = $_GET['action'] ?? '';
}

if($id <= 0 || !in_array($action, ['suspend','unsuspend'])){
    header("Location: index.php?status=failed");
    exit;
}

// Pastikan kolom is_suspended ada
$column_check = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'is_suspended'");
if(!$column_check || mysqli_num_rows($column_check) === 0){
    $result = mysqli_query($conn, "ALTER TABLE users ADD COLUMN is_suspended TINYINT(1) NOT NULL DEFAULT 0");
    if($result === false){
        error_log('toggle_suspend ALTER error: ' . mysqli_error($conn));
        header("Location: index.php?error=toggle_fail");
        exit;
    }
}

// Ambil current
$id_safe = mysqli_real_escape_string($conn, $id);
$q = mysqli_query($conn, "SELECT is_suspended, email FROM users WHERE id_user='$id_safe'");
if(!$q){
    error_log('toggle_suspend SELECT error: ' . mysqli_error($conn));
    header("Location: index.php?error=toggle_fail");
    exit;
}

$row = mysqli_fetch_assoc($q);
if(!$row){
    header("Location: index.php?error=not_found");
    exit;
}

$current = (int)($row['is_suspended'] ?? 0);
$actor = $_SESSION['id_user'];

if($action === 'suspend'){
    $update = mysqli_query($conn, "UPDATE users SET is_suspended=1 WHERE id_user='$id_safe'");
    $act = 'suspend';
    $redirect_status = 'suspended';
} else {
    $update = mysqli_query($conn, "UPDATE users SET is_suspended=0 WHERE id_user='$id_safe'");
    $act = 'unsuspend';
    $redirect_status = 'unsuspended';
}

if(!$update){
    error_log('toggle_suspend UPDATE error: ' . mysqli_error($conn));
    header("Location: index.php?error=toggle_fail");
    exit;
}

header("Location: index.php?status=$redirect_status");
exit;
?>
