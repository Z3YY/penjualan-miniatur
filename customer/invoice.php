<?php
require '../vendor/autoload.php';

use Dompdf\Dompdf;

include '../config/db.php';
session_start();

// Cek login
if (!isset($_SESSION['user_id'])) {
  die("Anda harus login untuk melihat invoice ini.");
}

$user_id_from_session = mysqli_real_escape_string($conn, $_SESSION['user_id']);

// Cek role user
$query_user_role = "SELECT role FROM users WHERE id = '$user_id_from_session'";
$result_user_role = mysqli_query($conn, $query_user_role);
if (!$result_user_role || mysqli_num_rows($result_user_role) == 0) {
  die("User tidak ditemukan atau role tidak terdefinisi.");
}
$user_data = mysqli_fetch_assoc($result_user_role);
$user_role = $user_data['role'];

// Pastikan ID pesanan ada
if (!isset($_GET['id'])) {
  die("ID pesanan tidak ditemukan.");
}
$id_pesanan_safe = mysqli_real_escape_string($conn, $_GET['id']);

// Ambil data pesanan
$query_pesanan = "SELECT p.*, u.nama AS nama_user, u.email
                  FROM pesanan p
                  JOIN users u ON p.user_id = u.id
                  WHERE p.id = '$id_pesanan_safe'";
if ($user_role === 'user') {
  $query_pesanan .= " AND p.user_id = '$user_id_from_session'";
}
$result_pesanan = mysqli_query($conn, $query_pesanan);
if (!$result_pesanan || mysqli_num_rows($result_pesanan) == 0) {
  die("Pesanan tidak ditemukan atau Anda tidak memiliki izin untuk melihatnya.");
}
$pesanan = mysqli_fetch_assoc($result_pesanan);

// Ambil item pesanan detail
$query_items = "
    SELECT pd.*, pr.nama AS nama_produk, pr.harga AS harga_produk_saat_ini, pr.gambar
    FROM pesanan_detail pd
    JOIN produk pr ON pd.produk_id = pr.id
    WHERE pd.pesanan_id = '$id_pesanan_safe'
";
$result_items = mysqli_query($conn, $query_items);

// Start output buffering
ob_start();
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Invoice Pesanan #<?= htmlspecialchars($pesanan['id']) ?></title>
  <style>
    body {
      font-family: sans-serif;
      font-size: 12px;
      color: #333;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th,
    td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: left;
    }

    th {
      background-color: #f5f5f5;
      font-weight: bold;
    }

    h2 {
      margin-bottom: 0;
      color: #0056b3;
      text-align: center;
    }

    .header-info {
      margin-bottom: 20px;
    }

    .header-info p {
      margin: 2px 0;
    }

    .total {
      font-weight: bold;
      text-align: right;
      background-color: #e6f7ff;
    }

    .total-label {
      text-align: right;
      padding-right: 15px;
    }
  </style>
</head>

<body>
  <h2>🧾 Invoice Pesanan #<?= htmlspecialchars($pesanan['id']) ?></h2>
  <div class="header-info">
    <p><strong>Nama Pelanggan:</strong> <?= htmlspecialchars($pesanan['nama_user'] ?? 'N/A') ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($pesanan['email'] ?? 'N/A') ?></p>
    <p><strong>No. Telepon:</strong> <?= htmlspecialchars($pesanan['no_telp'] ?? '-') ?></p>
    <p><strong>Alamat Pengiriman:</strong> <?= nl2br(htmlspecialchars($pesanan['alamat'] ?? 'N/A')) ?></p>
    <p><strong>Tanggal Pesan:</strong>
      <?php
      $tanggal_db = $pesanan['created_at'] ?? null;
      if ($tanggal_db && $tanggal_db !== '0000-00-00 00:00:00') {
        echo htmlspecialchars(date('d F Y H:i', strtotime($tanggal_db)));
      } else {
        echo "Tanggal tidak tersedia";
      }
      ?>
    </p>
    <p><strong>Status Pesanan:</strong> <?= htmlspecialchars($pesanan['status'] ?? 'N/A') ?></p>
  </div>

  <table>
    <thead>
      <tr>
        <th>Produk</th>
        <th>Jumlah</th>
        <th>Harga Satuan</th>
        <th>Subtotal</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $grand_total = 0;
      if (mysqli_num_rows($result_items) > 0) {
        while ($item_row = mysqli_fetch_assoc($result_items)) {
          $harga_satuan = $item_row['harga'] ?? $item_row['harga_produk_saat_ini'];
          $subtotal_item = $item_row['jumlah'] * $harga_satuan;
          $grand_total += $subtotal_item;
      ?>
          <tr>
            <td><?= htmlspecialchars($item_row['nama_produk']) ?></td>
            <td><?= htmlspecialchars($item_row['jumlah']) ?></td>
            <td>Rp <?= number_format($harga_satuan, 0, ',', '.') ?></td>
            <td>Rp <?= number_format($subtotal_item, 0, ',', '.') ?></td>
          </tr>
      <?php
        }
      } else {
        echo '<tr><td colspan="4" style="text-align:center;">Tidak ada item dalam pesanan ini.</td></tr>';
      }
      ?>
      <tr>
        <td colspan="3" class="total total-label">Grand Total:</td>
        <td class="total">Rp <?= number_format($grand_total, 0, ',', '.') ?></td>
      </tr>
    </tbody>
  </table>

  <p style="text-align: center; margin-top: 30px; font-size: 10px; color: #666;">
    Terima kasih atas pesanan Anda di MiniaturStore!
  </p>
</body>

</html>
<?php
$html = ob_get_clean();

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("invoice_{$id_pesanan_safe}.pdf", ["Attachment" => false]);
exit;
