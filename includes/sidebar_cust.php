<aside class="w-64 bg-white border-r border-gray-200 min-h-screen p-4 hidden md:block">
    <div class="mb-6">
        <p class="text-sm text-gray-500">Halo, **<?= htmlspecialchars($_SESSION['nama'] ?? 'Pengguna') ?>**</p>
    </div>

    <nav class="space-y-2">
        <a href="home.php" class="block px-3 py-2 rounded hover:bg-blue-100 <?= $currentPage === 'home.php' ? 'bg-blue-200 font-semibold' : '' ?>">🏠 Beranda</a>
        <a href="keranjang.php" class="block px-3 py-2 rounded hover:bg-blue-100 <?= $currentPage === 'keranjang.php' ? 'bg-blue-200 font-semibold' : '' ?>">🛒 Keranjang</a>
        <a href="riwayat.php" class="block px-3 py-2 rounded hover:bg-blue-100 <?= $currentPage === 'riwayat.php' ? 'bg-blue-200 font-semibold' : '' ?>">🧾 Riwayat Pesanan</a>
        <a href="settings.php" class="block px-3 py-2 rounded hover:bg-blue-100 <?= basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'bg-blue-200 font-semibold' : '' ?>">⚙️ Pengaturan Akun</a>
    </nav>

    <div class="mt-10">
        <a href="../logout.php" class="block text-red-600 hover:underline">🚪 Logout</a>
    </div>
</aside>