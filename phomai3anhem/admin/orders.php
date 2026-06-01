<?php 
// ============================================================
// File: admin/orders.php
// Chức năng: Quản lý danh sách đơn hàng (Đồng bộ CSDL mới)
//            Tích hợp thay đổi trạng thái trực tiếp bằng Fetch API
//            Tích hợp thanh tìm kiếm và bộ lọc thời gian thực
// ============================================================
include_once 'admin-check.php';
include_once '../config/db.php'; 

// Xử lý đổi trạng thái duyệt đơn nhanh bằng AJAX
if (isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['status'];
    $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$new_status, $order_id]);
    echo json_encode(['status' => 'success']);
    exit();
}

include_once 'includes/header.php'; 
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

<div class="table-responsive bg-white rounded-3 shadow-sm p-3">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Mã ĐH</th>
                <th>Người Đặt (Mua)</th>
                <th>Người Nhận Hàng</th>
                <th>Tổng Tiền</th>
                <th>Ngày Đặt</th>
                <th>Trạng Thái</th>
                <th>Thao Tác</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Câu truy vấn lấy danh sách đơn hàng
            $stmt = $pdo->query("SELECT * FROM orders ORDER BY id DESC");
            $has_orders = false;
            while ($row = $stmt->fetch()):
                $has_orders = true;
                
                // Chuẩn bị các thuộc tính để phục vụ cho hàm bộ lọc tìm kiếm JavaScript
                $customer_search = text_clean($row['full_name'] . ' ' . ($row['receiver_name'] ?? ''));
                $phone_search = text_clean($row['phone'] . ' ' . ($row['receiver_phone'] ?? ''));
                
                // Thiết lập class màu tương ứng cho thẻ select trạng thái
                $select_color_class = 'status-pending';
                if ($row['status'] == 'shipping') $select_color_class = 'status-shipping';
                if ($row['status'] == 'completed') $select_color_class = 'status-completed';
                if ($row['status'] == 'cancelled') $select_color_class = 'status-cancelled';
            ?>
            <tr class="order-data-row" 
                data-id="<?= $row['id'] ?>" 
                data-customer="<?= htmlspecialchars($customer_search) ?>" 
                data-phone="<?= htmlspecialchars($phone_search) ?>" 
                data-status="<?= $row['status'] ?>">
                
                <td class="fw-bold">#<?= $row['id'] ?></td>
                <td>
                    <div class="fw-semibold text-dark"><?= htmlspecialchars($row['full_name']) ?></div>
                    <small class="text-muted"><?= htmlspecialchars($row['phone']) ?></small>
                </td>
                <td>
                    <div class="fw-semibold text-warning">
                        <?= htmlspecialchars($row['receiver_name'] ?: $row['full_name']) ?>
                    </div>
                    <small class="text-muted d-block">
                        <i class="bi bi-telephone small"></i> <?= htmlspecialchars($row['receiver_phone'] ?: $row['phone']) ?>
                    </small>
                    <small class="text-muted text-truncate d-inline-block" style="max-width: 200px;" title="<?= htmlspecialchars($row['receiver_address'] ?: $row['address']) ?>">
                        <i class="bi bi-geo-alt small"></i> <?= htmlspecialchars($row['receiver_address'] ?: $row['address']) ?>
                    </small>
                </td>
                <td class="fw-bold text-danger"><?= number_format($row['total_money'], 0, ',', '.') ?>đ</td>
                <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                <td>
                    <select class="form-select form-select-sm rounded-pill px-3 fw-bold change-order-status <?= $select_color_class ?>" 
                            data-id="<?= $row['id'] ?>" style="width: 155px; font-size: 12px;">
                        <option value="pending" <?= $row['status'] == 'pending' ? 'selected' : '' ?>>🟠 CHỜ XỬ LÝ</option>
                        <option value="shipping" <?= $row['status'] == 'shipping' ? 'selected' : '' ?>>🔵 ĐANG GIAO</option>
                        <option value="completed" <?= $row['status'] == 'completed' ? 'selected' : '' ?>>🟢 THÀNH CÔNG</option>
                        <option value="cancelled" <?= $row['status'] == 'cancelled' ? 'selected' : '' ?>>🔴 ĐÃ HỦY ĐƠN</option>
                    </select>
                </td>
                <td>
                    <a href="orders-detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        <i class="bi bi-eye"></i> Chi tiết
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
            
            <tr id="noResultsRow" style="display: <?= $has_orders ? 'none' : '' ?>;">
                <td colspan="7" class="text-center text-muted py-4 small">
                    <i class="bi bi-inbox display-6 d-block mb-2 text-secondary"></i>
                    Không tìm thấy dữ liệu đơn hàng nào trùng khớp.
                </td>
            </tr>
        </tbody>
    </table>
</div>

<?php 
// Hàm bổ trợ dọn dẹp chuỗi chữ phục vụ tìm kiếm không phân biệt hoa thường
function text_clean($str) {
    return mb_strtolower(trim($str), 'UTF-8');
}
?>

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
        if (visibleCount === 0) {
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
        let self = this;

        // Đồng bộ lại thuộc tính data-status của thẻ tr để khi đang lọc không bị lệch cấu trúc hiển thị
        self.closest('.order-data-row').setAttribute('data-status', statusVal);

        // Thay đổi class màu sắc của select dựa trên giá trị mới chọn
        self.classList.remove('status-pending', 'status-shipping', 'status-completed', 'status-cancelled');
        if (statusVal === 'pending') self.classList.add('status-pending');
        if (statusVal === 'shipping') self.classList.add('status-shipping');
        if (statusVal === 'completed') self.classList.add('status-completed');
        if (statusVal === 'cancelled') self.classList.add('status-cancelled');

        let formData = new FormData();
        formData.append('update_status', true);
        formData.append('order_id', orderId);
        formData.append('status', statusVal);

        fetch('orders.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => { 
            if(data.status === 'success') {
                // Hiển thị một hiệu ứng nháy nhẹ dòng vừa cập nhật để Admin nhận biết trực quan
                let trRow = self.closest('.order-data-row');
                trRow.style.backgroundColor = 'rgba(229, 169, 59, 0.1)';
                setTimeout(() => {
                    trRow.style.backgroundColor = '';
                }, 400);
            } 
        })
        .catch(err => {
            console.error("Lỗi:", err);
            alert('Không thể kết nối đến máy chủ để cập nhật.');
        });
    });
});
</script>

<style>
/* CSS Tùy biến thanh select trạng thái giống cấu trúc Badge trực quan */
.change-order-status { border: 1px solid transparent !important; cursor: pointer; }
.status-pending { background-color: #fff3cd !important; color: #664d03 !important; border-color: #ffecb5 !important; }
.status-shipping { background-color: #cff4fc !important; color: #087990 !important; border-color: #b6effb !important; }
.status-completed { background-color: #d1e7dd !important; color: #0f5132 !important; border-color: #badbcc !important; }
.status-cancelled { background-color: #f8d7da !important; color: #842029 !important; border-color: #f5c2c7 !important; }

.form-select:focus, .form-control:focus {
    border-color: #E5A93B !important;
    box-shadow: 0 0 0 0.25rem rgba(229, 169, 59, 0.15) !important;
}
.order-data-row { transition: background-color 0.3s ease; }
</style>

<?php include_once 'includes/footer.php'; ?>