<?php
session_start();
include '../config/db.php'; // Pastikan path ini benar

// Pastikan user login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php'); // Redirect ke halaman login
    exit;
}

// Ambil user_id dari sesi
$user_id_from_session = mysqli_real_escape_string($conn, $_SESSION['user_id']);

// Ambil role user dari database
$query_user_role = "SELECT role FROM users WHERE id = '$user_id_from_session'";
$result_user_role = mysqli_query($conn, $query_user_role);

if (!$result_user_role || mysqli_num_rows($result_user_role) == 0) {
    header('Location: ../auth/login.php');
    exit;
}
$user_data = mysqli_fetch_assoc($result_user_role);
$user_role = $user_data['role']; // Dapatkan role user

$pesanan_id_from_get = $_GET['id'] ?? null;

// Validasi ID Pesanan dari URL
if (!$pesanan_id_from_get || !is_numeric($pesanan_id_from_get)) {
    echo "<p style='color:red; text-align:center; margin-top:10px;'>ID Pesanan tidak valid.</p>";
    exit;
}
$pesanan_id_safe = mysqli_real_escape_string($conn, $pesanan_id_from_get);

// --- BAGIAN PENTING: Menyesuaikan Query Berdasarkan Role User ---
$query_pesanan = "";
if ($user_role === 'admin') {
    $query_pesanan = "SELECT * FROM pesanan WHERE id = '$pesanan_id_safe'";
} else {
    $query_pesanan = "SELECT * FROM pesanan WHERE id = '$pesanan_id_safe' AND user_id = '$user_id_from_session'";
}

$result_pesanan = mysqli_query($conn, $query_pesanan);

if (!$result_pesanan) {
    echo "<p style='color:red; text-align:center; margin-top:10px;'>Error saat menjalankan query: " . mysqli_error($conn) . "</p>";
    exit;
}

$pesanan = mysqli_fetch_assoc($result_pesanan);

if (!$pesanan) {
    echo "<p style='color:red; text-align:center; margin-top:10px;'>Pesanan tidak ditemukan.</p>";
    exit;
}

// Validasi Status Pesanan (Harus 'Dikirim' agar bisa diselesaikan)
if ($pesanan['status'] !== 'Dikirim') {
    echo "<p style='color:orange; text-align:center; margin-top:10px;'>Pesanan ini belum bisa diselesaikan karena statusnya adalah '<strong>" . htmlspecialchars($pesanan['status']) . "</strong>' (seharusnya 'Dikirim').</p>";
    exit;
}

// Jika semua validasi lolos, update status pesanan
// UBAH NILAI 'Selesai' menjadi 'Berhasil' (sesuai ENUM di DB)
$update_query = "UPDATE pesanan SET status = 'Berhasil' WHERE id = '$pesanan_id_safe'";

// Jika Anda ingin memastikan hanya admin yang bisa update, tambahkan user_id_from_session
if ($user_role === 'user') { // Hanya user biasa yang perlu filter user_id saat update
    $update_query .= " AND user_id = '$user_id_from_session'";
}

$update_result = mysqli_query($conn, $update_query);

if (!$update_result) {
    echo "<p style='color:red; text-align:center; margin-top:10px;'>Gagal memperbarui status pesanan: " . mysqli_error($conn) . "</p>";
    exit;
}

// Redirect ke invoice atau halaman sukses
header("Location: ../customer/invoice.php?id=$pesanan_id_safe&status=selesai"); // Status di URL ini tidak berpengaruh pada DB
exit;
