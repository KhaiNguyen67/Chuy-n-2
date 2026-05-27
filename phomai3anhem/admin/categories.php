<?php 
include_once '../config/db.php'; 

// 1. Xử lý thêm danh mục mới
if (isset($_POST['btn_add_category'])) {
    $cate_name = trim($_POST['name']);
    $description = trim($_POST['description'] ?? '');
    
    if (!empty($cate_name)) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        $stmt->execute([$cate_name, $description]);
        header("Location: categories.php");
        exit();
    }
}

// 2. Xử lý xóa danh mục
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $del_id = (int)$_GET['id'];
    // Kiểm tra xem danh mục có chứa sản phẩm không trước khi xóa
    $count = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ? AND is_active = 1");
    $count->execute([$del_id]);
    
    if ($count->fetchColumn() == 0) {
        $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$del_id]);
        header("Location: categories.php");
    } else {
        echo "<script>alert('Không thể xóa! Danh mục này đang chứa sản phẩm kinh doanh.'); window.location='categories.php';</script>";
    }
    exit();
}

include_once 'includes/header.php'; 
// Lấy toàn bộ danh mục từ database
$categories = $pdo->query("SELECT c.*, COUNT(p.id) AS total_products FROM categories c 
                           LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1 
                           GROUP BY c.id ORDER BY c.id ASC")->fetchAll();
?>

<div class="row g-4">
    <div class="col-md-4" data-aos="fade-right">
        <div class="card admin-card bg-white p-4">
            <h5 class="fw-bold mb-3">Thêm Danh Mục Mới</h5>
            <form method="POST" action="categories.php">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Tên phân loại phô mai *</label>
                    <input type="text" name="name" class="form-control" placeholder="Ví dụ: Phô mai bán cứng" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Mô tả đặc điểm</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Nhập vài dòng mô tả ngắn..."></textarea>
                </div>
                <button type="submit" name="btn_add_category" class="btn btn-gold text-white btn-sm w-100 py-2 rounded-3">
                    <i class="bi bi-plus-circle me-1"></i> Lưu danh mục
                </button>
            </form>
        </div>
    </div>

    <div class="col-md-8" data-aos="fade-left">
        <div class="card admin-card bg-white p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-tags me-2"></i>Cây Thư Mục Phân Loại</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center m-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mã ID</th>
                            <th class="text-start">Tên danh mục</th>
                            <th class="text-start">Mô tả chi tiết</th>
                            <th>Số sản phẩm</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($categories as $cat): ?>
                        <tr>
                            <td><strong>#<?php echo $cat['id']; ?></strong></td>
                            <td class="text-start fw-bold text-dark"><?php echo htmlspecialchars($cat['name']); ?></td>
                            <td class="text-start text-muted small"><?php echo htmlspecialchars($cat['description'] ?: 'Chưa có mô tả.'); ?></td>
                            <td><span class="badge bg-warning text-dark fw-bold"><?php echo $cat['total_products']; ?> món</span></td>
                            <td>
                                <a href="categories.php?action=delete&id=<?php echo $cat['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn chắc chắn muốn xóa danh mục này?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>