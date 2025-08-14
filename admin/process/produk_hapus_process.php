<?php
session_start();
include '../../includes/admin_auth.php';
include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) $_POST['id'];

    // Cek apakah produk ada dan ambil nama file gambar
    $produk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT gambar FROM produk WHERE id = $id"));
    if (!$produk) {
        die("Produk tidak ditemukan.");
    }

    // Hapus gambar dari folder
    $gambar_path = '../uploads/produk/' . $produk['gambar'];
    if (file_exists($gambar_path)) {
        unlink($gambar_path);
    }

    // Hapus dari database
    $hapus = mysqli_query($conn, "DELETE FROM produk WHERE id = $id");

    if ($hapus) {
        header("Location: ../produk_index.php?hapus=success");
        exit;
    } else {
        die("Gagal menghapus produk: " . mysqli_error($conn));
    }
} else {
    header("Location: ../produk_index.php");
    exit;
}
