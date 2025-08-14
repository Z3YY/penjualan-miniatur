<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: ../pages/login.php');
  exit;
}

$id = $_POST['id'];
$user_id = $_SESSION['user_id'];

// Pastikan pesanan milik user & statusnya 'Dikirim'
$cek = mysqli_query($conn, "SELECT * FROM pesanan WHERE id = $id AND user_id = $user_id AND status = 'Dikirim'");
if (mysqli_num_rows($cek) === 0) {
  echo "Pesanan tidak valid.";
  exit;
}

// Update status ke 'Selesai'
mysqli_query($conn, "UPDATE pesanan SET status = 'Selesai' WHERE id = $id");

// Redirect ke riwayat
header('Location: ../pages/riwayat.php');
exit;
