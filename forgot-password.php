<?php
// ============================================================
// File: forgot-password.php
// Chức năng: Nhận Email yêu cầu, sinh Token bảo mật và gửi Email
// ============================================================
include_once 'config/db.php';
include_once 'includes/mailer.php'; // Nhúng file chứa hàm sendSystemMail() đã cài ở bước trước

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check_email'])) {
    $email = trim($_POST['email']);
    
    // Kiểm tra email xem có thực sự tồn tại trong hệ thống không
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        // 1. Tạo chuỗi mã Token ngẫu nhiên (bảo mật cao)
        $token = bin2hex(random_bytes(32));
        
        // 2. Thiết lập thời gian hết hạn cho Token (Ví dụ: 15 phút kể từ thời điểm hiện tại)
        $expire_time = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        
        // 3. Lưu Token và thời gian hết hạn vào Database của user này
        $stmt_update = $pdo->prepare("UPDATE users SET reset_token = ?, token_expire = ? WHERE id = ?");
        $stmt_update->execute([$token, $expire_time, $user['id']]);
        
        // 4. Tạo đường dẫn khôi phục mật khẩu gửi cho khách hàng
        // Bạn thay đổi 'localhost/your-project/' bằng đường dẫn thư mục dự án thực tế của bạn
        $reset_link = "http://localhost/your-project/reset-password.php?token=" . $token;
        
        // 5. Thiết kế nội dung giao diện HTML gửi Mail (Phong cách Clean Modern)
        $subject = "🔒 Yêu cầu đặt lại mật khẩu tài khoản Phô Mai";
        $body = "
        <div style='background-color: #f8f9fa; padding: 40px; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, sans-serif; color: #333;'>
            <div style='max-width: 520px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 16px; box-shadow: 0 4px 25px rgba(0,0,0,0.05);'>
                <div style='text-align: center; margin-bottom: 25px;'>
                    <h2 style='color: #d4af37; font-weight: bold; margin: 0;'>TIỆM PHÔ MAI CAO CẤP</h2>
                    <p style='color: #777; font-size: 14px; margin-top: 5px;'>Khám phá thế giới hương vị châu Âu</p>
                </div>
                <hr style='border: 0; border-top: 1px solid #eee; margin-bottom: 30px;'>
                <p>Xin chào,</p>
                <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản liên kết với địa chỉ email này. Vui lòng nhấn vào nút xác nhận dưới đây để tiến hành thiết lập mật khẩu mới:</p>
                
                <div style='text-align: center; margin: 35px 0;'>
                    <a href='{$reset_link}' style='background-color: #d4af37; color: #ffffff; padding: 14px 35px; text-decoration: none; font-weight: bold; border-radius: 30px; display: inline-block; box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3); letter-spacing: 0.5px;'>Đặt Lại Mật Khẩu</a>
                </div>
                
                <p style='font-size: 13px; color: #ca0000; font-weight: 500;'>* Lưu ý: Đường dẫn này chỉ có hiệu lực sử dụng trong vòng 15 phút.</p>
                <p style='font-size: 13px; color: #777;'>Nếu bạn không thực hiện yêu cầu này, bạn có thể an tâm bỏ qua email này, mật khẩu cũ của bạn vẫn sẽ được giữ an toàn tuyệt đối.</p>
                
                <hr style='border: 0; border-top: 1px solid #eee; margin-top: 30px; margin-bottom: 20px;'>
                <p style='font-size: 11px; color: #aaa; text-align: center; margin: 0;'>Đây là email tự động từ hệ thống, vui lòng không phản hồi lại thư này.</p>
            </div>
        </div>
        ";
        
        // 6. Thực thi hành động gửi Mail qua SMTP PHPMailer
        if (sendSystemMail($email, $subject, $body)) {
            $success = 'Hệ thống đã gửi một liên kết xác nhận đến Email của bạn. Vui lòng kiểm tra hộp thư (hoặc hộp thư rác/spam).';
        } else {
            $error = 'Có lỗi xảy ra trong quá trình gửi Mail. Vui lòng thử lại sau.';
        }
        
    } else {
        $error = 'Địa chỉ Email này không tồn tại trên hệ thống của chúng tôi.';
    }
}

include_once 'includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5" data-aos="zoom-in">
            <div class="glass-card p-5">
                <h3 class="fw-bold text-center mb-4">Khôi Phục Mật Khẩu</h3>

                <?php if(!empty($error)): ?>
                    <div class="alert alert-danger small py-2"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if(!empty($success)): ?>
                    <div class="alert alert-success small py-2"><?php echo $success; ?></div>
                <?php endif; ?>

                <?php if(empty($success)): ?>
                    <form method="POST" action="forgot-password.php">
                        <p class="text-muted small text-center mb-4">Vui lòng nhập Email tài khoản của bạn để nhận liên kết xác thực khôi phục mật khẩu an toàn.</p>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Địa chỉ Email đăng ký</label>
                            <input type="email" name="email" class="form-control rounded-pill px-3" required placeholder="name@example.com">
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="check_email" class="btn btn-gold py-2"><i class="bi bi-envelope-paper me-2"></i>Gửi Liên Kết Xác Thực</button>
                        </div>
                    </form>
                <?php endif; ?>

                <div class="text-center small text-muted mt-4">
                    <a href="login.php" class="text-decoration-none text-warning fw-bold"><i class="bi bi-arrow-left"></i> Quay lại Đăng Nhập</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>