<?php

include '../includes/auth.php'; // File ini diasumsikan menangani otentikasi user
include '../includes/header.php'; // Header HTML Anda
include '../config/db.php';     // File koneksi database

// 1. Validasi dan Sanitasi ID Produk dari URL
$id_produk = $_GET['id'] ?? null; // Gunakan null coalescing operator

if (!$id_produk || !is_numeric($id_produk)) {
  echo "<main class='container mx-auto px-4 py-8'><p class='text-red-500 text-center mt-10'>ID Produk tidak valid.</p></main>";
  include '../includes/footer.php';
  exit;
}

// 2. Ambil Data Produk Menggunakan Prepared Statement (PENTING untuk keamanan)
$query_produk = "SELECT * FROM produk WHERE id = ?";
$stmt_produk = mysqli_prepare($conn, $query_produk);

if ($stmt_produk === false) {
  // Handle error jika prepared statement gagal
  die("Error preparing statement: " . mysqli_error($conn));
}

// "i" menunjukkan bahwa $id_produk adalah integer
mysqli_stmt_bind_param($stmt_produk, "i", $id_produk);
mysqli_stmt_execute($stmt_produk);
$result_produk = mysqli_stmt_get_result($stmt_produk);
$produk = mysqli_fetch_assoc($result_produk);

mysqli_stmt_close($stmt_produk); // Tutup statement setelah digunakan

// 3. Periksa apakah produk ditemukan
if (!$produk) {
  echo "<main class='container mx-auto px-4 py-8'><p class='text-red-500 text-center mt-10'>Produk tidak ditemukan.</p></main>";
  include '../includes/footer.php';
  exit;
}
?>

<main class="container mx-auto px-4 py-8">
  <div class="flex flex-col md:flex-row gap-6">
    <?php
    // Pastikan path ke sidebar_cust.php benar relatif dari lokasi file ini
    include '../includes/sidebar_cust.php';
    ?>

    <div class="md:w-1/2 flex justify-center items-center">
      <img src="../uploads/produk/<?= htmlspecialchars($produk['gambar']) ?>" alt="<?= htmlspecialchars($produk['nama']) ?>" class="w-full h-auto max-h-96 object-contain rounded shadow-lg">
    </div>

    <div class="md:w-1/2">
      <h1 class="text-3xl font-bold text-gray-800 mb-2"><?= htmlspecialchars($produk['nama']) ?></h1>
      <p class="text-blue-600 font-bold text-2xl mb-4">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></p>
      <p class="text-gray-600 leading-relaxed mb-6"><?= nl2br(htmlspecialchars($produk['deskripsi'])) ?></p>

      <div class="mb-6">
        <p class="text-gray-700">Stok Tersedia:
          <span class="font-semibold <?= $produk['stok'] > 0 ? 'text-green-600' : 'text-red-600' ?>">
            <?= $produk['stok'] > 0 ? $produk['stok'] : 'Stok Habis' ?>
          </span>
        </p>
      </div>

      <?php if ($produk['stok'] > 0): // Hanya tampilkan form jika stok lebih dari 0 
      ?>
        <form action="../process/add_to_cart.php" method="POST" class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
          <input type="hidden" name="produk_id" value="<?= htmlspecialchars($produk['id']) ?>">
          <input type="number" name="jumlah" value="1" min="1" max="<?= $produk['stok'] ?>"
            class="w-24 border border-gray-300 rounded-md px-3 py-2 text-center text-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            aria-label="Jumlah Produk">
          <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-300 ease-in-out shadow-md text-lg">
            + Tambah ke Keranjang
          </button>
        </form>
      <?php else: ?>
        <p class="text-red-600 font-bold text-xl">Produk tidak tersedia untuk dibeli saat ini.</p>
      <?php endif; ?>
    </div>
  </div>

</main>

<?php include '../includes/footer.php'; ?>