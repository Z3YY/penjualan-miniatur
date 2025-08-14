<?php
include '../includes/admin_auth.php'; // Pastikan hanya admin yang bisa mengakses
include '../includes/header.php';     // Header dan CSS
include '../config/db.php';           // Koneksi database

$user_id = $_GET['id'] ?? null; // Ambil ID pengguna dari URL

// Validasi ID Pengguna
if (!$user_id || !is_numeric($user_id)) {
    $_SESSION['status_message'] = ['type' => 'error', 'message' => 'ID Pengguna tidak valid.'];
    header('Location: settings.php');
    exit;
}

$user_id_safe = mysqli_real_escape_string($conn, $user_id);

// Ambil data pengguna yang akan diedit
$user_query = mysqli_query($conn, "SELECT id, nama, email, role FROM users WHERE id = '$user_id_safe'");

if (!$user_query || mysqli_num_rows($user_query) == 0) {
    $_SESSION['status_message'] = ['type' => 'error', 'message' => 'Pengguna tidak ditemukan.'];
    header('Location: settings.php');
    exit;
}

$user_data = mysqli_fetch_assoc($user_query);

// Dapatkan ID admin yang sedang login untuk mencegah perubahan role diri sendiri yang tidak disengaja
$current_admin_id = $_SESSION['user_id'];

// Proses form jika ada data POST (saat form disubmit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_nama  = mysqli_real_escape_string($conn, $_POST['nama'] ?? '');
    $new_email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $new_role  = mysqli_real_escape_string($conn, $_POST['role'] ?? '');

    // Validasi dasar
    if (empty($new_nama) || empty($new_email) || empty($new_role)) {
        $_SESSION['status_message'] = ['type' => 'error', 'message' => 'Semua field wajib diisi.'];
        header('Location: edit_user.php?id=' . $user_id);
        exit;
    }

    // Pastikan role yang di-submit adalah role yang valid (misal: 'customer', 'admin')
    $valid_roles = ['customer', 'admin']; // Sesuaikan dengan ENUM role di database Anda
    if (!in_array($new_role, $valid_roles)) {
        $_SESSION['status_message'] = ['type' => 'error', 'message' => 'Role tidak valid.'];
        header('Location: edit_user.php?id=' . $user_id);
        exit;
    }

    // Pencegahan: Admin tidak bisa mengubah role-nya sendiri menjadi non-admin atau menghapus dirinya
    // Ini adalah langkah pengamanan jika role 'admin' ingin diubah
    if ($user_data['id'] == $current_admin_id && $new_role !== 'admin') {
        $_SESSION['status_message'] = ['type' => 'error', 'message' => 'Anda tidak bisa mengubah role akun Anda sendiri menjadi non-admin.'];
        header('Location: edit_user.php?id=' . $user_id);
        exit;
    }


    // Query UPDATE data pengguna
    $update_query = "UPDATE users SET nama = '$new_nama', email = '$new_email', role = '$new_role' WHERE id = '$user_id_safe'";

    if (mysqli_query($conn, $update_query)) {
        $_SESSION['status_message'] = ['type' => 'success', 'message' => 'Data pengguna berhasil diperbarui.'];
        header('Location: settings.php'); // Kembali ke halaman daftar pengaturan
        exit;
    } else {
        $_SESSION['status_message'] = ['type' => 'error', 'message' => 'Gagal memperbarui data pengguna: ' . mysqli_error($conn)];
        header('Location: edit_user.php?id=' . $user_id); // Kembali ke form dengan error
        exit;
    }
}
?>

<main class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">⚙️ Edit Akun Pengguna</h1>


    <div class="bg-white p-6 rounded-lg shadow-md max-w-md mx-auto">
        <form action="edit_user.php?id=<?= $user_data['id'] ?>" method="POST">
            <div class="mb-4">
                <label for="nama" class="block text-gray-700 text-sm font-bold mb-2">Nama:</label>
                <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($user_data['nama']) ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user_data['email']) ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-6">
                <label for="role" class="block text-gray-700 text-sm font-bold mb-2">Role:</label>
                <select id="role" name="role" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="customer" <?= $user_data['role'] == 'customer' ? 'selected' : '' ?>>User</option>
                    <option value="admin" <?= $user_data['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
                <?php if ($user_data['id'] == $current_admin_id): ?>
                    <p class="text-xs text-gray-500 mt-1">Anda tidak dapat mengubah role akun Anda sendiri di sini.</p>
                <?php endif; ?>
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Simpan Perubahan
                </button>
                <a href="settings.php" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Batal
                </a>
            </div>
        </form>
    </div>
</main>

<?php include '../includes/footer.php'; ?>