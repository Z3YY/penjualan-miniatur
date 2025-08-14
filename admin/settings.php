<?php
include '../includes/admin_auth.php'; // Pastikan hanya admin yang bisa mengakses
include '../includes/header.php';     // Header dan CSS
include '../config/db.php';           // Koneksi database

// Dapatkan ID admin yang sedang login untuk mencegah penghapusan/edit diri sendiri
$current_admin_id = $_SESSION['user_id'];

// Ambil daftar semua pengguna (users) dari database
// TIDAK PERNAH MENAMPILKAN PASSWORD DI SINI!
$users_query = mysqli_query($conn, "SELECT id, nama, email, role FROM users ORDER BY role ASC, nama ASC");

if (!$users_query) {
    die("Error fetching users: " . mysqli_error($conn));
}


?>

<main class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">⚙️ Pengaturan Akun Pengguna (Admin)</h1>

    <?php
    // Tampilkan pesan status dari sesi
    if (isset($_SESSION['status_message'])) {
        $msg_type = $_SESSION['status_message']['type'];
        $msg_content = $_SESSION['status_message']['message'];
        echo '<div class="mb-4 bg-' . ($msg_type == 'success' ? 'green' : 'red') . '-100 text-' . ($msg_type == 'success' ? 'green' : 'red') . '-800 px-4 py-3 rounded shadow">' . $msg_content . '</div>';
        unset($_SESSION['status_message']); // Hapus pesan setelah ditampilkan
    }
    ?>
    <?php
    // Tampilkan pesan sukses/error setelah operasi (edit/hapus)
    if (isset($_GET['status'])) {
        if ($_GET['status'] == 'success') {
            echo '<div class="mb-4 bg-green-100 text-green-800 px-4 py-3 rounded shadow">Operasi berhasil!</div>';
        } elseif ($_GET['status'] == 'error') {
            echo '<div class="mb-4 bg-red-100 text-red-800 px-4 py-3 rounded shadow">Terjadi kesalahan: ' . htmlspecialchars($_GET['message'] ?? 'Unknown error') . '</div>';
        } elseif ($_GET['status'] == 'self_delete_error') {
            echo '<div class="mb-4 bg-red-100 text-red-800 px-4 py-3 rounded shadow">Tidak bisa menghapus akun Anda sendiri!</div>';
        }
    }
    ?>

    <div class="overflow-x-auto bg-white shadow rounded-lg p-4">
        <table class="w-full text-sm text-left border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border-b">ID</th>
                    <th class="px-4 py-2 border-b">Nama</th>
                    <th class="px-4 py-2 border-b">Email</th>
                    <th class="px-4 py-2 border-b">Role</th>
                    <th class="px-4 py-2 border-b text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($users_query) > 0): ?>
                    <?php while ($user = mysqli_fetch_assoc($users_query)): ?>
                        <tr>
                            <td class="px-4 py-2 border-b"><?= htmlspecialchars($user['id']) ?></td>
                            <td class="px-4 py-2 border-b"><?= htmlspecialchars($user['nama']) ?></td>
                            <td class="px-4 py-2 border-b"><?= htmlspecialchars($user['email']) ?></td>
                            <td class="px-4 py-2 border-b"><?= htmlspecialchars($user['role']) ?></td>
                            <td class="px-4 py-2 border-b text-center space-x-2">
                                <a href="edit_user.php?id=<?= $user['id'] ?>" class="text-blue-600 hover:text-blue-800 underline">Edit</a>

                                <?php if ($user['id'] != $current_admin_id): // Jangan biarkan admin menghapus dirinya sendiri 
                                ?>
                                    <form action="process/delete_user.php" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus akun <?= htmlspecialchars($user['nama']) ?>? Aksi ini tidak bisa dibatalkan!')">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <button type="submit" class="text-red-600 hover:text-red-800 underline bg-transparent border-none p-0 cursor-pointer">Hapus</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-gray-500 italic">(Anda)</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-4 py-2 text-center text-gray-500">Tidak ada pengguna ditemukan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include '../includes/footer.php'; // Footer 
?>