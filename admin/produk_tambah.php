<?php
include '../includes/admin_auth.php';
include '../includes/header.php';
?>

<main class="container mx-auto px-4 py-6">
  <h1 class="text-2xl font-bold mb-6">➕ Tambah Produk</h1>

  <form action="../process/tambah_produk_process.php" method="POST" enctype="multipart/form-data" class="space-y-4">
    <div>
      <label class="block font-semibold mb-1">Nama Produk</label>
      <input type="text" name="nama" required class="w-full border rounded px-4 py-2">
    </div>

    <div>
      <label class="block font-semibold mb-1">Harga (Rp)</label>
      <input type="number" name="harga" required class="w-full border rounded px-4 py-2">
    </div>

    <div>
      <label class="block font-semibold mb-1">Stok</label>
      <input type="number" name="stok" required class="w-full border rounded px-4 py-2">
    </div>

    <div>
      <label class="block font-semibold mb-1">Deskripsi</label>
      <textarea name="deskripsi" rows="4" class="w-full border rounded px-4 py-2"></textarea>
    </div>

    <div>
      <label class="block font-semibold mb-1">Gambar Produk</label>
      <input type="file" name="gambar" accept="image/*" required class="w-full">
    </div>

    <button type="submit" name="tambah" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
      Simpan Produk
    </button>
  </form>
</main>

<?php include '../includes/footer.php'; ?>