<?php
include '../includes/auth.php';         // Autentikasi customer (pastikan session_start() ada di sini atau di auth.php)
include '../includes/header.php';       // Header HTML
include '../config/db.php';             // Koneksi database

// Ambil data customer yang sedang login
$user_id = $_SESSION['user_id'];
$user_id_safe = mysqli_real_escape_string($conn, $user_id);

$user_query = mysqli_query($conn, "SELECT id, nama, email, role FROM users WHERE id = '$user_id_safe'");
if (!$user_query || mysqli_num_rows($user_query) == 0) {
    // Jika data pengguna tidak ditemukan (seharusnya tidak terjadi jika sudah login)
    header('Location: ../logout.php'); // Log out saja
    exit;
}
$user_data = mysqli_fetch_assoc($user_query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pengaturan Akun - MiniaturStore</title>
    <link href="../assets/css/output.css" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-screen">
    <?php // include '../includes/header.php'; // sudah di-include di atas 
    ?>

    <div class="flex">
        <?php include '../includes/sidebar_cust.php'; // sudah di-include di atas 
        ?>

        <main class="flex-1 p-6">
            <h1 class="text-2xl font-bold mb-6">⚙️ Pengaturan Akun Anda</h1>

            <?php
            // Tampilkan pesan sukses/error dari sesi (setelah edit/ganti password/hapus)
            if (isset($_SESSION['status_message'])) {
                $msg_type = $_SESSION['status_message']['type'];
                $msg_content = $_SESSION['status_message']['message'];
                echo '<div class="mb-4 bg-' . ($msg_type == 'success' ? 'green' : 'red') . '-100 text-' . ($msg_type == 'success' ? 'green' : 'red') . '-800 px-4 py-3 rounded shadow">' . $msg_content . '</div>';
                unset($_SESSION['status_message']); // Hapus pesan setelah ditampilkan
            }
            ?>

            <div class="bg-white p-6 rounded-lg shadow-md max-w-lg mx-auto">
                <h2 class="text-lg font-semibold mb-4">Informasi Akun Anda</h2>
                <div class="space-y-2 mb-6">
                    <p><strong>Nama:</strong> <?= htmlspecialchars($user_data['nama']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($user_data['email']) ?></p>
                    <p><strong>Role:</strong> <?= htmlspecialchars($user_data['role']) ?></p>
                </div>

                <h2 class="text-lg font-semibold mb-4">Pilihan Aksi</h2>
                <div class="flex flex-col space-y-3">
                    <a href="edit_profile.php" class="bg-blue-500 hover:bg-blue-600 text-black font-bold py-2 px-4 rounded text-left">
                        Edit Profil
                    </a>
                    <a href="change_password.php" class="bg-green-500 hover:bg-green-600 text-black font-bold py-2 px-4 rounded text-center">
                        Ganti Password
                    </a>
                    <form action="../process/delete_my_account.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun Anda? Semua data pesanan Anda juga akan hilang. Aksi ini tidak bisa dibatalkan!')">
                        <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-black font-bold py-2 px-4 rounded text-right">
                            Hapus Akun
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>

</html>