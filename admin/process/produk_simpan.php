<?php

include '../../includes/admin_auth.php';
include '../../config/db.php';

if (isset($_POST['tambah'])) {
    $nama      = mysqli_real_escape_string($conn, $_POST['nama']);
    $harga     = (int) $_POST['harga'];
    $stok      = (int) $_POST['stok'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);

    // Upload gambar
    $gambar = $_FILES['gambar'];
    $gambar_name = time() . '-' . basename($gambar['name']);
    $target_dir = '../uploads/produk/';
    $target_file = $target_dir . $gambar_name;

    // Validasi file
    $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
    $file_ext = strtolower(pathinfo($gambar_name, PATHINFO_EXTENSION));

    if (!in_array($file_ext, $allowed_ext)) {
        die("Format gambar tidak didukung.");
    }

    if (!move_uploaded_file($gambar['tmp_name'], $target_file)) {
        die("Gagal upload gambar.");
    }

    // Simpan ke database
    $query = "INSERT INTO produk (nama, harga, stok, deskripsi, gambar, created_at)
            VALUES ('$nama', $harga, $stok, '$deskripsi', '$gambar_name', NOW())";

    if (mysqli_query($conn, $query)) {
        header("Location: ../produk_index.php?success=1");
        exit;
    } else {
        die("Gagal menyimpan produk: " . mysqli_error($conn));
    }
} else {
    header("Location: ../produk_index.php");
    exit;
}
