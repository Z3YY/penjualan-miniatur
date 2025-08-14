<?php
// Pastikan admin_auth.php sudah memanggil session_start() dan melakukan pengecekan role admin
include '../includes/admin_auth.php'; // Ini harusnya menangani otentikasi admin
include '../config/db.php';         // Koneksi database
include '../includes/header.php';   // Header HTML

// --- PERBAIKAN DI SINI: Validasi dan Sanitasi ID Pesanan ---
// Baris 6 (sebelumnya): $id = $_GET['id'];
$id_pesanan = $_GET['id'] ?? null; // Menggunakan null coalescing operator untuk menghindari "Undefined array key"

// Periksa apakah ID pesanan valid dan bukan kosong
if (!$id_pesanan || !is_numeric($id_pesanan)) {
    // Jika ID tidak ada atau tidak valid, tampilkan pesan error yang jelas dan keluar
    echo "<p class='p-4 text-red-600 text-center'>ID Pesanan tidak valid atau tidak ditemukan.</p>";
    include '../includes/footer.php';
    exit;
}

// Sanitasi ID untuk mencegah SQL Injection
$id_pesanan_safe = mysqli_real_escape_string($conn, $id_pesanan);

// --- PERBAIKAN DI SINI: Query SQL yang Benar dan Lebih Lengkap ---
// Baris 7 (sebelumnya): $pesanan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pesanan WHERE id = $id"));
// Ambil juga nama user agar tampilannya lebih informatif
$query_pesanan = "SELECT p.*, u.nama AS nama_customer
                  FROM pesanan p
                  JOIN users u ON p.user_id = u.id
                  WHERE p.id = '$id_pesanan_safe'"; // Gunakan ID yang sudah disanitasi dan diapit kutip tunggal

$result_pesanan = mysqli_query($conn, $query_pesanan);

// Periksa apakah query berhasil dan pesanan ditemukan
if (!$result_pesanan) {
    echo "<p class='p-4 text-red-600 text-center'>Error mengambil data pesanan: " . mysqli_error($conn) . "</p>";
    include '../includes/footer.php';
    exit;
}

$pesanan = mysqli_fetch_assoc($result_pesanan);

// --- Pengecekan Pesanan dan Bukti Bayar ---
// Ini sudah cukup baik, tapi pastikan $pesanan['bukti_bayar'] benar-benar ada di tabel pesanan.
if (!$pesanan || empty($pesanan['bukti_bayar'])) { // Gunakan empty() untuk cek string kosong juga
    echo "<p class='p-4 text-center'>Pesanan tidak ditemukan atau belum ada bukti bayar untuk pesanan ini.</p>";
    include '../includes/footer.php';
    exit;
}

// Tambahan: Pastikan status pesanan memungkinkan verifikasi (misal: "Menunggu Pembayaran")
if ($pesanan['status'] !== 'Menunggu') {
    echo "<p class='p-4 text-orange-600 text-center'>Pembayaran untuk pesanan ini sudah diverifikasi atau statusnya tidak lagi 'Menunggu Pembayaran'.</p>";
    echo "<p class='p-4 text-center'><a href='data_pesanan.php' class='text-blue-600 hover:underline'>Kembali ke Daftar Pesanan</a></p>";
    include '../includes/footer.php';
    exit;
}

?>

<main class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">✅ Verifikasi Pembayaran</h1>

    <div class="bg-white shadow-lg rounded-lg p-6 max-w-xl mx-auto space-y-5">
        <h2 class="text-xl font-semibold mb-3 text-blue-700">Detail Pesanan #<?= htmlspecialchars($pesanan['id']) ?></h2>

        <p class="text-lg"><strong>Customer:</strong> <?= htmlspecialchars($pesanan['nama_customer']) ?></p>
        <p class="text-lg"><strong>Total Pembayaran:</strong> <span class="text-green-700 font-bold">Rp <?= number_format($pesanan['total'], 0, ',', '.') ?></span></p>
        <p class="text-lg"><strong>Metode Pembayaran:</strong> <?= htmlspecialchars($pesanan['metode_pembayaran'] ?? 'N/A') ?></p>
        <p class="text-lg"><strong>Status Saat Ini:</strong> <span class="font-bold text-blue-600"><?= htmlspecialchars($pesanan['status']) ?></span></p>
        <p class="text-lg"><strong>Tanggal Pesan:</strong> <?= htmlspecialchars(date('d F Y H:i', strtotime($pesanan['tanggal_pesan'] ?? $pesanan['created_at']))) ?></p>
        <p class="text-lg"><strong>Alamat Pengiriman:</strong> <?= nl2br(htmlspecialchars($pesanan['alamat_pengiriman'] ?? 'N/A')) ?></p>


        <div class="mb-4 text-center">
            <p class="font-semibold text-gray-700 text-xl mb-3">Bukti Pembayaran:</p>
            <img src="../uploads/bukti/<?= htmlspecialchars($pesanan['bukti_bayar']) ?>" alt="Bukti Pembayaran"
                class="border border-gray-300 rounded-lg mx-auto max-w-full h-auto" style="max-width: 400px;">
            <p class="text-sm text-gray-500 mt-2">Pastikan gambar bukti jelas dan sesuai.</p>
        </div>

        <form action="../process/verifikasi_pembayaran_process.php" method="POST" class="flex justify-center space-x-4 pt-4 border-t border-gray-200">
            <input type="hidden" name="id" value="<?= htmlspecialchars($pesanan['id']) ?>">
            <button type="submit" name="action" value="terima" class="bg-green-600 text-white px-6 py-3 rounded-lg text-lg font-semibold hover:bg-green-700 transition duration-300 shadow-md">
                ✅ Terima Pembayaran
            </button>
            <button type="submit" name="action" value="tolak" class="bg-red-600 text-white px-6 py-3 rounded-lg text-lg font-semibold hover:bg-red-700 transition duration-300 shadow-md">
                ❌ Tolak Pembayaran
            </button>
        </form>
    </div>
</main>

<?php include '../includes/footer.php'; ?>