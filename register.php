<?php
// ============================================================
// File: register.php
// Chức năng: Đăng ký tài khoản khách hàng mới (Có Xác nhận mật khẩu)
// ============================================================
include_once 'config/db.php';
include_once 'includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password']; // 1. Lấy thêm giá trị từ ô Xác nhận mật khẩu
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    // Kiểm tra xem có trường bắt buộc nào bỏ trống không
    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Vui lòng điền đầy đủ các trường bắt buộc (*).';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Định dạng Email không hợp lệ.';
    } elseif (strlen($password) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự.';
    } elseif ($password !== $confirm_password) {
        // 2. Kiểm tra mật khẩu và mật khẩu nhập lại có khớp nhau không
        $error = 'Mật khẩu xác nhận không trùng khớp. Vui lòng nhập lại.';
    } else {
        // Kiểm tra xem email đã có người sử dụng chưa
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email này đã được đăng ký sử dụng trên hệ thống.';
        } else {
            // Mã hóa mật khẩu bảo mật cao (Bcrypt)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // 3. Câu lệnh SQL chuẩn, khớp chính xác 100% với cấu trúc file phomai3anhem.sql của bạn
            $sql = "INSERT INTO users (full_name, email, password, phone, address, role) 
                    VALUES (?, ?, ?, ?, ?, 'customer')";
            $stmt_insert = $pdo->prepare($sql);
            
            if ($stmt_insert->execute([$full_name, $email, $hashed_password, $phone, $address])) {
                $success = 'Đăng ký tài khoản thành công! Bạn có thể tiến hành đăng nhập ngay bây giờ.';
                // Xóa sạch dữ liệu cũ trong các ô input sau khi đăng ký thành công
                $full_name = $email = $phone = $address = '';
            } else {
                $error = 'Có lỗi xảy ra trong quá trình đăng ký, vui lòng thử lại.';
            }
        }
    }
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6" data-aos="zoom-in">
            <div class="glass-card p-5">
                <h2 class="fw-bold text-center mb-4">Đăng Ký Thành Viên</h2>
                
                <?php if(!empty($error)): ?>
                    <div class="alert alert-danger small py-2 fw-semibold text-center">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <?php if(!empty($success)): ?>
                    <div class="alert alert-success small py-2 fw-semibold text-center">
                        <i class="bi bi-check-circle-fill me-1"></i> <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="register.php">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Họ và Tên *</label>
                        <input type="text" name="full_name" class="form-control rounded-pill px-3" required placeholder="Nguyễn Văn A" value="<?php echo isset($full_name) ? htmlspecialchars($full_name) : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Địa chỉ Email *</label>
                        <input type="email" name="email" class="form-control rounded-pill px-3" required placeholder="name@example.com" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Mật khẩu *</label>
                        <input type="password" name="password" class="form-control rounded-pill px-3" required minlength="6" placeholder="Tối thiểu 6 ký tự">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Xác nhận mật khẩu *</label>
                        <input type="password" name="confirm_password" class="form-control rounded-pill px-3" required minlength="6" placeholder="Nhập lại mật khẩu chính xác">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control rounded-pill px-3" placeholder="0901234567" value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>">
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Địa chỉ giao hàng</label>
                        <textarea name="address" class="form-control rounded-3" rows="2" placeholder="Số nhà, tên đường, quận/huyện..."><?php echo isset($address) ? htmlspecialchars($address) : ''; ?></textarea>
                    </div>
                    
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-gold py-2">Tạo Tài Khoản</button>
                    </div>
                    <div class="text-center small text-muted">
                        Đã có tài khoản? <a href="login.php" class="text-warning fw-bold text-decoration-none">Đăng nhập tại đây</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>