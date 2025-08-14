<?php
session_start(); // Pastikan session sudah dimulai
include '../includes/admin_auth.php'; // Ini akan melindungi halaman hanya untuk admin
include '../config/db.php';          // Koneksi database
include '../includes/header.php';     // Header yang sudah disesuaikan

// Ambil ID pesanan dari URL
$id_pesanan = $_GET['id'] ?? null;

// Validasi dan sanitasi ID pesanan
if (!$id_pesanan || !is_numeric($id_pesanan)) {
    echo "<main class='container mx-auto px-4 py-8'><p class='text-red-500'>ID Pesanan tidak valid.</p></main>";
    include '../includes/footer.php';
    exit();
}


// --- Ambil Data Pesanan Utama (Menggunakan Prepared Statement) ---
// Join dengan tabel users untuk mendapatkan nama customer dan email
$query_pesanan = "  
    SELECT p.*, u.nama AS nama_user, u.email
    FROM pesanan p
    JOIN users u ON p.user_id = u.id
    WHERE p.id = ?
";
$stmt_pesanan = mysqli_prepare($conn, $query_pesanan);

if ($stmt_pesanan === false) {
    die("Error preparing statement for main order data: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt_pesanan, "i", $id_pesanan);
mysqli_stmt_execute($stmt_pesanan);
$result_pesanan = mysqli_stmt_get_result($stmt_pesanan);

// Cek apakah pesanan ditemukan
if (!$result_pesanan || mysqli_num_rows($result_pesanan) == 0) {
    echo "<main class='container mx-auto px-4 py-8'><p class='text-red-500'>Pesanan tidak ditemukan.</p></main>";
    include '../includes/footer.php';
    exit();
}
$pesanan = mysqli_fetch_assoc($result_pesanan); // Menggunakan $pesanan (seperti di invoice.php)
mysqli_stmt_close($stmt_pesanan);

// --- Ambil Item Detail Pesanan (Menggunakan Prepared Statement) ---
// Ambil juga stok produk saat ini untuk referensi admin
$query_items = "
    SELECT pd.*, pr.nama AS nama_produk, pr.harga AS harga_produk_saat_ini, pr.gambar, pr.stok AS current_stok
    FROM pesanan_detail pd
    JOIN produk pr ON pd.produk_id = pr.id
    WHERE pd.pesanan_id = ?
";
$stmt_items = mysqli_prepare($conn, $query_items);
if ($stmt_items === false) {
    die("Error preparing statement for order items: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt_items, "i", $id_pesanan);
mysqli_stmt_execute($stmt_items);
$result_items = mysqli_stmt_get_result($stmt_items);
mysqli_stmt_close($stmt_items); // Tutup statement setelah digunakan

$alamat = $pesanan['alamat'] ?? '';
$parts = explode(',', $alamat); // Pisah berdasarkan koma
$prov_id = isset($parts[0]) ? (int) trim($parts[0]) : 0;

$shipping_cost = 0;

if ($prov_id > 0) {
    $query = mysqli_query($conn, "SELECT shipping_cost FROM provinces WHERE prov_id = $prov_id");

    if ($query && mysqli_num_rows($query) > 0) {
        $result = mysqli_fetch_assoc($query);
        $shipping_cost = $result['shipping_cost'];
    }
}


// Tentukan warna status untuk badge
$status_class = '';
switch ($pesanan['status']) {
    case 'Menunggu':
        $status_class = 'bg-yellow-100 text-yellow-800';
        break;
    case 'Diproses':
        $status_class = 'bg-blue-100 text-blue-800';
        break;
    case 'Dikirim':
        $status_class = 'bg-purple-100 text-purple-800';
        break;
    case 'Berhasil':
        $status_class = 'bg-green-100 text-green-800';
        break;
    case 'Dibatalkan':
        $status_class = 'bg-red-100 text-red-800';
        break;
    default:
        $status_class = 'bg-gray-100 text-gray-800';
        break;
}
?>

<main class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Detail Pesanan #<?= htmlspecialchars($pesanan['id']) ?></h1>
    <a href="data_pesanan.php" class="inline-block mb-6 text-blue-600 hover:underline">← Kembali ke Data Pesanan</a>

    <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">Informasi Pesanan</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
            <div>
                <p><strong>ID Pesanan:</strong> <span class="font-bold text-gray-900"><?= htmlspecialchars($pesanan['id']) ?></span></p>
                <p><strong>Customer:</strong> <?= htmlspecialchars($pesanan['nama_user']) ?></p>
                <p><strong>Email Customer:</strong> <?= htmlspecialchars($pesanan['email']) ?></p>
                <p><strong>Tanggal Pesan:</strong> <?= htmlspecialchars(date('d F Y H:i', strtotime($pesanan['created_at']))) ?></p>

                <p><strong>Pajak:</strong> <span class="font-bold text-green-600">Rp <?= number_format($pesanan['pajak'], 0, ',', '.') ?></span></p>
                <p><strong>Ongkos Kirim:</strong> Rp <?= number_format($shipping_cost, 0, ',', '.') ?></p>
                <p><strong>Total Pembayaran:</strong> <span class="font-bold text-green-600">Rp <?= number_format($pesanan['harga_total'], 0, ',', '.') ?></span></p>
            </div>
            <div>
                <p><strong>Status:</strong> <span class="font-bold px-3 py-1 rounded-full <?= $status_class ?>"><?= htmlspecialchars($pesanan['status']) ?></span></p>
                <p><strong>Alamat Pengiriman:</strong> <?= nl2br(htmlspecialchars($pesanan['alamat'])) ?></p>
                <?php if (!empty($pesanan['resi'])): ?>
                    <p><strong>Nomor Resi:</strong> <span class="font-bold text-purple-600"><?= htmlspecialchars($pesanan['resi']) ?></span></p>
                <?php endif; ?>

                <?php if (!empty($pesanan['bukti_bayar'])): ?>
                    <p class="mt-4"><strong>Bukti Pembayaran:</strong></p>
                    <a href="../uploads/bukti/<?= htmlspecialchars($pesanan['bukti_bayar']) ?>" target="_blank"
                        class="inline-block bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-300 mt-2">
                        Lihat Bukti Pembayaran
                    </a>
                <?php else: ?>
                    <p class="mt-4 text-gray-600 italic">Belum ada bukti pembayaran diunggah.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">Daftar Produk</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border-collapse">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Produk</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Harga Satuan (Saat Pesan)</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Jumlah</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Subtotal</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Stok Saat Ini</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result_items) > 0): ?>
                        <?php while ($item = mysqli_fetch_assoc($result_items)) { ?>
                            <tr class="border-b border-gray-200 last:border-b-0 hover:bg-gray-50">
                                <td class="px-4 py-3 flex items-center">
                                    <img src="../uploads/produk/<?= htmlspecialchars($item['gambar']) ?>" alt="<?= htmlspecialchars($item['nama_produk']) ?>" class="w-16 h-16 object-cover rounded-md mr-3 shadow-sm">
                                    <span class="text-gray-800"><?= htmlspecialchars($item['nama_produk']) ?></span>
                                </td>
                                <td class="px-4 py-3 text-gray-800">Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                                <td class="px-4 py-3 text-gray-800"><?= $item['jumlah'] ?></td>
                                <td class="px-4 py-3 text-gray-800">Rp <?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.') ?></td>
                                <td class="px-4 py-3 text-gray-800">Rp <?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.') ?></td>
                                <td class="px-4 py-3 text-gray-800">
                                    <span class="font-semibold <?= $item['current_stok'] > 0 ? 'text-green-600' : 'text-red-600' ?>">
                                        <?= $item['current_stok'] ?>
                                    </span>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-gray-500">Tidak ada item dalam pesanan ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">Aksi Pesanan</h2>
        <form method="POST" action="../process/verifikasi_pembayaran_process.php" class="space-y-4">
            <input type="hidden" name="id" value="<?= htmlspecialchars($pesanan['id']) ?>">

            <?php if ($pesanan['status'] === 'Menunggu'): ?>
                <?php if (!empty($pesanan['bukti_bayar'])): ?>
                    <div class="flex flex-wrap items-center gap-4">
                        <button type="submit" name="action" value="terima"
                            class="bg-green-600 text-white px-5 py-2 rounded-md hover:bg-green-700 transition duration-300 shadow-md">
                            ✅ Terima Pembayaran (Set Status ke Diproses)
                        </button>
                        <button type="submit" name="action" value="tolak"
                            class="bg-red-600 text-white px-5 py-2 rounded-md hover:bg-red-700 transition duration-300 shadow-md"
                            onclick="return confirm('Yakin ingin MENOLAK pembayaran dan membatalkan pesanan ini? Bukti pembayaran akan dihapus.')">
                            ❌ Tolak Pembayaran (Set Status ke Dibatalkan)
                        </button>
                    </div>
                <?php else: ?>
                    <p class="text-gray-600 italic">Menunggu customer mengunggah bukti pembayaran.</p>
                <?php endif; ?>
            <?php elseif ($pesanan['status'] === 'Diproses'): ?>
                <div class="flex flex-col md:flex-row items-start md:items-center gap-4">
                    <label for="resi" class="font-semibold whitespace-nowrap">Nomor Resi:</label>
                    <input type="text" id="resi" name="resi" placeholder="Masukkan nomor resi pengiriman" required
                        class="flex-grow border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    <button type="submit" name="action" value="kirim"
                        class="bg-orange-600 text-white px-5 py-2 rounded-md hover:bg-orange-700 transition duration-300 shadow-md"
                        onclick="return confirm('Yakin ingin mengubah status pesanan menjadi DIKIRIM dan menyimpan nomor resi?')">
                        🚚 Kirim Pesanan (Set Status ke Dikirim)
                    </button>
                </div>
            <?php elseif ($pesanan['status'] === 'Dikirim'): ?>
                <button type="submit" name="action" value="berhasil"
                    class="bg-teal-600 text-white px-5 py-2 rounded-md hover:bg-teal-700 transition duration-300 shadow-md"
                    onclick="return confirm('Yakin ingin mengubah status pesanan menjadi BERHASIL (pesanan sudah diterima customer)?')">
                    ✅ Selesai (Set Status ke Berhasil)
                </button>
            <?php elseif ($pesanan['status'] === 'Berhasil'): ?>
                <p class="text-green-600 font-semibold">Pesanan ini sudah berhasil dan selesai.</p>
            <?php elseif ($pesanan['status'] === 'Dibatalkan'): ?>
                <p class="text-red-600 font-semibold">Pesanan ini telah dibatalkan.</p>
            <?php endif; ?>

            <?php if ($pesanan['status'] !== 'Berhasil' && $pesanan['status'] !== 'Dibatalkan'): ?>
                <hr class="border-gray-200 my-4">
                <button type="submit" name="action" value="batalkan_admin"
                    class="bg-gray-500 text-white px-5 py-2 rounded-md hover:bg-gray-600 transition duration-300 shadow-md"
                    onclick="return confirm('PERINGATAN: Yakin ingin MEMBATALKAN pesanan ini? (Jika sudah diproses, stok tidak akan otomatis dikembalikan)')">
                    🚫 Batalkan Pesanan (Oleh Admin)
                </button>
            <?php endif; ?>
        </form>
    </div>

    <div class="mt-8 text-center">
        <a href="../customer/invoice.php?id=<?= htmlspecialchars($pesanan['id']) ?>"
            target="_blank"
            class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-300 ease-in-out shadow-md">
            🧾 Cetak Invoice
        </a>
    </div>
</main>

<?php include '../includes/footer.php'; ?>