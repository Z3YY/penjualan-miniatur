<?php

include '../includes/auth.php'; // Ini seharusnya mengandung pengecekan user login
include '../config/db.php';
include '../includes/header.php'; // Memuat header dengan navigasi yang benar

// Pastikan user_id ada di sesi
// Asumsi 'auth.php' sudah memastikan $_SESSION['user_id'] ada
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php'); // Redirect jika belum login
    exit();
}

$current_user_id = $_SESSION['user_id']; // Menggunakan $_SESSION['user_id']

// Ambil ID pesanan dari URL
$id_pesanan = $_GET['id'] ?? null; // Gunakan null coalescing operator untuk mencegah error undefined index

// Validasi dan sanitasi ID pesanan
if (!$id_pesanan || !is_numeric($id_pesanan)) {
    echo "<main class='container mx-auto px-4 py-8'><p class='text-red-500'>ID Pesanan tidak valid.</p></main>";
    include '../includes/footer.php';
    exit();
}

// Lindungi dari SQL Injection
$id_pesanan_safe = mysqli_real_escape_string($conn, $id_pesanan);
$current_user_id_safe = mysqli_real_escape_string($conn, $current_user_id);

// --- 1. Pastikan pesanan milik user ini ---
$query_cek_pesanan = "SELECT * FROM pesanan WHERE id = '$id_pesanan_safe' AND user_id = '$current_user_id_safe'";
$result_cek_pesanan = mysqli_query($conn, $query_cek_pesanan);

if (!$result_cek_pesanan || mysqli_num_rows($result_cek_pesanan) == 0) {
    echo "<main class='container mx-auto px-4 py-8'><p class='text-red-500'>Pesanan tidak ditemukan atau bukan milik Anda.</p></main>";
    include '../includes/footer.php';
    exit();
}
// Simpan detail pesanan utama ke variabel $pesanan
$pesanan_utama = mysqli_fetch_assoc($result_cek_pesanan);


// --- 2. Ambil detail item dalam pesanan ---
$query_detail_items = "
    SELECT d.*, p.nama, p.gambar
    FROM pesanan_detail d
    JOIN produk p ON d.produk_id = p.id
    WHERE d.pesanan_id = '$id_pesanan_safe'
";
$result_detail_items = mysqli_query($conn, $query_detail_items);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan #<?= htmlspecialchars($id_pesanan) ?> | MiniaturStore</title>
    <link href="../assets/css/output.css" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-screen">

    <main class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6 text-blue-700">Detail Pesanan #<?= htmlspecialchars($id_pesanan) ?></h1>
        <a href="riwayat.php" class="inline-block mb-6 text-blue-600 hover:underline">← Kembali ke Riwayat Pesanan</a>

        <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Informasi Pesanan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                <div>
                    <p><strong>Status:</strong> <span class="font-bold text-blue-600"><?= htmlspecialchars($pesanan_utama['status']) ?></span></p>
                    <p><strong>Tanggal Pesan:</strong> <?= htmlspecialchars(date('d F Y H:i', strtotime($pesanan_utama['created_at']))) ?></p>
                    <p><strong>Total Pembayaran:</strong> Rp <?= number_format($pesanan_utama['total'], 0, ',', '.') ?></p>
                </div>
                <div>
                    <p><strong>Alamat Pengiriman:</strong> <?= nl2br(htmlspecialchars($pesanan_utama['alamat'])) ?></p>
                </div>
            </div>
        </div>


        <div class="bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Daftar Produk</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm border-collapse">
                    <thead class="bg-gray-100 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Produk</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Harga Satuan</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Jumlah</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result_detail_items) > 0): ?>
                            <?php while ($item = mysqli_fetch_assoc($result_detail_items)) { ?>
                                <tr class="border-b border-gray-200 last:border-b-0 hover:bg-gray-50">
                                    <td class="px-4 py-3 flex items-center">
                                        <img src="../uploads/produk/<?= htmlspecialchars($item['gambar']) ?>" alt="<?= htmlspecialchars($item['nama']) ?>" class="w-16 h-16 object-cover rounded-md mr-3 shadow-sm">
                                        <span class="text-gray-800"><?= htmlspecialchars($item['nama']) ?></span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-800">Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                                    <td class="px-4 py-3 text-gray-800"><?= $item['jumlah'] ?></td>
                                    <td class="px-4 py-3 text-gray-800">Rp <?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.') ?></td>
                                </tr>
                            <?php } ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="px-4 py-4 text-center text-gray-500">Tidak ada item dalam pesanan ini.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8 text-center">
            <a href="../customer/invoice.php?id=<?= htmlspecialchars($id_pesanan) ?>"
                target="_blank"
                class="inline-block bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition duration-300 ease-in-out shadow-md">
                🧾 Cetak Invoice
            </a>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>

</html>