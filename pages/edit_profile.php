<?php
include '../includes/auth.php';         // Autentikasi customer
include '../includes/header.php';       // Header HTML
include '../config/db.php';             // Koneksi database

$user_id = $_SESSION['user_id'];
$user_id_safe = mysqli_real_escape_string($conn, $user_id);

// Ambil data customer yang sedang login untuk ditampilkan di form
$user_query = mysqli_query($conn, "SELECT nama, email FROM users WHERE id = '$user_id_safe'");
if (!$user_query || mysqli_num_rows($user_query) == 0) {
    $_SESSION['status_message'] = ['type' => 'error', 'message' => 'Data pengguna tidak ditemukan.'];
    header('Location: settings.php');
    exit;
}
$user_data = mysqli_fetch_assoc($user_query);

// Proses form jika ada data POST (saat form disubmit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_nama  = mysqli_real_escape_string($conn, $_POST['nama'] ?? '');
    $new_email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');

    $errors = [];

    // Validasi Nama
    if (empty($new_nama)) {
        $errors[] = "Nama tidak boleh kosong.";
    }

    // Validasi Email
    if (empty($new_email)) {
        $errors[] = "Email tidak boleh kosong.";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid.";
    } else {
        // Cek apakah email sudah digunakan oleh pengguna lain (selain akun ini sendiri)
        $check_email_query = mysqli_query($conn, "SELECT id FROM users WHERE email = '$new_email' AND id != '$user_id_safe'");
        if (mysqli_num_rows($check_email_query) > 0) {
            $errors[] = "Email sudah digunakan oleh akun lain.";
        }
    }

    if (empty($errors)) {
        // Lakukan UPDATE ke database hanya untuk nama dan email
        $update_query = "UPDATE users SET nama = '$new_nama', email = '$new_email' WHERE id = '$user_id_safe'";

        if (mysqli_query($conn, $update_query)) {
            // Update session jika nama atau email berubah agar langsung tercermin di header/sidebar
            $_SESSION['nama'] = $new_nama;
            $_SESSION['email'] = $new_email; // Opsional, tergantung apakah email ditampilkan di session

            $_SESSION['status_message'] = ['type' => 'success', 'message' => 'Profil berhasil diperbarui.'];
            header('Location: settings.php'); // Kembali ke halaman pengaturan
            exit;
        } else {
            $_SESSION['status_message'] = ['type' => 'error', 'message' => 'Gagal memperbarui profil: ' . mysqli_error($conn)];
            header('Location: edit_profile.php'); // Tetap di halaman ini dengan error
            exit;
        }
    } else {
        // Jika ada error validasi, simpan pesan ke sesi untuk ditampilkan
        $_SESSION['status_message'] = ['type' => 'error', 'message' => implode('<br>', $errors)];
        header('Location: edit_profile.php'); // Kembali ke halaman ini agar form bisa diisi ulang
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Profil - MiniaturStore</title>
    <link href="../assets/css/output.css" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-screen">
    <?php // include '../includes/header.php'; // sudah di-include di atas 
    ?>

    <div class="flex">
        <?php include '../includes/sidebar_cust.php'; // sudah di-include di atas 
        ?>

        <main class="flex-1 p-6">
            <h1 class="text-2xl font-bold mb-6">📝 Edit Profil Anda</h1>

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
                <form action="edit_profile.php" method="POST">
                    <div class="mb-4">
                        <label for="nama" class="block text-gray-700 text-sm font-bold mb-2">Nama:</label>
                        <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($user_data['nama']) ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <div class="mb-6">
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user_data['email']) ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
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
    </div>

    <?php include '../includes/footer.php'; ?>
</body>

</html>