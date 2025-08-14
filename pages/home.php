<?php
include '../includes/auth.php';
include '../includes/header.php';
include '../config/db.php';

// Ambil 4 produk terlaris
$terlaris_query = mysqli_query($conn, "
  SELECT pr.*, SUM(pd.jumlah) AS total_terjual
  FROM pesanan_detail pd
  JOIN produk pr ON pr.id = pd.produk_id
  GROUP BY pd.produk_id
  ORDER BY total_terjual DESC
  LIMIT 4
");
if (!$terlaris_query) {
  die("Gagal mengambil produk terlaris: " . mysqli_error($conn));
}

// Ambil semua produk untuk daftar lengkap
$produk_query = mysqli_query($conn, "SELECT * FROM produk ORDER BY id DESC");
if (!$produk_query) {
  die("Gagal mengambil semua produk: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Beranda Customer - MiniaturStore</title>
  <link href="../assets/css/output.css" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-screen">
  <div class="flex">
    <?php include '../includes/sidebar_cust.php'; ?>

    <main class="flex-1 p-6">
      <h1 class="text-3xl font-bold mb-4 text-blue-700">Selamat Datang di MiniaturStore</h1>
      <p class="text-gray-600 mb-6">Temukan berbagai miniatur unik dan menarik untuk koleksimu!</p>

      <!-- PRODUK TERLARIS -->
      <h2 class="text-xl font-semibold mb-4 text-red-600">🔥 4 Produk Terlaris</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-10">
        <?php if (mysqli_num_rows($terlaris_query) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($terlaris_query)) { ?>
            <div class="bg-white shadow rounded overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-lg">
              <img src="../uploads/produk/<?= htmlspecialchars($row['gambar']) ?>" alt="<?= htmlspecialchars($row['nama']) ?>" class="w-full h-48 object-cover">
              <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-800 truncate"><?= htmlspecialchars($row['nama']) ?></h3>
                <p class="text-blue-600 font-bold mt-1">Rp <?= number_format($row['harga']) ?></p>
                <a href="produk.php?id=<?= htmlspecialchars($row['id']) ?>" class="inline-block mt-3 text-sm text-white bg-blue-600 px-4 py-2 rounded-full hover:bg-blue-700 transition duration-300">Lihat Detail</a>
              </div>
            </div>
          <?php } ?>
        <?php else: ?>
          <p class="col-span-full text-center text-gray-500">Belum ada data produk terlaris.</p>
        <?php endif; ?>
      </div>

      <!-- SEMUA PRODUK -->
      <h2 class="text-xl font-semibold mb-4 text-green-700">📦 Semua Produk</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php if (mysqli_num_rows($produk_query) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($produk_query)) { ?>
            <div class="bg-white shadow rounded overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-lg">
              <img src="../uploads/produk/<?= htmlspecialchars($row['gambar']) ?>" alt="<?= htmlspecialchars($row['nama']) ?>" class="w-full h-48 object-cover">
              <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-800 truncate"><?= htmlspecialchars($row['nama']) ?></h3>
                <p class="text-blue-600 font-bold mt-1">Rp <?= number_format($row['harga']) ?></p>
                <a href="produk.php?id=<?= htmlspecialchars($row['id']) ?>" class="inline-block mt-3 text-sm text-white bg-blue-600 px-4 py-2 rounded-full hover:bg-blue-700 transition duration-300">Lihat Detail</a>
              </div>
            </div>
          <?php } ?>
        <?php else: ?>
          <p class="col-span-full text-center text-gray-500">Belum ada produk yang tersedia.</p>
        <?php endif; ?>
      </div>
    </main>
  </div>

  <?php include '../includes/footer.php'; ?>
</body>

</html>