<?php 
// ============================================================
// File: admin/orders.php
// Chức năng: Quản lý danh sách đơn hàng (Đồng bộ CSDL mới)
//            Tích hợp thanh tìm kiếm và bộ lọc thời gian thực
// ============================================================
include_once 'admin-check.php';
include_once '../config/db.php'; 

// Xử lý đổi trạng thái duyệt đơn nhanh bằng AJAX
if (isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['status'];
    // Đổi order_status thành status
    $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$new_status, $order_id]);
    echo json_encode(['status' => 'success']);
    exit();
}

include_once 'includes/header.php'; 
$orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC")->fetchAll();
?>

<div class="mb-4">
    <h4 class="fw-bold text-dark"><i class="bi bi-receipt me-2"></i>Hệ thống Quản lý Đơn Hàng</h4>
</div>

<div class="card border-0 shadow-sm rounded-4 p-3 bg-white mb-4">
    <div class="row g-3">
        <div class="col-12 col-md-7 col-lg-8">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted rounded-start-3"><i class="bi bi-search"></i></span>
                <input type="text" id="searchOrderInput" class="form-control bg-light border-start-0 rounded-end-3 py-2 small" placeholder="Tìm theo mã đơn (VD: #12), tên khách hàng hoặc số điện thoại..." onkeyup="filterAdminOrders()">
            </div>
        </div>
        <div class="col-12 col-md-5 col-lg-4">
            <select id="filterStatusSelect" class="form-select bg-light rounded-3 py-2 small text-secondary" onchange="filterAdminOrders()">
                <option value="all">-- Tất cả trạng thái đơn --</option>
                <option value="pending">Chờ xử lý</option>
                <option value="shipping">Đang giao hàng</option>
                <option value="completed">Thành công</option>
                <option value="cancelled">Đã hủy đơn</option>
            </select>
        </div>
    </div>
</div>

<div class="card admin-card bg-white border-0 shadow-sm rounded-4 p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle text-center m-0" id="adminOrdersTable">
            <thead class="table-light text-secondary small text-uppercase">
                <tr>
                    <th>Mã đơn</th>
                    <th class="text-start">Khách hàng</th>
                    <th>Số điện thoại</th>
                    <th>Ngày đặt</th>
                    <th>Tổng tiền phải thu</th>
                    <th>Trạng thái đơn</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($orders) > 0): ?>
                    <?php foreach($orders as $o): ?>
                    <tr class="order-data-row"
                        data-id="#<?php echo $o['id']; ?>"
                        data-customer="<?php echo mb_strtolower(htmlspecialchars($o['full_name']), 'UTF-8'); ?>"
                        data-phone="<?php echo htmlspecialchars($o['phone']); ?>"
                        data-status="<?php echo strtolower(htmlspecialchars($o['status'])); ?>">
                        <td><span class="badge bg-light text-dark border">#<?php echo $o['id']; ?></span></td>
                        <td class="text-start fw-bold text-dark"><?php echo htmlspecialchars($o['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($o['phone']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($o['created_at'])); ?></td>
                        <td class="text-danger fw-bold"><?php echo number_format($o['total_money'], 0, ',', '.'); ?>đ</td>
                        <td>
                            <select class="form-select form-select-sm d-inline-block w-auto rounded-3 change-order-status" data-id="<?php echo $o['id']; ?>">
                                <option value="pending" <?php echo $o['status']=='pending'?'selected':''; ?>>Chờ xử lý</option>
                                <option value="shipping" <?php echo $o['status']=='shipping'?'selected':''; ?>>Đang giao hàng</option>
                                <option value="completed" <?php echo $o['status']=='completed'?'selected':''; ?>>Thành công</option>
                                <option value="cancelled" <?php echo $o['status']=='cancelled'?'selected':''; ?>>Đã hủy đơn</option>
                            </select>
                        </td>
                        <td>
                            <div class="btn-group gap-1">
                                <a href="orders-detail.php?id=<?php echo $o['id']; ?>" class="btn btn-sm btn-outline-secondary rounded-3 border-0 px-2" title="Xem chi tiết">
                                    <i class="bi bi-eye fs-5"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Chưa có đơn hàng nào trong hệ thống.</td>
                    </tr>
                <?php endif; ?>
                
                <tr id="noResultsRow" style="display: none;">
                    <td colspan="7" class="text-center py-5 text-muted small">
                        <i class="bi bi-receipt-cutoff me-1 fs-5 d-block mb-2"></i> Không tìm thấy đơn hàng nào khớp với điều kiện lọc hiện tại.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
// Hàm lọc danh sách đơn hàng Realtime tại giao diện Client
function filterAdminOrders() {
    let keyword = document.getElementById('searchOrderInput').value.toLowerCase().trim();
    let selectedStatus = document.getElementById('filterStatusSelect').value;
    
    let rows = document.querySelectorAll('.order-data-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        let idAttr = row.getAttribute('data-id').toLowerCase();
        let customerAttr = row.getAttribute('data-customer');
        let phoneAttr = row.getAttribute('data-phone');
        let statusAttr = row.getAttribute('data-status');
        
        // Điều kiện khớp từ khóa (Mã đơn, Tên khách hoặc Số điện thoại)
        let matchKeyword = (idAttr.includes(keyword) || customerAttr.includes(keyword) || phoneAttr.includes(keyword));
        // Điều kiện khớp Trạng thái
        let matchStatus = (selectedStatus === 'all' || statusAttr === selectedStatus);
        
        if (matchKeyword && matchStatus) {
            row.style.display = "";
            visibleCount++;
        } else {
            row.style.display = "none";
        }
    });
    
    // Quản lý hiển thị dòng thông báo dự phòng khi kết quả trống rỗng
    let noResultsRow = document.getElementById('noResultsRow');
    if (noResultsRow) {
        if (visibleCount === 0 && rows.length > 0) {
            noResultsRow.style.display = "";
        } else {
            noResultsRow.style.display = "none";
        }
    }
}

// Xử lý gửi ngầm lệnh cập nhật trạng thái đơn hàng mượt mà bằng Fetch API
document.querySelectorAll('.change-order-status').forEach(select => {
    select.addEventListener('change', function() {
        let orderId = this.getAttribute('data-id');
        let statusVal = this.value;

        // Đồng bộ lại thuộc tính data-status của thẻ tr để khi đang lọc không bị lệch cấu trúc hiển thị
        this.closest('.order-data-row').setAttribute('data-status', statusVal);

        let formData = new FormData();
        formData.append('update_status', true);
        formData.append('order_id', orderId);
        formData.append('status', statusVal);

        fetch('orders.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => { 
            if(data.status === 'success') {
                alert('Cập nhật trạng thái đơn hàng #' + orderId + ' thành công!'); 
            } 
        })
        .catch(err => console.error("Lỗi:", err));
    });
});
</script>

<style>
.form-select:focus, .form-control:focus {
    border-color: #E5A93B !important;
    box-shadow: 0 0 0 0.25rem rgba(229, 169, 59, 0.15) !important;
}
</style>

<?php include_once 'includes/footer.php'; ?>