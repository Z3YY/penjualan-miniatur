<?php
include '../../includes/admin_auth.php'; // Pastikan hanya admin yang bisa mengakses proses ini
include '../../config/db.php';           // Koneksi database

// Pastikan request datang dari form POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['status_message'] = ['type' => 'error', 'message' => 'Akses tidak sah.'];
    header('Location: ../admin/settings.php');
    exit;
}

$user_id_to_delete = $_POST['user_id'] ?? null; // Ambil ID pengguna yang akan dihapus dari POST

// Validasi ID Pengguna
if (!$user_id_to_delete || !is_numeric($user_id_to_delete)) {
    $_SESSION['status_message'] = ['type' => 'error', 'message' => 'ID Pengguna tidak valid.'];
    header('Location: ../settings.php');
    exit;
}

$user_id_to_delete_safe = mysqli_real_escape_string($conn, $user_id_to_delete);

// Dapatkan ID admin yang sedang login
$current_admin_id = $_SESSION['user_id'];

// PENCEGAHAN KRUSIAL: Admin tidak bisa menghapus akunnya sendiri!
if ($user_id_to_delete_safe == $current_admin_id) {
    $_SESSION['status_message'] = ['type' => 'self_delete_error', 'message' => 'Anda tidak bisa menghapus akun Anda sendiri!'];
    header('Location: ../settings.php');
    exit;
}

// Lakukan query DELETE
$delete_query = "DELETE FROM users WHERE id = '$user_id_to_delete_safe'";

if (mysqli_query($conn, $delete_query)) {
    // Redirect dengan pesan sukses
    $_SESSION['status_message'] = ['type' => 'success', 'message' => 'Akun pengguna berhasil dihapus.'];
    header('Location: ../settings.php');
    exit;
} else {
    // Redirect dengan pesan error
    $_SESSION['status_message'] = ['type' => 'error', 'message' => 'Gagal menghapus akun pengguna: ' . mysqli_error($conn)];
    header('Location: ../settings.php');
    exit;
}
