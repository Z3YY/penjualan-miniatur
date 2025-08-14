<?php
include '../includes/admin_auth.php';
include '../config/db.php';

$id = $_GET['id'];
$produk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM produk WHERE id = $id"));

if (!$produk) {
  echo "Produk tidak ditemukan.";
  exit;
}

include '../includes/header.php';
?>

<main class="container mx-auto px-4 py-8">
  <h1 class="text-2xl font-bold mb-6">✏️ Edit Produk</h1>

  <form action="process/produk_update.php" method="POST" enctype="multipart/form-data" class="space-y-4">
    <input type="hidden" name="id" value="<?= $produk['id'] ?>">

    <label class="block">
      <span class="font-semibold">Nama Produk</span>
      <input type="text" name="nama" value="<?= $produk['nama'] ?>" required class="block w-full border px-4 py-2 rounded mt-1">
    </label>

    <label class="block">
      <span class="font-semibold">Harga</span>
      <input type="number" name="harga" value="<?= $produk['harga'] ?>" required class="block w-full border px-4 py-2 rounded mt-1">
    </label>

    <label class="block">
      <span class="font-semibold">Stok</span>
      <input type="number" name="stok" value="<?= $produk['stok'] ?>" required class="block w-full border px-4 py-2 rounded mt-1">
    </label>

    <label class="block">
      <span class="font-semibold">Deskripsi</span>
      <textarea name="deskripsi" rows="4" class="block w-full border px-4 py-2 rounded mt-1"><?= $produk['deskripsi'] ?></textarea>
    </label>

    <label class="block">
      <span class="font-semibold">Gambar (kosongkan jika tidak diganti)</span>
      <input type="file" name="gambar" accept=".jpg,.jpeg,.png,.webp" class="mt-1">
      <img src="../uploads/produk/<?= $produk['gambar'] ?>" class="w-40 mt-2 border">
    </label>

    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">💾 Simpan Perubahan</button>
  </form>
</main>

<?php include '../includes/footer.php'; ?>