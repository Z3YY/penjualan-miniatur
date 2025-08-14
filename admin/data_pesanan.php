<?php
include '../includes/admin_auth.php';
include '../includes/header.php';
include '../config/db.php';

// Ambil data pesanan & nama user
$pesanan = mysqli_query($conn, "
    SELECT p.*, u.nama AS nama_user 
    FROM pesanan p 
    JOIN users u ON p.user_id = u.id 
    ORDER BY p.created_at DESC
");
?>

<main class="container mx-auto px-4 py-8">
  <h1 class="text-2xl font-bold mb-6">📦 Data Pesanan</h1>

  <?php if (isset($_GET['verifikasi']) && $_GET['verifikasi'] === 'sukses'): ?>
    <div class="mb-4 bg-green-100 text-green-800 px-4 py-3 rounded shadow">
      ✅ Verifikasi pembayaran berhasil!
    </div>
  <?php elseif (isset($_GET['verifikasi']) && $_GET['verifikasi'] === 'ditolak'): ?>
    <div class="mb-4 bg-red-100 text-red-800 px-4 py-3 rounded shadow">
      ❌ Bukti pembayaran ditolak.
    </div>
  <?php endif; ?>

  <div class="overflow-x-auto">
    <table class="w-full text-sm border text-left">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 border">Tanggal</th>
          <th class="px-4 py-2 border">Customer</th>
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
            <td class="px-4 py-2 border"><?= htmlspecialchars($row['nama_user']) ?></td>
            <td class="px-4 py-2 border"><?= htmlspecialchars($row['alamat']) ?></td>
            <td class="px-4 py-2 border">Rp <?= number_format($row['harga_total']) ?></td>
            <td class="px-4 py-2 border">
              <?php
              $status = $row['status'];
              $status_label = match ($status) {
                'Menunggu' => '<span class="text-yellow-600 font-semibold">⏳ Menunggu</span>',
                'Diproses' => '<span class="text-blue-600 font-semibold">🔄 Diproses</span>',
                'Dikirim'  => '<span class="text-purple-600 font-semibold">🚚 Dikirim</span>',
                'Berhasil'  => '<span class="text-green-600 font-semibold">✅ Berhasil</span>', // <--- UBAH KE 'Berhasil'
                default    => htmlspecialchars($status),
              };
              echo $status_label;
              ?>
            </td>
            <td class="px-4 py-2 border space-x-2">
              <a href="pesanan_detail.php?id=<?= $row['id'] ?>" class="text-blue-600 underline">Detail</a>

              <?php if ($row['status'] === 'Menunggu' && $row['bukti_bayar']) { ?>
                <a href="verifikasi_pembayaran.php?id=<?= $row['id'] ?>" class="text-green-600 underline">Verifikasi</a>
              <?php } ?>

              <?php if ($row['status'] === 'Diproses') { ?>
                <a href="kirim_pesanan.php?id=<?= $row['id'] ?>" class="text-orange-600 underline">Kirim</a>
              <?php } ?>

              <?php if ($row['status'] === 'Dikirim') { ?>
                <a href="selesaikan_pesanan.php?id=<?= $row['id'] ?>" class="text-teal-600 underline">Selesai</a>
              <?php } ?>

              <?php if ($row['status'] === 'Berhasil') { ?> <span class="text-green-700 font-bold">✅</span>
              <?php } ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</main>

<?php include '../includes/footer.php'; ?>