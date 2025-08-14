<?php
// Pastikan session sudah dimulai di awal setiap kali header ini di-include

// Tentukan halaman yang sedang aktif untuk penandaan di navigasi
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<link href="../assets/css/output.css" rel="stylesheet">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<header class="bg-white shadow">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
            <a href="./dashboard.php" class="text-xl font-bold text-blue-600">🧱 MiniaturStore</a>
        <?php else: ?>
            <a href="./home.php" class="text-xl font-bold text-blue-600">🧱 MiniaturStore</a>
        <?php endif; ?>

        <nav class="space-x-4">
            <?php
            // Pastikan session_start() sudah dipanggil di awal setiap file yang menggunakan session,
            // seperti di config.php atau di halaman itu sendiri sebelum memanggil header.
            // Contoh: if (session_status() == PHP_SESSION_NONE) { session_start(); }

            if (isset($_SESSION['user_id'])): // JIKA PENGGUNA SUDAH LOGIN
                if ($_SESSION['role'] === 'admin'):
                    // Navigasi untuk Admin
            ?>
                    <a href="dashboard.php"
                        class="px-2 py-1 rounded <?= $currentPage === 'dashboard.php' ? 'text-white bg-blue-600' : 'text-gray-700 hover:text-blue-600' ?>">Dashboard</a>

                    <a href="data_pesanan.php"
                        class="px-2 py-1 rounded <?= $currentPage === 'data_pesanan.php' ? 'text-white bg-blue-600' : 'text-gray-700 hover:text-blue-600' ?>">Pesanan</a>

                    <a href="produk_index.php"
                        class="px-2 py-1 rounded <?= $currentPage === 'produk_index.php' ? 'text-white bg-blue-600' : 'text-gray-700 hover:text-blue-600' ?>">Produk</a>
                <?php else:
                    // Navigasi untuk Customer
                ?>
                    <a href="home.php"
                        class="px-2 py-1 rounded <?= $currentPage === 'home.php' ? 'text-white bg-blue-600' : 'text-gray-700 hover:text-blue-600' ?>">Beranda</a>

                    <a href="keranjang.php"
                        class="px-2 py-1 rounded <?= $currentPage === 'keranjang.php' ? 'text-white bg-blue-600' : 'text-gray-700 hover:text-blue-600' ?>">Keranjang</a>

                    <a href="riwayat.php"
                        class="px-2 py-1 rounded <?= $currentPage === 'riwayat.php' ? 'text-white bg-blue-600' : 'text-gray-700 hover:text-blue-600' ?>">Riwayat</a>
                <?php endif; ?>

                <span class="text-sm text-gray-500">Halo, <?= htmlspecialchars($_SESSION['nama']) ?></span>
                <a href="../logout.php" class="text-red-500 hover:underline">Logout</a>
            <?php else:
            ?>
                <a href="./pages/login.php" class="text-gray-700 hover:text-blue-600">Login</a>
                <a href=".  /pages/register.php" class="text-gray-700 hover:text-blue-600">Daftar</a>
            <?php endif; ?>
        </nav>
    </div>
</header>