<?php


include '../includes/admin_auth.php'; // Ini akan melindungi halaman hanya untuk admin
include '../config/db.php';          // Koneksi database
include '../includes/header.php';    // Header yang sudah disesuaikan

$produk = mysqli_query($conn, "SELECT * FROM produk ORDER BY id DESC");
?>

<main class="container mx-auto px-4 py-6">
  <div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">🛍️ Daftar Produk</h1>
    <a href="produk_tambah.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Tambah Produk</a>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm border">
      <thead class="bg-gray-100">
        <tr>
          <th class="border px-4 py-2">Gambar</th>
          <th class="border px-4 py-2">Nama</th>
          <th class="border px-4 py-2">Harga</th>
          <th class="border px-4 py-2">Stok</th>
          <th class="border px-4 py-2">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($p = mysqli_fetch_assoc($produk)): ?>
          <tr>
            <td class="border px-4 py-2">
              <img src="../uploads/produk/<?= htmlspecialchars($p['gambar']) ?>" alt="Gambar Produk" class="w-16 h-16 object-cover rounded">
            </td>
            <td class="border px-4 py-2"><?= htmlspecialchars($p['nama']) ?></td>
            <td class="border px-4 py-2">Rp <?= number_format($p['harga']) ?></td>
            <td class="border px-4 py-2"><?= $p['stok'] ?></td>
            <td class="px-4 py-2 border whitespace-nowrap">
              <a href="produk_edit.php?id=<?= $p['id'] ?>"
                class="inline-block px-3 py-1 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700 transition duration-300 ease-in-out shadow-sm">
                Edit
              </a>

              <form method="POST" action="process/produk_hapus_process.php"
                onsubmit="return confirm('Yakin ingin menghapus produk ini?')"
                class="inline-block ml-2"> <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <button type="submit"
                  class="bg-red-600 text-white px-3 py-1 rounded-md hover:bg-red-700 transition duration-300 ease-in-out text-sm shadow-sm">
                  Hapus
                </button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</main>

<?php include '../includes/footer.php'; ?>