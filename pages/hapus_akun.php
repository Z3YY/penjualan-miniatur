<?php
include '../includes/auth.php'; // Autentikasi customer (pastikan session sudah dimulai di sini)
include '../config/db.php';     // Koneksi database

// Pastikan request datang dari form POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['status_message'] = ['type' => 'error', 'message' => 'Akses tidak sah.'];
    header('Location: ../customer/settings.php');
    exit;
}

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php'); // Arahkan ke login jika belum login
    exit;
}

$user_id = $_SESSION['user_id'];
$password_confirm_input = $_POST['password_confirm'] ?? ''; // Ambil password konfirmasi dari form

$user_id_safe = mysqli_real_escape_string($conn, $user_id);

// Ambil hash password pengguna dari database untuk verifikasi
$user_query = mysqli_query($conn, "SELECT password FROM users WHERE id = '$user_id_safe'");
if (!$user_query || mysqli_num_rows($user_query) == 0) {
    $_SESSION['status_message'] = ['type' => 'error', 'message' => 'Akun tidak ditemukan.'];
    header('Location: ../customer/settings.php');
    exit;
}
$user_data = mysqli_fetch_assoc($user_query);
$current_hashed_password = $user_data['password'];

// Verifikasi password yang dimasukkan dengan hash di database
if (!password_verify($password_confirm_input, $current_hashed_password)) {
    $_SESSION['status_message'] = ['type' => 'error', 'message' => 'Konfirmasi password salah. Akun tidak dihapus.'];
    header('Location: ../customer/settings.php');
    exit;
}

// Jika password cocok, lakukan proses penghapusan
$delete_query = "DELETE FROM users WHERE id = '$user_id_safe'";

if (mysqli_query($conn, $delete_query)) {
    // Akun berhasil dihapus, hancurkan sesi dan redirect ke halaman login/beranda
    session_unset();    // Hapus semua variabel sesi
    session_destroy();  // Hancurkan sesi

    $_SESSION['status_message'] = ['type' => 'success', 'message' => 'Akun Anda berhasil dihapus.'];
    header('Location: ../pages/login.php'); // Arahkan ke halaman login
    exit;
} else {
    // Jika ada error database
    $_SESSION['status_message'] = ['type' => 'error', 'message' => 'Gagal menghapus akun: ' . mysqli_error($conn)];
    header('Location: ../customer/settings.php');
    exit;
}
