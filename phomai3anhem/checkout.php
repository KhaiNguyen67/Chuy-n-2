<?php
// ============================================================
// File: checkout.php
// Chức năng: Xem chi tiết đơn hàng, tăng giảm số lượng bằng AJAX & Đặt hàng
// ============================================================
include_once 'config/db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ── 1. CHẶN NẾU GIỎ HÀNG TRỐNG ──────────────────────────────
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    header("Location: product.php");
    exit();
}

// ── Lấy thông tin user nếu đã đăng nhập ──────────────────────
$user_name = $_SESSION['user_name'] ?? '';
$user_id   = $_SESSION['user_id']   ?? null;
$phone     = '';
$address   = '';

if ($user_id) {
    $stmt = $pdo->prepare("SELECT phone, address FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_info = $stmt->fetch();
    if ($user_info) {
        $phone   = $user_info['phone'];
        $address = $user_info['address'];
    }
}

// ── 2. XỬ LÝ LƯU ĐƠN HÀNG KHI BẤM NÚT ĐẶT HÀNG ─────────────
$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_order'])) {
    $receiver_name    = trim($_POST['receiver_name']);
    $receiver_phone   = trim($_POST['receiver_phone']);
    $shipping_address = trim($_POST['shipping_address']);
    $note             = trim($_POST['note']);

    if (empty($receiver_name) || empty($receiver_phone) || empty($shipping_address)) {
        $error = 'Vui lòng điền đầy đủ thông tin giao hàng bắt buộc (*).';
    } else {
        try {
            $pdo->beginTransaction();

            // Tính tổng tiền từ giá DB (bảo vệ khỏi giả mạo giá phía client)
            $total_amount = 0;
            $cart_items   = [];

            foreach ($_SESSION['cart'] as $p_id => $cart_value) {
                $st = $pdo->prepare("SELECT price FROM products WHERE id = ?");
                $st->execute([$p_id]);
                $p_data = $st->fetch();

                if ($p_data) {
                    $qty = is_array($cart_value)
                        ? (int)($cart_value['quantity'] ?? $cart_value['qty'] ?? 1)
                        : (int)$cart_value;

                    $item_price    = (int)$p_data['price'];
                    $total_amount += $item_price * $qty;

                    $cart_items[$p_id] = ['qty' => $qty, 'price' => $item_price];
                }
            }

            // Chèn đơn hàng
            $stmt_order = $pdo->prepare("
                INSERT INTO orders
                    (user_id, total_amount, receiver_name, receiver_phone, shipping_address, order_note, status)
                VALUES (?, ?, ?, ?, ?, ?, 'pending')
            ");
            $stmt_order->execute([
                $user_id, $total_amount, $receiver_name,
                $receiver_phone, $shipping_address, $note,
            ]);
            $order_id = $pdo->lastInsertId();

            // Chèn chi tiết đơn hàng & trừ tồn kho
            $stmt_detail       = $pdo->prepare("INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt_update_stock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

            foreach ($cart_items as $prod_id => $item) {
                $stmt_detail->execute([$order_id, $prod_id, $item['qty'], $item['price']]);
                $stmt_update_stock->execute([$item['qty'], $prod_id]);
            }

            $pdo->commit();
            unset($_SESSION['cart']);
            $success = 'Đặt hàng thành công! Đơn hàng của bạn đang được xử lý.';

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Lỗi hệ thống: ' . $e->getMessage();
        }
    }
}

// ── 3. ĐỌC CHI TIẾT SẢN PHẨM TRONG GIỎ ĐỂ HIỂN THỊ ─────────
$products_in_cart = [];
$grand_total      = 0;

if (!empty($_SESSION['cart'])) {
    $ids       = array_keys($_SESSION['cart']);
    $in_clause = implode(',', array_fill(0, count($ids), '?'));

    $stmt_cart = $pdo->prepare("SELECT * FROM products WHERE id IN ($in_clause)");
    $stmt_cart->execute($ids);

    foreach ($stmt_cart->fetchAll() as $prod) {
        $cart_value = $_SESSION['cart'][$prod['id']];
        $qty = is_array($cart_value)
            ? (int)($cart_value['quantity'] ?? $cart_value['qty'] ?? 1)
            : (int)$cart_value;

        $subtotal     = (int)$prod['price'] * $qty;
        $grand_total += $subtotal;

        $products_in_cart[] = [
            'id'       => $prod['id'],
            'name'     => $prod['name'],
            'price'    => (int)$prod['price'],
            'qty'      => $qty,
            'subtotal' => $subtotal,
        ];
    }
}

include_once 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/checkout.css">

<div class="container my-5">

    <!-- Tiêu đề trang -->
    <div class="text-center mb-5" data-aos="fade-up">
        <h1 class="fw-bold">Thủ Tục Thanh Toán</h1>
        <p class="text-muted">Vui lòng kiểm tra lại danh sách phô mai và điền thông tin nhận hàng</p>
    </div>

    <?php if (!empty($success)): ?>
        <!-- ===== TRẠNG THÁI ĐẶT HÀNG THÀNH CÔNG ===== -->
        <div class="row justify-content-center" data-aos="zoom-in">
            <div class="col-md-7 text-center py-5 glass-card">
                <i class="bi bi-check-circle-fill text-success display-3"></i>
                <h3 class="fw-bold mt-4 text-dark"><?php echo $success; ?></h3>
                <p class="text-muted mt-2">Cảm ơn bạn đã lựa chọn Phô Mai 3 Anh Em làm người đồng hành ẩm thực.</p>
                <a href="product.php" class="btn btn-gold mt-4 px-4 py-2">Tiếp tục mua sắm</a>
            </div>
        </div>

    <?php else: ?>
        <div class="row g-4">

            <!-- ===== CỘT TRÁI: FORM THÔNG TIN GIAO HÀNG ===== -->
            <div class="col-lg-6" data-aos="fade-right">
                <div class="checkout-form-card p-4 p-md-5 h-100">
                    <h4 class="fw-bold mb-4 text-dark">
                        <i class="bi bi-geo-alt text-warning me-2"></i>Thông Tin Giao Hàng
                    </h4>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger small py-2"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="checkout.php" id="checkout-form">

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Họ và tên người nhận *</label>
                            <input type="text"
                                   name="receiver_name"
                                   class="form-control rounded-pill px-3"
                                   value="<?php echo htmlspecialchars($user_name); ?>"
                                   required
                                   placeholder="Nhập tên người nhận hàng">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Số điện thoại liên hệ *</label>
                            <input type="text"
                                   name="receiver_phone"
                                   class="form-control rounded-pill px-3"
                                   value="<?php echo htmlspecialchars($phone); ?>"
                                   required
                                   placeholder="Ví dụ: 0901234567">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Địa chỉ nhận hàng thực tế *</label>
                            <textarea name="shipping_address"
                                      class="form-control rounded-3"
                                      rows="3"
                                      required
                                      placeholder="Ghi cụ thể số nhà, tên đường, phường/xã, quận/huyện..."><?php echo htmlspecialchars($address); ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold">Ghi chú đơn hàng (Nếu có)</label>
                            <textarea name="note"
                                      class="form-control rounded-3"
                                      rows="2"
                                      placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi đến 15 phút..."></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit"
                                    name="btn_order"
                                    class="btn btn-gold btn-lg py-3 text-uppercase fs-6 fw-bold shadow-sm"
                                    id="btn-submit-order">
                                <i class="bi bi-credit-card me-2"></i> Xác Nhận Đặt Hàng
                            </button>
                        </div>

                    </form>
                </div>
            </div>

            <!-- ===== CỘT PHẢI: TÓM TẮT ĐƠN HÀNG ===== -->
            <div class="col-lg-6" data-aos="fade-left">
                <div class="glass-card p-4 p-md-5 h-100 d-flex flex-column">
                    <h4 class="fw-bold mb-4 text-dark">
                        <i class="bi bi-bag-check text-warning me-2"></i>Tóm Tắt Đơn Hàng
                    </h4>

                    <!-- Danh sách sản phẩm -->
                    <div class="cart-items-wrapper flex-grow-1" id="cart-items-wrapper">
                        <?php foreach ($products_in_cart as $item): ?>
                            <div class="product-row"
                                 data-id="<?php echo $item['id']; ?>"
                                 data-price="<?php echo $item['price']; ?>">

                                <!-- Tên + đơn giá -->
                                <div class="product-info">
                                    <h6 class="fw-bold text-dark mb-1">
                                        <?php echo htmlspecialchars($item['name']); ?>
                                    </h6>
                                    <small class="text-danger fw-bold item-price-display">
                                        <?php echo number_format($item['price'], 0, ',', '.'); ?> đ
                                    </small>
                                </div>

                                <!-- Điều chỉnh số lượng + thành tiền -->
                                <div class="product-qty-group">
                                    <div class="qty-control input-group input-group-sm">
                                        <button class="btn btn-outline-secondary rounded-start-pill btn-qty-minus"
                                                type="button">
                                            <i class="bi bi-dash"></i>
                                        </button>
                                        <input type="text"
                                               class="form-control text-center bg-light fw-bold input-qty"
                                               value="<?php echo $item['qty']; ?>"
                                               readonly>
                                        <button class="btn btn-outline-secondary rounded-end-pill btn-qty-plus"
                                                type="button">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </div>
                                    <span class="item-subtotal fw-bold text-dark">
                                        <?php echo number_format($item['subtotal'], 0, ',', '.'); ?> đ
                                    </span>
                                </div>

                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Tóm tắt tổng tiền -->
                    <div class="order-summary-footer border-top pt-4 mt-3">
                        <div class="summary-row mb-2">
                            <span class="text-muted">Tạm tính:</span>
                            <span class="fw-semibold text-dark" id="txt-subtotal">
                                <?php echo number_format($grand_total, 0, ',', '.'); ?> đ
                            </span>
                        </div>
                        <div class="summary-row mb-3">
                            <span class="text-muted">Phí vận chuyển:</span>
                            <span class="text-success fw-semibold">Miễn phí (Freeship)</span>
                        </div>
                        <hr class="border-secondary opacity-25">
                        <div class="summary-row summary-total">
                            <span class="fw-bold fs-5 text-dark">Tổng tiền phải trả:</span>
                            <span class="fw-bold fs-4 text-danger" id="txt-grand-total">
                                <?php echo number_format($grand_total, 0, ',', '.'); ?> đ
                            </span>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    <?php endif; ?>

</div>

<script src="assets/js/checkout.js"></script>

<?php include_once 'includes/footer.php'; ?>