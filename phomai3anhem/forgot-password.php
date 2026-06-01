<?php
// ============================================================
// File: forgot-password.php
// Chức năng: Giả lập khôi phục và đặt lại mật khẩu bằng Email
// ============================================================
include_once 'config/db.php';

$error = '';
$success = '';
$user_found = false;
$email_saved = '';

// Bước 1: Kiểm tra Email xem có tồn tại hay không
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check_email'])) {
    $email = trim($_POST['email']);
    
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        $user_found = true;
        $email_saved = $user['email'];
    } else {
        $error = 'Địa chỉ Email này không tồn tại trên hệ thống của chúng tôi.';
    }
}

// Bước 2: Tiến hành cập nhật lại mật khẩu mới trực tiếp (Quy trình đơn giản hóa cho đồ án)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $email = trim($_POST['email_holder']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($password) || empty($confirm_password)) {
        $error = 'Vui lòng điền đầy đủ thông tin mật khẩu.';
        $user_found = true; 
        $email_saved = $email;
    } elseif ($password !== $confirm_password) {
        $error = 'Mật khẩu xác nhận không trùng khớp.';
        $user_found = true;
        $email_saved = $email;
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt_update = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        
        if ($stmt_update->execute([$hashed_password, $email])) {
            $success = 'Mật khẩu của bạn đã được đặt lại thành công! Hãy quay lại trang Đăng nhập.';
        } else {
            $error = 'Có lỗi xảy ra, vui lòng thử lại.';
        }
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

                <?php if(!$user_found && empty($success)): ?>
                    <form method="POST" action="forgot-password.php">
                        <p class="text-muted small text-center mb-4">Vui lòng nhập Email tài khoản của bạn để xác thực thông tin khôi phục.</p>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Địa chỉ Email đăng ký</label>
                            <input type="email" name="email" class="form-control rounded-pill px-3" required placeholder="name@example.com">
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="check_email" class="btn btn-gold py-2">Xác Thực Email</button>
                        </div>
                    </form>
                <?php endif; ?>

                <?php if($user_found && empty($success)): ?>
                    <form method="POST" action="forgot-password.php">
                        <input type="hidden" name="email_holder" value="<?php echo htmlspecialchars($email_saved); ?>">
                        <div class="alert alert-info small py-1 text-center">Tài khoản hợp lệ: <b><?php echo htmlspecialchars($email_saved); ?></b></div>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Mật khẩu mới</label>
                            <input type="password" name="password" class="form-control rounded-pill px-3" required placeholder="Tối thiểu 6 ký tự">
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Nhập lại mật khẩu mới</label>
                            <input type="password" name="confirm_password" class="form-control rounded-pill px-3" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="reset_password" class="btn btn-success rounded-pill py-2">Đặt Lại Mật Khẩu</button>
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