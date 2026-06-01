<?php
// ============================================================
// File: reset-password.php
// Chức năng: Kiểm tra Token hợp lệ từ URL và cho phép cập nhật mật khẩu mới
// ============================================================
include_once 'config/db.php';

$error = '';
$success = '';
$token_valid = false;
$token = isset($_GET['token']) ? trim($_GET['token']) : '';

if (!empty($token)) {
    // Tìm kiếm user sở hữu mã token này và kiểm tra thời hạn (thời gian hiện tại phải nhỏ hơn thời gian hết hạn)
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE reset_token = ? AND token_expire > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if ($user) {
        $token_valid = true;
    } else {
        $error = 'Đường dẫn xác thực không hợp lệ hoặc đã hết hạn sử dụng. Vui lòng yêu cầu lại mã mới.';
    }
} else {
    $error = 'Yêu cầu không hợp lệ. Không tìm thấy mã xác thực xác minh.';
}

// Xử lý khi người dùng ấn nút Đặt lại mật khẩu mới
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $user_token = $_POST['token_holder'];

    // Tìm lại thông tin user dựa trên token ẩn gửi lên từ form
    $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND token_expire > NOW()");
    $stmt->execute([$user_token]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = 'Đã có lỗi xảy ra hoặc phiên làm việc của bạn đã hết hạn.';
    } elseif (empty($password) || strlen($password) < 6) {
        $error = 'Mật khẩu mới phải có độ dài tối thiểu từ 6 ký tự trở lên.';
        $token_valid = true; // Giữ lại giao diện form
    } elseif ($password !== $confirm_password) {
        $error = 'Mật khẩu nhập lại xác nhận không trùng khớp.';
        $token_valid = true; 
    } else {
        // Thực hiện băm bảo mật mật khẩu mới
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Cập nhật mật khẩu mới, đồng thời HỦY bỏ token cũ (set thành NULL) để không thể sử dụng lại đường link này nữa
        $stmt_update = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, token_expire = NULL WHERE id = ?");
        
        if ($stmt_update->execute([$hashed_password, $user['id']])) {
            $success = 'Mật khẩu của bạn đã được cập nhật thành công! Bạn có thể quay lại trang đăng nhập ngay bây giờ.';
            $token_valid = false; // Ẩn form nhập sau khi thành công
        } else {
            $error = 'Lỗi hệ thống cơ sở dữ liệu, vui lòng thử lại.';
            $token_valid = true;
        }
    }
}

include_once 'includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5" data-aos="zoom-in">
            <div class="glass-card p-5">
                <h3 class="fw-bold text-center mb-4">Thiết Lập Mật Khẩu Mới</h3>

                <?php if(!empty($error)): ?>
                    <div class="alert alert-danger small py-2"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if(!empty($success)): ?>
                    <div class="alert alert-success small py-2"><?php echo $success; ?></div>
                <?php endif; ?>

                <?php if($token_valid && empty($success)): ?>
                    <form method="POST" action="reset-password.php?token=<?php echo htmlspecialchars($token); ?>">
                        <input type="hidden" name="token_holder" value="<?php echo htmlspecialchars($token); ?>">
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Mật khẩu mới</label>
                            <input type="password" name="password" class="form-control rounded-pill px-3" required placeholder="Tối thiểu 6 ký tự">
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Nhập lại mật khẩu mới</label>
                            <input type="password" name="confirm_password" class="form-control rounded-pill px-3" required placeholder="Xác nhận mật khẩu giống trên">
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="update_password" class="btn btn-success rounded-pill py-2">Xác Nhận Thay Đổi</button>
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