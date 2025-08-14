<?php
session_start();
// Pastikan path ke file include benar relatif dari lokasi file ini
// Asumsi file ini ada di 'penjualan-miniatur/process/verifikasi_pembayaran_process.php'
include '../includes/admin_auth.php'; // Middleware autentikasi dan otorisasi admin
include '../config/db.php';         // File koneksi database

// Pastikan admin sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header('Location: ../pages/login.php'); // Redirect jika bukan admin atau belum login
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $pesanan_id = $_POST['id'] ?? null;
  $action = $_POST['action'] ?? null; // 'terima', 'tolak', 'kirim', 'berhasil', 'batalkan_admin'
  $resi = $_POST['resi'] ?? null; // Untuk aksi 'kirim'

  // --- Validasi dan Sanitasi ID Pesanan ---
  if (!$pesanan_id || !is_numeric($pesanan_id) || !$action) {
    header("Location: ../admin/data_pesanan.php?status_update=gagal&msg=ID Pesanan atau Aksi tidak valid.");
    exit;
  }

  // --- Ambil Detail Pesanan (Menggunakan Prepared Statement untuk keamanan) ---
  $stmt_pesanan = mysqli_prepare($conn, "SELECT * FROM pesanan WHERE id = ?");
  if ($stmt_pesanan === false) {
    error_log("Failed to prepare statement for fetching order details: " . mysqli_error($conn));
    header("Location: ../admin/data_pesanan.php?status_update=gagal&msg=Kesalahan sistem saat mengambil pesanan.");
    exit;
  }
  mysqli_stmt_bind_param($stmt_pesanan, "i", $pesanan_id);
  mysqli_stmt_execute($stmt_pesanan);
  $result_pesanan = mysqli_stmt_get_result($stmt_pesanan);
  $pesanan = mysqli_fetch_assoc($result_pesanan);
  mysqli_stmt_close($stmt_pesanan);

  if (!$pesanan) {
    header("Location: ../admin/data_pesanan.php?status_update=gagal&msg=Pesanan tidak ditemukan.");
    exit;
  }

  $current_status = $pesanan['status'];
  $new_status = $current_status; // Default, tidak berubah
  $redirect_msg = 'status_update=gagal'; // Default message

  // --- Logika Perubahan Status Berdasarkan Aksi ---
  switch ($action) {
    case 'terima':
      if ($current_status === 'Menunggu') {
        // --- PENGURANGAN STOK DENGAN TRANSAKSI (PENTING!) ---
        mysqli_begin_transaction($conn);
        $stok_berhasil_dikurangi = true;

        // Ambil detail produk dalam pesanan ini
        $stmt_detail = mysqli_prepare($conn, "SELECT produk_id, jumlah FROM pesanan_detail WHERE pesanan_id = ?");
        mysqli_stmt_bind_param($stmt_detail, "i", $pesanan_id);
        mysqli_stmt_execute($stmt_detail);
        $result_detail = mysqli_stmt_get_result($stmt_detail);

        while ($item = mysqli_fetch_assoc($result_detail)) {
          $produk_id = $item['produk_id'];
          $jumlah_dibeli = $item['jumlah'];

          // Kurangi stok produk, pastikan stok tidak kurang dari jumlah yang dibeli
          $stmt_update_stok = mysqli_prepare($conn, "UPDATE produk SET stok = stok - ? WHERE id = ? AND stok >= ?");
          if ($stmt_update_stok === false) {
            error_log("Failed to prepare stock update statement: " . mysqli_error($conn));
            $stok_berhasil_dikurangi = false;
            break;
          }
          mysqli_stmt_bind_param($stmt_update_stok, "iii", $jumlah_dibeli, $produk_id, $jumlah_dibeli);
          mysqli_stmt_execute($stmt_update_stok);

          if (mysqli_stmt_affected_rows($stmt_update_stok) === 0) {
            // Jika tidak ada baris yang terpengaruh, berarti stok tidak cukup
            $stok_berhasil_dikurangi = false;
            break;
          }
          mysqli_stmt_close($stmt_update_stok); // Tutup statement setelah setiap update
        }
        mysqli_stmt_close($stmt_detail);

        if ($stok_berhasil_dikurangi) {
          $new_status = 'Diproses';
          mysqli_commit($conn); // Commit transaksi
          $redirect_msg = 'verifikasi=sukses';
        } else {
          mysqli_rollback($conn); // Rollback transaksi jika stok tidak cukup
          $redirect_msg = 'status_update=gagal&msg=Stok_tidak_cukup_untuk_beberapa_produk!';
          header("Location: ../admin/data_pesanan.php?" . $redirect_msg); // Redirect langsung di sini
          exit;
        }
      } else {
        $redirect_msg = 'status_update=gagal&msg=Status_pesanan_tidak_memenuhi_syarat_untuk_diterima.';
      }
      break;

    case 'tolak':
      if ($current_status === 'Menunggu') {
        $new_status = 'Dibatalkan'; // Statusnya langsung Dibatalkan

        // Hapus bukti bayar fisik jika ada dan valid
        if (!empty($pesanan['bukti_bayar']) && file_exists('../../uploads/bukti/' . $pesanan['bukti_bayar'])) {
          unlink('../../uploads/bukti/' . $pesanan['bukti_bayar']);
        }
        // Update bukti_bayar di DB jadi NULL
        $stmt_clear_bukti = mysqli_prepare($conn, "UPDATE pesanan SET bukti_bayar = NULL WHERE id = ?");
        if ($stmt_clear_bukti === false) {
          error_log("Failed to prepare statement for clearing bukti_bayar: " . mysqli_error($conn));
          $redirect_msg = 'status_update=gagal&msg=Kesalahan_sistem_saat_menolak_pembayaran.';
          break;
        }
        mysqli_stmt_bind_param($stmt_clear_bukti, "i", $pesanan_id);
        mysqli_stmt_execute($stmt_clear_bukti);
        mysqli_stmt_close($stmt_clear_bukti);

        $redirect_msg = 'verifikasi=ditolak';
      } else {
        $redirect_msg = 'status_update=gagal&msg=Status_pesanan_tidak_memenuhi_syarat_untuk_ditolak.';
      }
      break;

    case 'kirim':
      if ($current_status === 'Diproses' && !empty($resi)) {
        $new_status = 'Dikirim';
        $redirect_msg = 'status_update=sukses';
      } else {
        $redirect_msg = 'status_update=gagal&msg=Status_pesanan_tidak_memenuhi_syarat_atau_resi_kosong.';
      }
      break;

    case 'berhasil':
      if ($current_status === 'Dikirim') {
        $new_status = 'Berhasil';
        $redirect_msg = 'status_update=sukses';
      } else {
        $redirect_msg = 'status_update=gagal&msg=Status_pesanan_tidak_memenuhi_syarat_untuk_diselesaikan.';
      }
      break;

    case 'batalkan_admin':
      if ($current_status !== 'Berhasil' && $current_status !== 'Dibatalkan') {
        $new_status = 'Dibatalkan';
        // TODO: Jika pesanan sudah 'Diproses' dan dibatalkan oleh admin,
        //      Anda mungkin perlu mengembalikan stok produk yang sudah dikurangi.
        //      Ini membutuhkan logika transaksi tambahan dan kompleks.
        $redirect_msg = 'status_update=sukses';
      } else {
        $redirect_msg = 'status_update=gagal&msg=Pesanan_tidak_dapat_dibatalkan_pada_status_ini.';
      }
      break;

    default:
      $redirect_msg = 'status_update=gagal&msg=Aksi_tidak_dikenali.';
      break;
  }

  // --- Update Status Pesanan di Database (Hanya jika status berubah) ---
  if ($new_status !== $current_status) {
    $query_update_pesanan = "UPDATE pesanan SET status = ?";
    $params = [$new_status];
    $types = "s";

    if ($action === 'kirim' && $new_status === 'Dikirim') {
      $query_update_pesanan .= ", resi = ?";
      $params[] = $resi;
      $types .= "s";
    }

    $query_update_pesanan .= " WHERE id = ?";
    $params[] = $pesanan_id;
    $types .= "i";

    $stmt_update_status = mysqli_prepare($conn, $query_update_pesanan);
    if ($stmt_update_status === false) {
      error_log("Failed to prepare status update statement: " . mysqli_error($conn));
      header("Location: ../admin/data_pesanan.php?status_update=gagal&msg=Kesalahan_sistem_saat_memperbarui_status.");
      exit;
    }
    mysqli_stmt_bind_param($stmt_update_status, $types, ...$params); // Menggunakan spread operator untuk params

    if (!mysqli_stmt_execute($stmt_update_status)) {
      error_log("Failed to execute status update: " . mysqli_error($conn));
      header("Location: ../admin/data_pesanan.php?status_update=gagal&msg=Gagal_memperbarui_status_pesanan.");
      exit;
    }
    mysqli_stmt_close($stmt_update_status);
  }

  header("Location: ../admin/data_pesanan.php?" . $redirect_msg);
  exit;
} else {
  // Jika bukan POST request, redirect
  header("Location: ../admin/data_pesanan.php");
  exit;
}
