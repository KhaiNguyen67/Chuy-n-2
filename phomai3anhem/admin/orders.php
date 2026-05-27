<?php 
include_once '../config/db.php'; 

// Xử lý đổi trạng thái duyệt đơn nhanh bằng AJAX hoặc GET request
if (isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['status'];
    $pdo->prepare("UPDATE orders SET order_status = ? WHERE id = ?")->execute([$new_status, $order_id]);
    echo json_encode(['status' => 'success']);
    exit();
}

include_once 'includes/header.php'; 
$orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC")->fetchAll();
?>

<div class="mb-4">
    <h4 class="fw-bold text-dark"><i class="bi bi-receipt me-2"></i>Hệ thống Quản lý Đơn Hàng</h4>
</div>

<div class="card admin-card bg-white p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle text-center m-0">
            <thead class="table-light">
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Số điện thoại</th>
                    <th>Ngày đặt</th>
                    <th>Tổng tiền phải thu</th>
                    <th>Trạng thái đơn</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($orders as $o): ?>
                <tr>
                    <td><span class="badge bg-light text-dark border">#<?php echo $o['id']; ?></span></td>
                    <td class="text-start fw-bold"><?php echo htmlspecialchars($o['customer_name']); ?></td>
                    <td><?php echo htmlspecialchars($o['customer_phone']); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($o['created_at'])); ?></td>
                    <td class="text-danger fw-bold"><?php echo number_format($o['grand_total'], 0, ',', '.'); ?>đ</td>
                    <td>
                        <select class="form-select form-select-sm d-inline-block w-auto change-order-status" data-id="<?php echo $o['id']; ?>">
                            <option value="pending" <?php echo $o['order_status']=='pending'?'selected':''; ?>>Chờ xử lý</option>
                            <option value="shipping" <?php echo $o['order_status']=='shipping'?'selected':''; ?>>Đang giao hàng</option>
                            <option value="completed" <?php echo $o['order_status']=='completed'?'selected':''; ?>>Thành công</option>
                            <option value="cancelled" <?php echo $o['order_status']=='cancelled'?'selected':''; ?>>Đã hủy đơn</option>
                        </select>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i> Xem</button>
                        <button class="btn btn-sm btn-outline-dark" onclick="window.print()"><i class="bi bi-printer"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Xử lý gửi ngầm lệnh cập nhật trạng thái đơn hàng mượt mà bằng Fetch API
document.querySelectorAll('.change-order-status').forEach(select => {
    select.addEventListener('change', function() {
        let orderId = this.getAttribute('data-id');
        let statusVal = this.value;

        let formData = new FormData();
        formData.append('update_status', true);
        formData.append('order_id', orderId);
        formData.append('status', statusVal);

        fetch('orders.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => { if(data.status === 'success') alert('Cập nhật trạng thái đơn hàng thành công!'); });
    });
});
</script>

<?php include_once 'includes/footer.php'; ?>