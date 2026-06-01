<?php
// ============================================================
// File: view_cart.php
// Chức năng: Hiển thị giỏ hàng, cập nhật số lượng & xóa sản phẩm
// ============================================================
include_once 'config/db.php';
include_once 'includes/header.php';

if (session_status() == PHP_SESSION_NONE) { session_start(); }
?>

<div class="container my-5">
    <div class="mb-4" data-aos="fade-up">
        <h2 class="fw-bold"><i class="bi bi-bag-check me-2 text-warning"></i>Giỏ Hàng Của Bạn</h2>
        <p class="text-muted small">Kiểm tra lại danh sách phô mai trước khi tiến hành thanh toán</p>
    </div>

    <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
        <div class="row g-4">
            
            <div class="col-lg-8" data-aos="fade-right">
                <form method="POST" action="cart.php?action=update" class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <div class="table-responsive">
                        <table class="table align-middle m-0 text-center">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-start">Sản phẩm</th>
                                    <th>Giá tiền</th>
                                    <th style="width: 140px;">Số lượng</th>
                                    <th>Thành tiền</th>
                                    <th>Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $grand_total = 0;
                                foreach ($_SESSION['cart'] as $id => $item): 
                                    $subtotal = $item['price'] * $item['quantity'];
                                    $grand_total += $subtotal;
                                    
                                    $local_image = 'assets/img/' . $item['image'];
                                    $img_src = (!empty($item['image']) && file_exists($local_image)) ? $local_image : 'https://images.unsplash.com/photo-1528750994863-30f4a7c05267?q=80&w=100';
                                ?>
                                <tr>
                                    <td class="text-start d-flex align-items-center gap-3">
                                        <img src="<?php echo $img_src; ?>" class="rounded-3 border bg-light" style="width: 55px; height: 55px; object-fit: contain;">
                                        <div>
                                            <h6 class="fw-bold m-0 text-dark"><?php echo htmlspecialchars($item['name']); ?></h6>
                                            <small class="text-muted">Kho: <?php echo $item['stock']; ?> chiếc</small>
                                        </div>
                                    </td>
                                    <td class="fw-semibold"><?php echo number_format($item['price'], 0, ',', '.'); ?>đ</td>
                                    <td>
                                        <input type="number" name="update_qty[<?php echo $id; ?>]" class="form-control form-control-sm text-center fw-bold rounded-3" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>" onchange="this.form.submit()">
                                    </td>
                                    <td class="text-danger fw-bold"><?php echo number_format($subtotal, 0, ',', '.'); ?>đ</td>
                                    <td>
                                        <a href="cart.php?action=delete&id=<?php echo $id; ?>" class="text-secondary hover-danger fs-5" onclick="return confirm('Xóa sản phẩm này khỏi giỏ hàng?')">
                                            <i class="bi bi-trash3"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                        <a href="product.php" class="btn btn-sm btn-outline-secondary rounded-3"><i class="bi bi-arrow-left me-1"></i> Tiếp tục mua sắm</a>
                        <button type="submit" class="btn btn-sm btn-dark rounded-3"><i class="bi bi-arrow-clockwise me-1"></i> Cập nhật giỏ hàng</button>
                    </div>
                </form>
            </div>

            <div class="col-lg-4" data-aos="fade-left">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white sticky-top" style="top: 100px;">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Tóm tắt đơn hàng</h5>
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>Tạm tính giỏ hàng:</span>
                        <span><?php echo number_format($grand_total, 0, ',', '.'); ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 small text-muted">
                        <span>Phí giao hàng:</span>
                        <span class="text-success">Miễn phí</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold">Tổng tiền phải trả:</span>
                        <span class="fs-4 fw-bold text-danger"><?php echo number_format($grand_total, 0, ',', '.'); ?> đ</span>
                    </div>
                    
                    <a href="checkout.php" class="btn btn-gold btn-lg w-100 py-3 text-uppercase fs-6 fw-bold shadow-sm rounded-3">
                        Tiến Hành Thanh Toán <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

        </div>
    <?php else: ?>
        <div class="text-center my-5 py-5 bg-white rounded-4 shadow-sm border" data-aos="zoom-in">
            <i class="bi bi-cart-x text-muted display-1"></i>
            <h5 class="fw-bold mt-4 text-dark">Giỏ hàng của bạn đang trống!</h5>
            <p class="text-muted small">Hãy quay lại cửa hàng chọn mua những miếng phô mai thơm ngon thượng hạng nhé.</p>
            <a href="product.php" class="btn btn-gold px-4 py-2 mt-2 text-white rounded-pill shadow-sm"><i class="bi bi-shop me-1"></i> Đến cửa hàng ngay</a>
        </div>
    <?php endif; ?>
</div>

<style>
.hover-danger:hover { color: #dc3545 !important; }
</style>

<?php include_once 'includes/footer.php'; ?>