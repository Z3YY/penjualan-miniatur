<?php
include '../includes/auth.php';         // Autentikasi customer
include '../includes/header.php';       // Header HTML
include '../config/db.php';             // Koneksi database

$user_id = $_SESSION['user_id'];
$user_id_safe = mysqli_real_escape_string($conn, $user_id);

// Ambil HASH password customer yang sedang login untuk verifikasi
$user_query = mysqli_query($conn, "SELECT password FROM users WHERE id = '$user_id_safe'");
if (!$user_query || mysqli_num_rows($user_query) == 0) {
    $_SESSION['status_message'] = ['type' => 'error', 'message' => 'Data pengguna tidak ditemukan.'];
    header('Location: settings.php');
    exit;
}
$user_data = mysqli_fetch_assoc($user_query);
$current_hashed_password_db = $user_data['password']; // Ini adalah hash password yang tersimpan

// Proses form jika ada data POST (saat form disubmit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old_password_input     = $_POST['old_password'] ?? '';
    $new_password_input     = $_POST['new_password'] ?? '';
    $confirm_password_input = $_POST['confirm_password'] ?? '';

    $errors = [];

    // 1. Verifikasi Password Lama
    if (empty($old_password_input)) {
        $errors[] = "Password lama tidak boleh kosong.";
    } elseif (!password_verify($old_password_input, $current_hashed_password_db)) {
        $errors[] = "Password lama salah.";
    }

    // 2. Validasi Password Baru
    if (empty($new_password_input)) {
        $errors[] = "Password baru tidak boleh kosong.";
    } elseif (strlen($new_password_input) < 6) { // Contoh: minimal 6 karakter
        $errors[] = "Password baru minimal 6 karakter.";
    } elseif ($new_password_input !== $confirm_password_input) {
        $errors[] = "Konfirmasi password baru tidak cocok.";
    }

    if (empty($errors)) {
        // Hash password baru
        $hashed_new_password = password_hash($new_password_input, PASSWORD_DEFAULT);

        // Lakukan UPDATE password ke database
        $update_query = "UPDATE users SET password = '$hashed_new_password' WHERE id = '$user_id_safe'";

        if (mysqli_query($conn, $update_query)) {
            $_SESSION['status_message'] = ['type' => 'success', 'message' => 'Password berhasil diubah.'];
            header('Location: settings.php'); // Kembali ke halaman pengaturan setelah sukses
            exit;
        } else {
            $_SESSION['status_message'] = ['type' => 'error', 'message' => 'Gagal mengubah password: ' . mysqli_error($conn)];
            header('Location: change_password.php'); // Tetap di halaman ini dengan error database
            exit;
        }
    } else {
        // Jika ada error validasi, simpan pesan ke sesi untuk ditampilkan
        $_SESSION['status_message'] = ['type' => 'error', 'message' => implode('<br>', $errors)];
        header('Location: change_password.php'); // Kembali ke halaman ini agar form bisa diisi ulang
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Ganti Password - MiniaturStore</title>
    <link href="../assets/css/output.css" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-screen">
    <?php // include '../includes/header.php'; // Sudah di-include di atas 
    ?>

    <div class="flex">
        <?php include '../includes/sidebar_cust.php'; // Sudah di-include di atas 
        ?>

        <main class="flex-1 p-6">
            <h1 class="text-2xl font-bold mb-6 text-center">🔑 Ganti Password Anda</h1>

            <?php
            // Tampilkan pesan status dari sesi
            if (isset($_SESSION['status_message'])) {
                $msg_type = $_SESSION['status_message']['type'];
                $msg_content = $_SESSION['status_message']['message'];
                echo '<div class="mb-4 bg-' . ($msg_type == 'success' ? 'green' : 'red') . '-100 text-' . ($msg_type == 'success' ? 'green' : 'red') . '-800 px-4 py-3 rounded shadow">' . $msg_content . '</div>';
                unset($_SESSION['status_message']); // Hapus pesan setelah ditampilkan
            }
            ?>

            <div class="bg-white p-6 rounded-lg shadow-md max-w-md mx-auto">
                <form action="change_password.php" method="POST">
                    <div class="mb-4">
                        <label for="old_password" class="block text-gray-700 text-sm font-bold mb-2">Password Lama:</label>
                        <input type="password" id="old_password" name="old_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <div class="mb-4">
                        <label for="new_password" class="block text-gray-700 text-sm font-bold mb-2">Password Baru:</label>
                        <input type="password" id="new_password" name="new_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <div class="mb-6">
                        <label for="confirm_password" class="block text-gray-700 text-sm font-bold mb-2">Konfirmasi Password Baru:</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>

                    <div class="flex items-center justify-between">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Ganti Password
                        </button>
                        <a href="settings.php" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>

</html>