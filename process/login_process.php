<?php
session_start();
include '../config/db.php';

$email = $_POST['email'];
$password = $_POST['password'];

// Cek user berdasarkan email
$query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
$user = mysqli_fetch_assoc($query);

if ($user && password_verify($password, $user['password'])) {
    // Login berhasil
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['role'] = $user['role'];

    // Redirect berdasarkan role
    if ($user['role'] === 'admin') {
        header('Location: ../admin/dashboard.php');
    } else {
        header('Location: ../pages/home.php');
    }
    exit;
} else {
    // Login gagal
    header('Location: ../pages/login.php?error=Email atau password salah');
    exit;
}
