<?php
include '../config/db.php';

$id = $_POST['id'];
$nama = $_POST['nama'];
$deskripsi = $_POST['deskripsi'];
$harga = $_POST['harga'];
$stok = $_POST['stok'];

if (!empty($_FILES['gambar']['name'])) {
    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];
    move_uploaded_file($tmp, "../uploads/produk/$gambar");

    mysqli_query($conn, "UPDATE produk SET 
      nama='$nama', deskripsi='$deskripsi', harga='$harga', stok='$stok', gambar='$gambar' 
      WHERE id=$id");
} else {
    mysqli_query($conn, "UPDATE produk SET 
      nama='$nama', deskripsi='$deskripsi', harga='$harga', stok='$stok' 
      WHERE id=$id");
}

header("Location: ../admin/produk_list.php");
?>
