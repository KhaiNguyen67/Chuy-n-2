<?php
// ============================================================
// File: checkout.php
// Chức năng: Thanh toán và hiển thị thông tin tài khoản qua Modal sau khi bấm nút
// ============================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once 'config/db.php';
include_once 'includes/header.php';

// ============================================================
$bank_name    = "Ngân hàng Tung Tung"; 
$bank_account = "0987654321";    
$account_name = "NGUYEN DUY KHAI"; 
// ============================================================

if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    echo "<script>alert('Giỏ hàng của bạn đang trống!'); window.location.href='index.php';</script>";
    exit();
}

$user_info = ['full_name' => '', 'email' => '', 'phone' => '', 'address' => ''];
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT full_name, email, phone, address FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $db_user = $stmt->fetch();
    if ($db_user) $user_info = $db_user;
}

// Khai báo biến hỗ trợ bật Modal thông tin tài khoản
$show_bank_modal = false;
$generated_order_id = 0;
$total_checkout_money = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_checkout'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $note = trim($_POST['note']);
    $user_id = $_SESSION['user_id'] ?? null;

    $total_checkout_money = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_checkout_money += $item['price'] * $item['quantity'];
    }

    if (!empty($full_name) && !empty($phone) && !empty($address)) {
        try {
            $pdo->beginTransaction();

            $sql_order = "INSERT INTO orders (user_id, full_name, email, phone, address, note, total_money, status) 
                          VALUES (:user_id, :full_name, :email, :phone, :address, :note, :total_money, 'pending')";
            $stmt_order = $pdo->prepare($sql_order);
            $stmt_order->execute([
                'user_id'     => $user_id,
                'full_name'   => $full_name,
                'email'       => $email,
                'phone'       => $phone,
                'address'     => $address,
                'note'        => $note,
                'total_money' => $total_checkout_money
            ]);

            $generated_order_id = $pdo->lastInsertId();

            $sql_detail = "INSERT INTO order_details (order_id, product_id, price, quantity) 
                           VALUES (:order_id, :product_id, :price, :quantity)";
            $stmt_detail = $pdo->prepare($sql_detail);

            foreach ($_SESSION['cart'] as $product_id => $item) {
                $stmt_detail->execute([
                    'order_id'   => $generated_order_id,
                    'product_id' => $product_id,
                    'price'      => $item['price'],
                    'quantity'   => $item['quantity']
                ]);
            }

            $pdo->commit();
            unset($_SESSION['cart']); // Xóa sạch giỏ hàng

            // Bật cờ cho phép hiện Modal thông tin chuyển khoản lên màn hình
            $show_bank_modal = true;

        } catch (Exception $e) {
            $pdo->rollBack();
            $error_msg = "Có lỗi xảy ra: " . $e->getMessage();
        }
    } else {
        $error_msg = "Vui lòng điền đầy đủ các thông tin bắt buộc (*)";
    }
}

// Nội dung chuyển khoản mẫu đính kèm mã đơn hàng để bạn dễ quản lý dữ liệu
$qr_content = "DHPHOMAI" . $generated_order_id;
?>

