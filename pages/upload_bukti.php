<?php
include '../includes/auth.php';
include '../includes/header.php';
include '../config/db.php';

$pesanan_id = $_GET['id'];
$pesanan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pesanan WHERE id = $pesanan_id"));
?>

<main class="container mx-auto px-4 py-10">
  <div class="bg-white p-6 rounded-lg shadow-md max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold text-blue-700 mb-4">💳 Pembayaran Pesanan</h2>

    <div class="mb-4">
      <p class="text-gray-700">Total yang harus dibayar:</p>
      <p class="text-2xl font-semibold text-green-600">Rp <?= number_format($pesanan['harga_total']) ?></p>
    </div>

    <div class="mb-6">
      <p class="text-gray-700 mb-2">Silakan transfer ke rekening berikut melalui aplikasi e-wallet atau m-banking:</p>
      <div class="flex items-center justify-center bg-gray-100 rounded p-4">
        <img src="../assets/img/qr_transfer.jpg" alt="QR Transfer" class="w-64 border border-gray-300 rounded shadow">
      </div>
      <p class="text-sm text-center text-gray-500 mt-2">* Scan QR ini untuk melakukan pembayaran</p>
    </div>

    <form action="../process/upload_bukti_process.php" method="POST" enctype="multipart/form-data" class="space-y-4">
      <input type="hidden" name="pesanan_id" value="<?= $pesanan_id ?>">

      <div>
        <label class="block font-semibold mb-1 text-gray-700">🧾 Upload Bukti Pembayaran (jpg/png)</label>
        <input type="file" name="bukti" accept="image/*" required
          class="block w-full border border-gray-300 rounded px-4 py-2 focus:ring focus:ring-blue-300">
      </div>

      <button type="submit"
        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded font-semibold transition duration-150">
        🚀 Upload & Konfirmasi
      </button>
    </form>
  </div>
</main>

<?php include '../includes/footer.php'; ?>