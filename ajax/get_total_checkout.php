<?php
session_start();
include '../config/db.php';

$user_id = $_SESSION['user_id'];
$total = 0;

$data = mysqli_query($conn, "
  SELECT k.jumlah, p.harga 
  FROM keranjang k 
  JOIN produk p ON k.produk_id = p.id 
  WHERE k.user_id = $user_id
");

while ($row = mysqli_fetch_assoc($data)) {
    $total += $row['jumlah'] * $row['harga'];
}

echo $total;
