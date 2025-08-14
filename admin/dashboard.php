<?php
include '../includes/admin_auth.php';
include '../includes/header.php';
include '../config/db.php';

// Ambil data
$jumlah_produk    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM produk"))['total'];
$jumlah_customer  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'customer'"))['total'];
$jumlah_pesanan   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pesanan"))['total'];
$total_pendapatan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total) AS total FROM pesanan WHERE status='Berhasil'"))['total'] ?? 0;

$statuses = ['Menunggu', 'Diproses', 'Dikirim', 'Berhasil', 'Dibatalkan']; // Harus 'Menunggu' dan 'Berhasil'
$status_count = [];
foreach ($statuses as $s) {
    // Pastikan nilai status disanitasi sebelum digunakan dalam query
    $safe_s = mysqli_real_escape_string($conn, $s);
    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM pesanan WHERE status='$safe_s'");
    // Periksa apakah query berhasil sebelum mencoba mengambil hasilnya
    if ($result) {
        $status_count[$s] = mysqli_fetch_assoc($result)['total'];
    } else {
        // Handle error jika query gagal (misalnya, log error)
        $status_count[$s] = 0; // Set ke 0 jika gagal
        error_log("Error fetching status count for '$s': " . mysqli_error($conn));
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link href="../assets/css/output.css" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-screen">

    <div class="flex">

        <!-- Sidebar -->
        <?php include '../includes/sidebar.php'; ?>

        <!-- Konten Utama -->
        <main class="flex-1 p-6">
            <h2 class="text-2xl font-bold mb-6">📊 Dashboard Admin</h2>

            <?php
            // Hitung jumlah pesanan menunggu pembayaran
            $verifikasi_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pesanan WHERE status='Menunggu' AND bukti_bayar IS NOT NULL"))['total'];
            ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-10">
                <div class="bg-white rounded shadow p-4">
                    <h3 class="text-sm text-gray-600">🛍️ Total Produk</h3>
                    <p class="text-xl font-semibold"><?= $jumlah_produk ?> produk</p>
                </div>
                <div class="bg-white rounded shadow p-4">
                    <h3 class="text-sm text-gray-600">👥 Total Customer</h3>
                    <p class="text-xl font-semibold"><?= $jumlah_customer ?> orang</p>
                </div>
                <div class="bg-white rounded shadow p-4">
                    <h3 class="text-sm text-gray-600">📦 Total Pesanan</h3>
                    <p class="text-xl font-semibold"><?= $jumlah_pesanan ?> pesanan</p>
                </div>
                <div class="bg-white rounded shadow p-4">
                    <h3 class="text-sm text-gray-600">💰 Total Pendapatan</h3>
                    <p class="text-xl font-semibold text-green-600">Rp <?= number_format($total_pendapatan) ?></p>
                </div>
            </div>

            <div class="bg-white rounded shadow p-4">
                <h3 class="text-lg font-semibold mb-4">📋 Ringkasan Status Pesanan</h3>
                <table class="w-full border border-gray-300 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-4 py-2 text-left">Status</th>
                            <th class="border px-4 py-2 text-left">Jumlah Pesanan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($status_count as $status_name => $total_count) { ?>
                            <tr>
                                <td class="border px-4 py-2"><?= htmlspecialchars($status_name) ?></td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($total_count) ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>

</html>