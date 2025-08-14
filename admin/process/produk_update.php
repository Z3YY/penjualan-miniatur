<?php

include '../../includes/admin_auth.php';
include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id        = (int) $_POST['id'];
    $nama      = mysqli_real_escape_string($conn, $_POST['nama']);
    $harga     = (int) $_POST['harga'];
    $stok      = (int) $_POST['stok'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);

    $gambar_update = '';
    if (!empty($_FILES['gambar']['name'])) {
        $gambar = $_FILES['gambar'];
        $gambar_name = time() . '-' . basename($gambar['name']);
        $target_dir = '../uploads/produk/';
        $target_file = $target_dir . $gambar_name;

        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
        $file_ext = strtolower(pathinfo($gambar_name, PATHINFO_EXTENSION));
        if (!in_array($file_ext, $allowed_ext)) {
            die("Format gambar tidak didukung.");
        }

        if (!move_uploaded_file($gambar['tmp_name'], $target_file)) {
            die("Gagal upload gambar.");
        }

        $gambar_update = ", gambar='$gambar_name'";
    }

    $query = "UPDATE produk SET 
              nama='$nama',
              harga=$harga,
              stok=$stok,
              deskripsi='$deskripsi'
              $gambar_update 
            WHERE id=$id";

    if (mysqli_query($conn, $query)) {
        header("Location: ../produk_index.php?edit=success");
        exit;
    } else {
        die("Gagal update produk: " . mysqli_error($conn));
    }
} else {
    header("Location: ../produk_index.php");
    exit;
}
