<?php if ($pesanan['status'] == 'Belum Bayar') { ?>
  <a href="upload_bukti.php?id=<?= $pesanan['id'] ?>">📤 Upload Bukti Pembayaran</a>
<?php } elseif ($pesanan['bukti_bayar']) { ?>
  <p>📎 Bukti sudah diupload. Menunggu konfirmasi admin.</p>
<?php } ?>
