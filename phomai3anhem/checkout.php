<?php
// ============================================================
// File: checkout.php
// Chức năng: Trang tiến hành thanh toán (Cửa hàng Phô Mai 3 Anh Em)
//            Tích hợp tính năng trùng khớp thông tin & Gợi ý quét mã QR
// ============================================================
session_start();
include_once 'config/db.php';
include_once 'includes/header.php'; // Navbar chung của bạn

// Giả định giỏ hàng lưu trong Session (Nếu chưa có, lấy demo để hiển thị)
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    // Dữ liệu demo khớp với ảnh bạn gửi để test giao diện
    $_SESSION['cart'] = [
        [
            'id' => 10,
            'name' => 'Emmental Grand Cru',
            'image' => 'emmental.jpg', // Tên file ảnh trong assets/img/
            'price' => 340000,
            'quantity' => 1
        ]
    ];
}

// Tính tổng tiền giỏ hàng
$total_money = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_money += $item['price'] * $item['quantity'];
}
?>

<div class="container my-5 pt-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-dark mb-2">Tiến Hành Thanh Toán</h2>
        <p class="text-muted small">Vui lòng điền thông tin đặt nhận hàng chính xác để hoàn tất đơn hàng.</p>
    </div>

    <div id="qrPaymentSection" class="row justify-content-center mb-5 d-none">
        <div class="col-12 col-md-6 text-center">
            <div class="card border-0 shadow-lg rounded-4 p-4 bg-white border-top border-4 border-warning">
                <div class="text-success mb-3">
                    <i class="bi bi-check-circle-fill display-4"></i>
                </div>
                <h4 class="fw-bold text-dark mb-1">Đặt Hàng Thành Công!</h4>
                <p class="text-muted small">Mã đơn hàng của bạn: <span class="fw-bold text-dark" id="txtOrderCode">#3942</span></p>
                <hr class="text-muted my-3">
                
                <p class="fw-bold text-secondary mb-3"><i class="bi bi-qr-code me-2"></i>QUÉT MÃ QR ĐỂ THANH TOÁN</p>
                
                <img src="https://img.vietqr.io/image/MBBank-09128390183-qr_only.png?amount=<?= $total_money ?>&addInfo=Phomai3Anhem%20ThanhToan%20DonHang" 
                     alt="Mã QR Thanh Toán" class="img-fluid rounded-3 border p-2 bg-light shadow-sm mb-3" style="max-width: 250px;">
                
                <div class="alert alert-warning text-start small mb-0 rounded-3">
                    <strong>Hướng dẫn:</strong> Mở ứng dụng Ngân hàng (Banking) hoặc Ví điện tử quét mã QR trên để chuyển khoản số tiền <strong><?= number_format($total_money, 0, ',', '.') ?>đ</strong> tự động chuẩn xác.
                </div>
                <div class="mt-4">
                    <a href="index.php" class="btn btn-gold text-white rounded-pill px-4">Quay về Trang Chủ</a>
                </div>
            </div>
        </div>
    </div>

    <div id="checkoutFormSection" class="row g-4">
        
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <form id="formCheckout" onsubmit="executeOrder(event)">
                    
                    <div class="row g-4">
                        <div class="col-12 col-md-6 border-end-md">
                            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-person-fill text-warning me-2"></i>Thông Tin Người Mua</h5>
                            
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Họ tên người mua <span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded-3" id="buyerName" required placeholder="Ví dụ: Nguyễn Văn A">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control rounded-3" id="buyerPhone" required placeholder="Nhập số điện thoại...">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Địa chỉ Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control rounded-3" id="buyerEmail" required placeholder="name@gmail.com">
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-truck text-warning me-2"></i>Người Nhận Hàng</h5>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="chkSameAsBuyer" onchange="syncBuyerToReceiver()">
                                    <label class="form-check-label small fw-bold text-warning cursor-pointer" for="chkSameAsBuyer">
                                        GIỐNG NGƯỜI MUA
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Họ tên người nhận <span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded-3" id="receiverName" required placeholder="Tên người nhận hàng...">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Số điện thoại nhận <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control rounded-3" id="receiverPhone" required placeholder="Số điện thoại gọi giao hàng...">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Địa chỉ giao hàng chính xác <span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded-3" id="receiverAddress" required placeholder="Số nhà, tên đường, quận/huyện...">
                            </div>
                        </div>
                        
                        <div class="col-12 border-top pt-3">
                            <div class="mb-2">
                                <label class="form-label small fw-semibold text-secondary">Ghi chú đơn hàng (Tùy chọn)</label>
                                <textarea class="form-control rounded-3" id="orderNotes" rows="2" placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi đến 15 phút..."></textarea>
                            </div>
                        </div>
                    </div>
                    
                </form>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-bag-check-fill text-warning me-2"></i>Đơn Hàng</h5>
                
                <div class="order-summary-list mb-4">
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                            <div class="d-flex align-items-center gap-3">
                                <img src="assets/img/<?= !empty($item['image']) ? $item['image'] : 'default.jpg'; ?>" 
                                     class="rounded-3 border" style="width: 50px; height: 50px; object-fit: contain; background: #fafafa;" alt="<?= htmlspecialchars($item['name']) ?>">
                                <div>
                                    <h6 class="fw-bold text-dark mb-0 small"><?= htmlspecialchars($item['name']) ?></h6>
                                    <small class="text-muted"><?= number_format($item['price'], 0, ',', '.') ?>đ x <?= $item['quantity'] ?></small>
                                </div>
                            </div>
                            <span class="fw-bold text-dark small"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>đ</span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="bg-light rounded-3 p-3 mb-4">
                    <div class="d-flex justify-content-between mb-2 small text-secondary">
                        <span>Tạm tính:</span>
                        <span><?= number_format($total_money, 0, ',', '.') ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <span class="fw-bold text-dark">Tổng thanh toán:</span>
                        <span class="fw-bold text-danger fs-5"><?= number_format($total_money, 0, ',', '.') ?>đ</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-dark mb-2">Phương thức thanh toán trực tuyến</label>
                    <div class="border border-warning rounded-3 p-3 bg-warning-subtle d-flex align-items-start gap-3">
                        <input class="form-check-input mt-1" type="radio" checked name="payment_method" id="radQr">
                        <div>
                            <label class="form-check-label fw-bold text-dark small d-block" for="radQr">
                                <i class="bi bi-bank me-1 text-warning"></i> Chuyển khoản ngân hàng trực tiếp
                            </label>
                            <small class="text-muted d-block" style="font-size: 11px;">Hệ thống sẽ hiển thị mã QR động sau khi bấm nút đặt hàng dưới đây.</small>
                        </div>
                    </div>
                </div>

                <button type="button" onclick="triggerSubmitForm()" class="btn btn-gold text-white w-100 py-2.5 rounded-pill fw-bold shadow-sm text-uppercase">
                    <i class="bi bi-wallet2 me-2"></i> Thanh Toán Ngay
                </button>
            </div>
        </div>

    </div>
</div>

<script src="assets/js/checkout.js">
</script>

<style>
.btn-gold {
    background-color: #E5A93B;
    border: none;
    font-weight: 600;
    transition: all 0.3s ease;
}
.btn-gold:hover {
    background-color: #C98F2A;
    transform: translateY(-2px);
}
.cursor-pointer {
    cursor: pointer;
}
.form-control:focus {
    border-color: #E5A93B !important;
    box-shadow: 0 0 0 0.25rem rgba(229, 169, 59, 0.15) !important;
}
.bg-warning-subtle {
    background-color: rgba(229, 169, 59, 0.08) !important;
}
@media (min-width: 768px) {
    .border-end-md {
        border-end: 1px solid #dee2e6 !important;
        border-right: 1px solid #dee2e6 !important;
    }
}
</style>

<?php include_once 'includes/footer.php'; ?>