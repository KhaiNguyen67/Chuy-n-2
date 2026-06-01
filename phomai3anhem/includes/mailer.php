<?php
// ============================================================
// File: includes/mailer.php
// Chức năng: Cấu hình lõi gửi Email bằng PHPMailer (SMTP)
// ============================================================

// Nhúng các file thư viện cốt lõi của PHPMailer vào hệ thống
// (Nếu dùng Composer thì chỉ cần: require '../vendor/autoload.php';)
require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Hàm gửi email dùng chung cho toàn hệ thống
 * @param string $toEmail Email người nhận
 * @param string $subject Tiêu đề email
 * @param string $body Nội dung email (chấp nhận thẻ HTML)
 * @return bool Trả về true nếu gửi thành công, false nếu thất bại
 */
function sendSystemMail($toEmail, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        // Cấu hình kết nối Máy chủ SMTP của Gmail
        $mail->isSMTP();                                            // Sử dụng SMTP
        $mail->Host       = 'smtp.gmail.com';                     // Máy chủ SMTP của Google
        $mail->SMTPAuth   = true;                                   // Bật tính năng xác thực SMTP
        $mail->Username   = 'email-cua-ban@gmail.com';             // Tài khoản Gmail của bạn
        $mail->Password   = 'xxxx xxxx xxxx xxxx';                 // Mật khẩu ứng dụng (16 ký tự vừa tạo)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Mã hóa TLS bảo mật
        $mail->Port       = 587;                                    // Cổng kết nối SMTP TLS

        // Thiết lập cấu hình hiển thị bảng mã ngôn ngữ tiếng Việt
        $mail->CharSet = 'UTF-8';

        // Thông tin người gửi (Hiển thị ở hộp thư người nhận)
        $mail->setFrom('email-cua-ban@gmail.com', 'Tiệm Phô Mai Cao Cấp');
        $mail->addAddress($toEmail);                                // Thêm email người nhận

        // Thiết lập định dạng nội dung dạng HTML
        $mail->isHTML(true);                                  
        $mail->Subject = $subject;
        $mail->Body    = $body;

        // Tiến hành thực thi lệnh gửi thư đi
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Bạn có thể ghi log lỗi ra file nếu cần thiết: error_log($mail->ErrorInfo);
        return false;
    }
}