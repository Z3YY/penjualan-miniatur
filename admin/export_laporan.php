<?php
include '../includes/admin_auth.php';
include '../config/db.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=laporan_penjualan.xls");

$result = mysqli_query($conn, "
  SELECT p.*, u.nama 
  FROM pesanan p 
  JOIN users u ON p.user_id = u.id 
  WHERE p.status = 'Selesai'
  ORDER BY p.tanggal DESC
");
?>

<table border="1">
  <tr style="background-color:#f3f4f6">
    <th>No</th>
    <th>Tanggal</th>
    <th>Nama Customer</th>
    <th>Total</th>
    <th>Metode Pembayaran</th>
    <th>Status</th>
  </tr>

  <?php $no = 1; while ($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
      <td><?= $no++ ?></td>
      <td><?= $row['tanggal'] ?></td>
      <td><?= $row['nama'] ?></td>
      <td>Rp <?= number_format($row['total']) ?></td>
      <td><?= $row['metode'] ?? '-' ?></td>
      <td><?= $row['status'] ?></td>
    </tr>
  <?php } ?>
</table>
