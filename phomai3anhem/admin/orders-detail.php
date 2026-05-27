<?php 
include_once '../config/db.php'; 
include_once 'includes/header.php'; 

// 1. Nhận mã đơn hàng từ liên kết URL
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. Truy vấn thông tin tổng quan của đơn hàng (bảng orders)
$stmt_order = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt_order->execute([$order_id]);
$order = $stmt_order->fetch();

if (!$order) {
    echo "<div class='alert alert-danger'>Mã đơn hàng không hợp lệ hoặc đã bị xóa khỏi hệ thống.</div>";
    include_once 'includes/footer.php';
    exit();
}

// 3. Truy vấn danh sách chi tiết các mặt hàng mua trong đơn đó (bảng order_items)
$stmt_items = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt_items->execute([$order_id]);
$items = $stmt_items->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0 text-dark"><i class="bi bi-file-earmark-text me-2"></i>Chi Tiết Đơn Hàng #<?php echo $order['id']; ?></h4>
    <a href="orders.php" class="btn btn-outline-secondary btn-sm px-3"><i class="bi bi-arrow-left"></i> Quay lại danh sách</a>
</div>

<div class="row g-4">
    <div class="col-md-5" data-aos="fade-right">
        <div class="card admin-card bg-white p-4 mb-4">
            <h5 class="fw-bold mb-3 border-bottom pb-2 text-warning">Thông Tin Khách Nhận</h5>
            <div class="mb-2 small"><strong>Họ và tên:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></div>
            <div class="mb-2 small"><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?></div>
            <div class="mb-2 small"><strong>Địa chỉ giao:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?></div>
            <div class="mb-2 small"><strong>Mã Code đơn:</strong> <span class="text-primary font-monospace"><?php echo htmlspecialchars($order['order_code'] ?? 'PM-UNKNOWN'); ?></span></div>
            <div class="mb-0 small"><strong>Ghi chú từ khách:</strong> <span class="text-muted"><?php echo htmlspecialchars($order['note'] ?: 'Không có ghi chú.'); ?></span></div>
        </div>

        <div class="card admin-card bg-white p-4">
            <h5 class="fw-bold mb-3 border-bottom pb-2 text-warning">Trạng Thái Xử Lý</h5>
            <div class="d-flex align-items-center justify-content-between">
                <span class="small">Tình trạng đơn hàng:</span>
                <span class="badge py-2 px-3 fs-6 <?php 
                    echo $order['order_status'] == 'pending' ? 'bg-warning text-dark' : 
                        ($order['order_status'] == 'shipping' ? 'bg-info text-white' : 
                        ($order['order_status'] == 'completed' ? 'bg-success text-white' : 'bg-danger')); 
                ?>">
                    <?php echo strtoupper($order['order_status']); ?>
                </span>
            </div>
            <button class="btn btn-dark w-100 btn-sm mt-4 py-2" onclick="window.print()"><i class="bi bi-printer me-1"></i> In phiếu giao hàng (Hóa đơn)</button>
        </div>
    </div>

    <div class="col-md-7" data-aos="fade-left">
        <div class="card admin-card bg-white p-4">
            <h5 class="fw-bold mb-3 border-bottom pb-2 text-warning">Giỏ Hàng Đặt Mua</h5>
            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center m-0 small">
                    <thead class="table-light">
                        <tr>
                            <th class="text-start">Tên phô mai</th>
                            <th>Đơn giá</th>
                            <th>SL</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($items as $item): ?>
                        <tr>
                            <td class="text-start fw-bold"><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><?php echo number_format($item['unit_price'], 0, ',', '.'); ?>đ</td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td class="text-danger fw-bold"><?php echo number_format($item['subtotal'], 0, ',', '.'); ?>đ</td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="table-light fs-6">
                            <td colspan="3" class="text-end fw-bold">Tổng thanh toán:</td>
                            <td class="text-danger fw-bold fs-5"><?php echo number_format($order['grand_total'], 0, ',', '.'); ?>đ</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>