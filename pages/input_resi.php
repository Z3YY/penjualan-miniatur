<?php
include '../includes/auth.php';
include '../config/db.php';

$id = $_GET['id'] ?? 0;
$pesanan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pesanan WHERE id = $id"));

if (!$pesanan || $pesanan['status'] !== 'Diproses') {
  echo "<p class='text-red-600 p-4'>Pesanan tidak ditemukan atau belum bisa dikirim.</p>";
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $resi = htmlspecialchars($_POST['resi']);
  mysqli_query($conn, "UPDATE pesanan SET resi = '$resi', status = 'Dikirim' WHERE id = $id");

  // Kirim email ke customer
  $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT email FROM users WHERE id = {$pesanan['user_id']}"));
  include '../includes/mailer.php';
  kirim_email(
    $user['email'],
    'Pesanan Anda Telah Dikirim',
    "Pesanan Anda telah dikirim dengan nomor resi: <strong>$resi</strong>. Terima kasih telah berbelanja!"
  );

  header("Location: data_pesanan.php");
  exit;
}
?>

<h2 class="text-2xl font-bold mb-6">📦 Input Nomor Resi</h2>

<p><strong>ID Pesanan:</strong> <?= $pesanan['id'] ?></p>
<p><strong>Status:</strong> <?= $pesanan['status'] ?></p>
<p><strong>Total:</strong> Rp <?= number_format($pesanan['total']) ?></p>

<form method="POST" class="mt-4 space-y-4">
  <label class="block">
    <span class="font-semibold">Nomor Resi:</span>
    <input type="text" name="resi" required class="border px-4 py-2 rounded w-full mt-1">
  </label>

  <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
    Simpan & Tandai Dikirim
  </button>
</form>