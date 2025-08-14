<?php
include '../includes/auth.php';
include '../includes/header.php';
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil isi keranjang user
$data = mysqli_query($conn, "
  SELECT k.*, p.nama, p.harga, p.gambar 
  FROM keranjang k 
  JOIN produk p ON k.produk_id = p.id 
  WHERE k.user_id = $user_id
");
?>

<main class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">🛒 Keranjang Belanja</h1>

    <?php if (mysqli_num_rows($data) == 0) : ?>
        <p class="text-gray-600">Keranjang kamu masih kosong.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 text-left">Produk</th>
                        <th class="p-3 text-left">Harga</th>
                        <th class="p-3 text-left">Jumlah</th>
                        <th class="p-3 text-left">Subtotal</th>
                        <th class="p-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <?php
                    $total = 0;
                    while ($row = mysqli_fetch_assoc($data)) :
                        $subtotal = $row['harga'] * $row['jumlah'];
                        $total += $subtotal;
                    ?>
                        <tr class="border-t">
                            <td class="p-3 flex items-center gap-3">
                                <img src="../uploads/produk/<?= $row['gambar'] ?>" alt="" class="w-12 h-12 object-cover rounded">
                                <?= htmlspecialchars($row['nama']) ?>
                            </td>
                            <td class="p-3">Rp <?= number_format($row['harga']) ?></td>
                            <td class="p-3"><?= $row['jumlah'] ?></td>
                            <td class="p-3">Rp <?= number_format($subtotal) ?></td>
                            <td class="p-3">
                                <a href="../process/hapus_keranjang.php?id=<?= $row['id'] ?>" class="text-red-600 hover:underline">🗑️ Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                    <?php
                    // ✅ Tambahan perhitungan pajak & harga total
                    $pajak = round($total * 0.11); // 11% PPN
                    $harga_total = $total + $pajak;
                    ?>

                    <tr class="font-bold border-t">
                        <td colspan="3" class="p-3 text-right">Total:</td>
                        <td class="p-3">Rp <?= number_format($total) ?></td>
                        <td class="p-3"></td>
                    </tr>
                    <tr class="border-t">
                        <td colspan="3" class="p-3 text-right">Pajak (11%):</td>
                        <td class="p-3">Rp <?= number_format($pajak) ?></td>
                        <td class="p-3"></td>
                    </tr>
                    <tr class="font-bold border-t text-blue-700">
                        <td colspan="3" class="p-3 text-right">Harga Total:</td>
                        <td class="p-3">Rp <?= number_format($harga_total) ?></td>
                        <td class="p-3"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-6 text-right">
            <a href="checkout.php" class="inline-block bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700">
                🧾 Checkout Sekarang
            </a>
        </div>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>