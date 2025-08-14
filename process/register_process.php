<?php
session_start();
include '../config/db.php';

$nama = mysqli_real_escape_string($conn, $_POST['nama']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// Validasi konfirmasi password
if ($password !== $confirm_password) {
    header('Location: ../pages/register.php?error=Konfirmasi password tidak cocok');
    exit;
}

// Validasi panjang password
if (strlen($password) < 6) {
    header('Location: ../pages/register.php?error=Password minimal 6 karakter');
    exit;
}

// Cek apakah email sudah digunakan
$cek = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
if (mysqli_num_rows($cek) > 0) {
    header('Location: ../pages/register.php?error=Email sudah digunakan');
    exit;
}

// Hash password
$hash = password_hash($password, PASSWORD_DEFAULT);

// Simpan user baru dengan role customer
$query = mysqli_query($conn, "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$hash', 'customer')");

if ($query) {
    $user_id = mysqli_insert_id($conn);
    $_SESSION['user_id'] = $user_id;
    $_SESSION['nama'] = $nama;
    $_SESSION['role'] = 'customer';

    header('Location: ../pages/home.php');
    exit;
} else {
    header('Location: ../pages/register.php?error=Gagal mendaftar');
    exit;
}
