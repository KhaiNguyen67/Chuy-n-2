<?php 
include_once '../config/db.php'; 
include_once 'includes/header.php'; 

// Tính toán các con số tổng quan (Khớp cấu trúc bảng orders trong database.sql)
$total_revenue = $pdo->query("SELECT SUM(grand_total) FROM orders WHERE order_status = 'completed'")->fetchColumn() ?? 0;
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$new_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'pending'")->fetchColumn();
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold m-0 text-dark">Trang Tổng Quan Thống Kê</h3>
    <span class="badge bg-white text-dark border p-2"><i class="bi bi-calendar3 me-2"></i>Hôm nay: <?php echo date('d/m/Y'); ?></span>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card admin-card p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted small text-uppercase fw-bold">Tổng doanh thu</h6>
                    <h3 class="fw-bold text-success m-0"><?php echo number_format($total_revenue, 0, ',', '.'); ?>đ</h3>
                </div>
                <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success fs-3 lh-1"><i class="bi bi-currency-dollar"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card admin-card p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted small text-uppercase fw-bold">Tổng đơn hàng</h6>
                    <h3 class="fw-bold text-dark m-0"><?php echo $total_orders; ?></h3>
                </div>
                <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-primary fs-3 lh-1"><i class="bi bi-cart3"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card admin-card p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted small text-uppercase fw-bold">Đơn hàng mới chờ duyệt</h6>
                    <h3 class="fw-bold text-danger m-0"><?php echo $new_orders; ?></h3>
                </div>
                <div class="bg-danger bg-opacity-10 p-3 rounded-3 text-danger fs-3 lh-1"><i class="bi bi-bell-fill animate-bounce"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card admin-card p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted small text-uppercase fw-bold">Sản phẩm phô mai</h6>
                    <h3 class="fw-bold text-warning m-0"><?php echo $total_products; ?></h3>
                </div>
                <div class="bg-warning bg-opacity-10 p-3 rounded-3 text-warning fs-3 lh-1"><i class="bi bi-egg-fried"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card admin-card p-4 bg-white h-100">
            <h5 class="fw-bold mb-4">Biểu đồ xu hướng doanh số năm 2026</h5>
            <canvas id="revenueChart" style="max-height: 300px;"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card admin-card p-4 bg-white h-100">
            <h5 class="fw-bold mb-3">Đơn hàng mới nhất</h5>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle small">
                    <tbody>
                        <?php
                        $latest_orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5")->fetchAll();
                        foreach($latest_orders as $o):
                        ?>
                        <tr>
                            <td><strong>#<?php echo $o['id']; ?></strong></td>
                            <td><?php echo htmlspecialchars($o['customer_name']); ?></td>
                            <td><span class="badge <?php echo $o['order_status']=='pending'?'bg-warning':'bg-success'; ?>"><?php echo $o['order_status']; ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Vẽ biểu đồ doanh thu mượt mà với ChartJS
const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6'],
        datasets: [{
            label: 'Doanh thu thực tế (VND)',
            data: [15000000, 23000000, 18000000, 31000000, <?php echo $total_revenue; ?>, 0],
            borderColor: '#cca43b',
            backgroundColor: 'rgba(204, 164, 59, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        }]
    }
});
</script>

<?php include_once 'includes/footer.php'; ?>