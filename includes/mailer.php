<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

function kirim_email($tujuan, $subjek, $pesan) {
    $mail = new PHPMailer(true);

    try {
        // Pengaturan SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Ganti sesuai provider
        $mail->SMTPAuth   = true;
        $mail->Username   = 'EMAIL_ANDA@gmail.com';  // Email pengirim
        $mail->Password   = 'APP_PASSWORD';          // Password / App password Gmail
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Pengirim & Penerima
        $mail->setFrom('EMAIL_ANDA@gmail.com', 'Miniatur Store');
        $mail->addAddress($tujuan);

        // Konten Email
        $mail->isHTML(true);
        $mail->Subject = $subjek;
        $mail->Body    = $pesan;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
