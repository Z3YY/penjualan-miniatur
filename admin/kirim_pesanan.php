<?php
include '../includes/admin_auth.php';
include '../includes/header.php';
include '../config/db.php';

$id = $_GET['id'];
$pesanan = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT p.*, u.nama, u.email 
  FROM pesanan p 
  JOIN users u ON p.user_id = u.id 
  WHERE p.id = $id
"));

if (!$pesanan || $pesanan['status'] !== 'Diproses') {
    echo '<div class="p-4 bg-red-100 text-red-700 rounded mx-4 my-6">❌ Data pesanan tidak ditemukan atau belum bisa dikirim.</div>';
    include '../includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resi = mysqli_real_escape_string($conn, $_POST['resi']);

    // Update pesanan
    mysqli_query($conn, "UPDATE pesanan SET status='Dikirim', resi='$resi' WHERE id=$id");

    // Kirim email notifikasi
    include '../includes/mailer.php';
    $subject = 'Pesanan Anda Dikirim';
    $message = "Halo {$pesanan['nama']}, pesanan Anda telah dikirim.\nNomor Resi: $resi\nTerima kasih telah berbelanja di toko kami.";
    kirim_email($pesanan['email'], $subject, $message);

    header("Location: data_pesanan.php");
    exit;
}
?>

<main class="container mx-auto px-4 py-8">
    <h2 class="text-2xl font-bold mb-6">📦 Kirim Pesanan</h2>

    <div class="bg-white p-6 rounded shadow space-y-3">
        <p><strong>Nama:</strong> <?= htmlspecialchars($pesanan['nama']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($pesanan['email']) ?></p>
        <p><strong>Total:</strong> Rp <?= number_format($pesanan['total']) ?></p>
        <p><strong>Status:</strong> <?= $pesanan['status'] ?></p>

        <form method="POST" class="space-y-4 mt-4">
            <label class="block">
                <span class="font-semibold">Nomor Resi Pengiriman:</span>
                <input type="text" name="resi" required class="mt-1 block w-full px-4 py-2 border rounded">
            </label>

            <div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Kirim Pesanan
                </button>
                <a href="data_pesanan.php" class="ml-4 text-gray-600 underline">Batal</a>
            </div>
        </form>
    </div>
</main>

<?php include '../includes/footer.php'; ?>