<div class="container my-5 pt-4">
    <div class="mb-4 text-center">
        <h2 class="fw-bold text-dark">Tiến Hành Thanh Toán</h2>
        <p class="text-muted small">Vui lòng điền thông tin nhận hàng chính xác để thực hiện mua hàng.</p>
    </div>

    <?php if (isset($error_msg)): ?>
        <div class="alert alert-danger rounded-3 small"><?= $error_msg; ?></div>
    <?php endif; ?>

    <form action="checkout.php" method="POST">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom"><i class="bi bi-geo-alt-fill text-warning me-2"></i>Thông Tin Nhận Hàng</h5>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Họ và tên người nhận <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-3 py-2" name="full_name" value="<?= htmlspecialchars($user_info['full_name']); ?>" required placeholder="Ví dụ: Nguyễn Duy Khải">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 col-12 mb-3 mb-md-0">
                            <label class="form-label small fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control rounded-3 py-2" name="phone" value="<?= htmlspecialchars($user_info['phone']); ?>" required placeholder="Nhập số điện thoại gọi giao hàng">
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="form-label small fw-bold">Địa chỉ Email</label>
                            <input type="email" class="form-control rounded-3 py-2" name="email" value="<?= htmlspecialchars($user_info['email']); ?>" placeholder="Địa chỉ email nhận hóa đơn">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Địa chỉ giao hàng chính xác <span class="text-danger">*</span></label>
                        <textarea class="form-control rounded-3" name="address" rows="3" required placeholder="Số nhà, tên đường, quận/huyện..."><?= htmlspecialchars($user_info['address']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Ghi chú đơn hàng (Tùy chọn)</label>
                        <textarea class="form-control rounded-3" name="note" rows="2" placeholder="Ví dụ: Gọi trước khi đến 15 phút..."></textarea>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white position-sticky" style="top: 90px;">
                    <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom"><i class="bi bi-bag-check-fill text-warning me-2"></i>Đơn Hàng</h5>
                    
                    <div class="cart-items-preview mb-3 overflow-auto" style="max-height: 200px;">
                        <?php 
                        $cart_total = 0;
                        if (isset($_SESSION['cart'])):
                            foreach ($_SESSION['cart'] as $id => $item): 
                                $subtotal = $item['price'] * $item['quantity'];
                                $cart_total += $subtotal;
                        ?>
                            <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom border-light">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="position-relative">
                                        <img src="assets/img/<?= !empty($item['image']) ? $item['image'] : 'default-cheese.jpg'; ?>" class="rounded-3 border object-fit-cover" style="width: 50px; height: 50px;" alt="">
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary text-white" style="font-size: 10px;">
                                            <?= $item['quantity']; ?>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-dark small fw-bold text-truncate" style="max-width: 180px;"><?= htmlspecialchars($item['name']); ?></h6>
                                        <small class="text-muted"><?= number_format($item['price'], 0, ',', '.'); ?>đ</small>
                                    </div>
                                </div>
                                <span class="text-dark small fw-bold"><?= number_format($subtotal, 0, ',', '.'); ?>đ</span>
                            </div>
                        <?php 
                            endforeach; 
                        endif;
                        ?>
                    </div>

                    <div class="price-summary bg-light p-3 rounded-4 mb-4">
                        <div class="d-flex justify-content-between mb-2 small text-secondary">
                            <span>Tạm tính:</span>
                            <span><?= number_format($cart_total, 0, ',', '.'); ?>đ</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark">Tổng thanh toán:</span>
                            <span class="fs-5 fw-bold text-danger"><?= number_format($cart_total, 0, ',', '.'); ?>đ</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold mb-2">Phương thức thanh toán trực tuyến</label>
                        <div class="border rounded-3 p-3 d-flex align-items-center bg-white border-warning">
                            <input class="form-check-input text-warning me-3" type="radio" checked>
                            <div>
                                <strong class="text-dark small d-block"><i class="bi bi-bank text-warning me-1"></i> Chuyển khoản ngân hàng trực tiếp</strong>
                                <small class="text-muted" style="font-size: 11px;">Thông tin tài khoản sẽ hiển thị sau khi bấm đặt hàng</small>
                            </div>
                        </div>
                    </div>

                    <button type="submit" name="btn_checkout" class="btn btn-gold w-100 py-2.5 text-white rounded-pill shadow-sm text-uppercase fw-bold">
                        <i class="bi bi-wallet2 me-2"></i>Thanh Toán Ngay
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="bankPaymentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content border-0 rounded-4 shadow-lg bg-white">
            <div class="modal-header bg-light border-0 justify-content-center py-3">
                <h5 class="modal-title fw-bold text-dark m-0"><i class="bi bi-check2-circle text-success me-2"></i>Đặt Hàng Thành Công!</h5>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted text-center small mb-4">Vui lòng chuyển khoản số tiền đơn hàng vào tài khoản ngân hàng dưới đây để hệ thống duyệt giao hàng sớm nhất.</p>
                
                <div class="border rounded-4 p-3 bg-light border-warning-subtle mb-4 shadow-sm">
                    <div class="mb-2.5 pb-2 border-bottom border-white d-flex justify-content-between align-items-center">
                        <span class="small text-secondary">Ngân hàng:</span> 
                        <strong class="text-dark text-end fs-6"><?= $bank_name; ?></strong>
                    </div>
                    <div class="mb-2.5 pb-2 border-bottom border-white d-flex justify-content-between align-items-center">
                        <span class="small text-secondary">Số tài khoản:</span> 
                        <strong class="text-primary font-monospace fs-5"><?= $bank_account; ?></strong>
                    </div>
                    <div class="mb-2.5 pb-2 border-bottom border-white d-flex justify-content-between align-items-center">
                        <span class="small text-secondary">Chủ tài khoản:</span> 
                        <strong class="text-dark text-uppercase"><?= $account_name; ?></strong>
                    </div>
                    <div class="mb-2.5 pb-2 border-bottom border-white d-flex justify-content-between align-items-center">
                        <span class="small text-secondary">Số tiền cần chuyển:</span> 
                        <strong class="text-danger fw-bold fs-5"><?= number_format($total_checkout_money, 0, ',', '.'); ?>đ</strong>
                    </div>
                    <div class="mb-0 d-flex justify-content-between align-items-center">
                        <span class="small text-secondary">Nội dung chuyển khoản:</span> 
                        <strong class="text-dark bg-warning-subtle px-2 py-0.5 rounded text-uppercase font-monospace small border border-warning"><?= $qr_content; ?></strong>
                    </div>
                </div>

                <div class="alert alert-warning rounded-3 small p-2 text-center mb-4" style="font-size: 11px;">
                    <i class="bi bi-info-circle me-1"></i> Bạn hãy chụp lại màn hình này hoặc ghi nhớ thông tin tài khoản trước khi bấm tiếp tục.
                </div>

                <button type="button" class="btn btn-gold w-100 text-white rounded-pill py-2.5 fw-bold text-uppercase small shadow-sm" onclick="redirectToThankYou()">
                    Tôi Đã Xác Nhận Chuyển Khoản <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Chờ PHP thực thi chèn CSDL hoàn tất, nếu đúng sẽ kích hoạt mở Modal
document.addEventListener("DOMContentLoaded", function() {
    <?php if ($show_bank_modal): ?>
        if (typeof bootstrap !== 'undefined') {
            let bankModal = new bootstrap.Modal(document.getElementById('bankPaymentModal'));
            bankModal.show();
        }
    <?php endif; ?>
});

function redirectToThankYou() {
    window.location.href = 'thankyou.php?order_id=<?= $generated_order_id; ?>';
}
</script>

<style>
.btn-gold {
    background-color: #E5A93B !important;
    border: none !important;
    font-weight: 600;
    transition: all 0.3s ease;
}
.btn-gold:hover {
    background-color: #C98F2A !important;
    transform: translateY(-1px);
}
.form-control:focus {
    border-color: #E5A93B;
    box-shadow: 0 0 0 0.25rem rgba(229, 169, 59, 0.25);
}
</style>

<?php include_once 'includes/footer.php'; ?>