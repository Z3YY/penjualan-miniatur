<?php
include '../config/db.php';
include '../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pesanan_id = isset($_POST['pesanan_id']) ? (int) $_POST['pesanan_id'] : 0;
    $user_id = $_SESSION['user_id'];

    // Cek apakah pesanan milik user
    $cek = mysqli_query($conn, "SELECT * FROM pesanan WHERE id = $pesanan_id AND user_id = $user_id");
    if (!$cek || mysqli_num_rows($cek) === 0) {
        echo "❌ Pesanan tidak valid.";
        exit;
    }

    // Handle file upload
    if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] === 0) {
        $file = $_FILES['bukti'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // Validasi ekstensi
        $allowed = ['jpg', 'jpeg', 'png'];
        if (!in_array($ext, $allowed)) {
            echo "❌ Format file tidak didukung. Hanya JPG, JPEG, dan PNG.";
            exit;
        }

        // Simpan file
        $nama_file = 'bukti_' . time() . '_' . rand(100, 999) . '.' . $ext;
        $target_dir = '../uploads/bukti/';
        if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
        $path = $target_dir . $nama_file;

        if (move_uploaded_file($file['tmp_name'], $path)) {
            // Update ke database
            $query = "UPDATE pesanan 
                      SET bukti_bayar = '$nama_file', status = 'Menunggu'
                      WHERE id = $pesanan_id";
            mysqli_query($conn, $query);

            header("Location: ../customer/invoice.php?id=$pesanan_id");
            exit;
        } else {
            echo "❌ Gagal menyimpan file.";
        }
    } else {
        echo "❌ Upload bukti gagal.";
    }
} else {
    echo "❌ Metode tidak diizinkan.";
}
