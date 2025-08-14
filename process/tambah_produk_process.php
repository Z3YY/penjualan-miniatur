<?php
include '../config/db.php';

$nama = $_POST['nama'];
$deskripsi = $_POST['deskripsi'];
$harga = $_POST['harga'];
$stok = $_POST['stok'];

$gambar = $_FILES['gambar']['name'];
$tmp = $_FILES['gambar']['tmp_name'];
$path = "../uploads/produk/$gambar";

move_uploaded_file($tmp, $path);

$query = mysqli_query($conn, "INSERT INTO produk (nama, deskripsi, harga, stok, gambar) 
VALUES ('$nama', '$deskripsi', '$harga', '$stok', '$gambar')");

if ($query) {
    header("Location: ../admin/produk_index.php");
} else {
    echo "Gagal tambah produk.";
}
