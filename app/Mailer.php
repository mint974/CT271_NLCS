<?php

namespace App;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    public static function send($toEmail, $toName, $subject, $body): bool
    {
        $mail = new PHPMailer(true);

        try {
            // Cấu hình SMTP
            $mail->isSMTP();
            $mail->Host       = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['MAIL_USERNAME'] ?? 'mtan090704@gmail.com'; 
            $mail->Password   = $_ENV['MAIL_PASSWORD'] ?? 'lvze tixy fihr rjpw';    
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $_ENV['MAIL_PORT'] ?? 587;

            // ✅ Cài đặt mã hóa UTF-8 để hiển thị đúng tiếng Việt
            $mail->CharSet = 'UTF-8';

            // Người gửi
            $mail->setFrom($_ENV['MAIL_FROM'] ?? $mail->Username, $_ENV['MAIL_FROM_NAME'] ?? 'Mint Fresh Fruit');

            // Người nhận
            $mail->addAddress($toEmail, $toName);

            // Nội dung
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log("Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }
}
