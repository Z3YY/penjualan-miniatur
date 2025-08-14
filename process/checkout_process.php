<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: ../pages/login.php');
  exit;
}

$user_id = $_SESSION['user_id'];

// Cek form
if (!isset($_POST['province_id'], $_POST['city_id'], $_POST['district_id'], $_POST['subdistrict_id'], $_POST['postalcode'], $_POST['alamat_detail'], $_POST['no_telp'], $_POST['ongkir_value'], $_POST['pajak_value'], $_POST['harga_total_value'])) {
  header('Location: ../pages/checkout.php?error=incomplete');
  exit;
}

$province_id = mysqli_real_escape_string($conn, $_POST['province_id']);
$city_id = mysqli_real_escape_string($conn, $_POST['city_id']);
$district_id = mysqli_real_escape_string($conn, $_POST['district_id']);
$subdistrict_id = mysqli_real_escape_string($conn, $_POST['subdistrict_id']);
$postalcode = mysqli_real_escape_string($conn, $_POST['postalcode']);
$alamat_detail = mysqli_real_escape_string($conn, $_POST['alamat_detail']);
$no_telp = mysqli_real_escape_string($conn, $_POST['no_telp']);

$ongkir = (int) $_POST['ongkir_value'];
$pajak = (int) $_POST['pajak_value'];
$harga_total = (int) $_POST['harga_total_value'];

// Ambil nama dari ID
$get = function ($table, $id_field, $id) use ($conn) {
  $res = mysqli_query($conn, "SELECT * FROM $table WHERE $id_field = '$id' LIMIT 1");
  $row = mysqli_fetch_assoc($res);
  return $row;
};

$prov_row = $get('provinces', 'prov_id', $province_id);
$city_row = $get('cities', 'city_id', $city_id);
$district_row = $get('districts', 'dis_id', $district_id);
$subdistrict_row = $get('subdistricts', 'subdis_id', $subdistrict_id);

$prov = $prov_row['prov_name'] ?? '';
$city = $city_row['city_name'] ?? '';
$district = $district_row['dis_name'] ?? '';
$subdistrict = $subdistrict_row['subdis_name'] ?? '';

// Gabungkan alamat
$alamat = "Prov: $prov, Kota: $city, Kec: $district, Kel: $subdistrict, Kode Pos: $postalcode - $alamat_detail";

// Ambil data keranjang
$keranjang = mysqli_query($conn, "
  SELECT k.produk_id, k.jumlah, p.harga
  FROM keranjang k
  JOIN produk p ON k.produk_id = p.id
  WHERE k.user_id = '$user_id'
");

$total = 0;
$items = [];

while ($row = mysqli_fetch_assoc($keranjang)) {
  $sub = $row['jumlah'] * $row['harga'];
  $total += $sub;
  $items[] = $row;
}

if (empty($items)) {
  header('Location: ../pages/bayar.php?status=empty_cart');
  exit;
}

mysqli_autocommit($conn, false);
$success = true;
$timestamp = date('Y-m-d H:i:s');

// Insert pesanan
$q = "INSERT INTO pesanan (user_id, alamat, no_telp, total, pajak, harga_total, status, created_at)
      VALUES ('$user_id', '$alamat', '$no_telp', '$total', '$pajak', '$harga_total', 'Menunggu', '$timestamp')";

if (!mysqli_query($conn, $q)) {
  $success = false;
} else {
  $pesanan_id = mysqli_insert_id($conn);
  foreach ($items as $item) {
    $produk_id = $item['produk_id'];
    $jumlah = $item['jumlah'];
    $harga = $item['harga'];
    $q_detail = "INSERT INTO pesanan_detail (pesanan_id, produk_id, jumlah, harga)
                 VALUES ('$pesanan_id', '$produk_id', '$jumlah', '$harga')";
    if (!mysqli_query($conn, $q_detail)) {
      $success = false;
      break;
    }
  }
  if ($success) {
    mysqli_query($conn, "DELETE FROM keranjang WHERE user_id = '$user_id'");
  }
}

if ($success) {
  mysqli_commit($conn);
  header("Location: ../pages/bayar.php?id=$pesanan_id");
} else {
  mysqli_rollback($conn);
  header("Location: ../pages/bayar.php?status=error");
}
exit;
