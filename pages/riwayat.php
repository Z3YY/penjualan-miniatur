<?php
include '../includes/auth.php';
include '../includes/header.php';
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$user_id = $_SESSION['user_id'];
$pesanan = mysqli_query($conn, "SELECT * FROM pesanan WHERE user_id = $user_id ORDER BY created_at DESC");
?>

<main class="container mx-auto px-4 py-8">
  <h1 class="text-2xl font-bold mb-6">🧾 Riwayat Pesanan</h1>

  <?php if (isset($_GET['success'])): ?>
    <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
      Pesanan berhasil dibuat! Silakan lakukan pembayaran.
    </div>
  <?php endif; ?>

  <div class="overflow-x-auto">
    <table class="w-full text-sm text-left border">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 border">Tanggal</th>
          <th class="px-4 py-2 border">Alamat</th>
          <th class="px-4 py-2 border">Total</th>
          <th class="px-4 py-2 border">Status</th>
          <th class="px-4 py-2 border">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($pesanan)): ?>
          <tr>
            <td class="px-4 py-2 border"><?= date('d M Y H:i', strtotime($row['created_at'])) ?></td>
            <td class="px-4 py-2 border"><?= $row['alamat'] ?></td>
            <td class="px-4 py-2 border">Rp <?= number_format($row['harga_total']) ?></td>
            <td class="px-4 py-2 border"><?= $row['status'] ?></td>
            <td class="px-4 py-2 border space-y-1">
              <a href="pesanan_detail.php?id=<?= $row['id'] ?>" class="text-blue-600 underline block">Detail</a>

              <?php if ($row['status'] === 'Menunggu' && !$row['bukti_bayar']): ?>
                <a href="upload_bukti.php?id=<?= $row['id'] ?>" class="text-green-600 underline block">Upload Bukti</a>
              <?php elseif ($row['bukti_bayar']): ?>
                <span class="text-gray-500 block">Sudah Bayar</span>
              <?php endif; ?>

              <?php if ($row['status'] === 'Dikirim'): ?>
                <form method="POST" action="../process/konfirmasi_terima.php" class="inline" onsubmit="return confirm('Yakin sudah diterima?')">
                  <input type="hidden" name="id" value="<?= $row['id'] ?>">
                  <button type="submit" class="text-white bg-green-600 hover:bg-green-700 px-3 py-1 rounded block">
                    ✔️ Konfirmasi Diterima
                  </button>
                </form>
                <a href="../process/invoice.php?id=<?= $row['id'] ?>"
                  target="_blank"
                  class="text-blue-600 underline block">🧾 Cetak Invoice</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</main>

<?php include '../includes/footer.php'; ?>