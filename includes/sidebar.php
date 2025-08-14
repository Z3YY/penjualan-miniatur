<aside class="w-64 bg-white border-r border-gray-200 min-h-screen p-4 hidden md:block">
  <div class="mb-6">
    <p class="text-sm text-gray-500">Admin Panel</p>
  </div>

  <nav class="space-y-2">
    <a href="data_pesanan.php" class="block px-3 py-2 rounded hover:bg-blue-100 <?= basename($_SERVER['PHP_SELF']) === 'data_pesanan.php' ? 'bg-blue-200 font-semibold' : '' ?>">📦 Data Pesanan</a>
    <a href="produk_index.php" class="block px-3 py-2 rounded hover:bg-blue-100 <?= basename($_SERVER['PHP_SELF']) === 'produk_index.php' ? 'bg-blue-200 font-semibold' : '' ?>">🛍️ Produk</a>
    <a href="produk_tambah.php" class="block px-3 py-2 rounded hover:bg-blue-100 <?= basename($_SERVER['PHP_SELF']) === 'produk_tambah.php' ? 'bg-blue-200 font-semibold' : '' ?>">➕ Tambah Produk</a>
    <a href="settings.php" class="block px-3 py-2 rounded hover:bg-blue-100 <?= basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'bg-blue-200 font-semibold' : '' ?>">⚙️ Pengaturan Akun</a>
  </nav>

  <div class="mt-10">
    <a href="../logout.php" class="block text-red-600 hover:underline">🚪 Logout</a>
  </div>
</aside>