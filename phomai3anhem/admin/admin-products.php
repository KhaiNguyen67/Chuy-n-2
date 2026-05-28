<?php
// ============================================================
// File: admin-products.php
// Chức năng: Giao diện danh sách sản phẩm Admin Dashboard (PDO)
// ============================================================
include_once '../config/db.php';
include_once 'includes/header.php'; // Sử dụng lại Navbar chung để đồng bộ

// Lấy danh sách danh mục để đổ vào thẻ Chọn (Select Option) trong Modal
$stmt_cats = $pdo->query("SELECT * FROM categories ORDER BY id ASC");
$categories = $stmt_cats->fetchAll();

// Lấy toàn bộ danh sách sản phẩm kèm tên danh mục tương ứng
$stmt_prods = $pdo->query("SELECT p.*, c.name AS category_name FROM products p 
                           LEFT JOIN categories c ON p.category_id = c.id 
                           ORDER BY p.id DESC");
$products = $stmt_prods->fetchAll();
?>

<div class="container my-5 pt-4">
    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] == 'success_add'): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-3 small" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>Thêm sản phẩm mới thành công!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($_GET['status'] == 'success_edit'): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-3 small" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>Cập nhật thông tin sản phẩm thành công!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($_GET['status'] == 'success_delete'): ?>
            <div class="alert alert-warning alert-dismissible fade show rounded-3 small" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>Đã xóa sản phẩm khỏi hệ thống cơ sở dữ liệu!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Bảng Điều Khiển Quản Trị</h3>
            <span class="badge bg-dark-subtle text-dark rounded-pill px-3 py-1.5 small text-uppercase">Quản lý sản phẩm</span>
        </div>
        <button class="btn btn-gold px-4 py-2 text-white" onclick="openAddModal()">
            <i class="bi bi-plus-circle me-2"></i>Thêm Sản Phẩm Mới
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.5px;">
                    <tr>
                        <th class="border-0 ps-4" style="width: 100px;">Hình ảnh</th>
                        <th class="border-0">Tên Sản Phẩm</th>
                        <th class="border-0">Phân Loại</th>
                        <th class="border-0 text-end">Giá Bán hiện tại</th>
                        <th class="border-0 text-center">Định Vị</th>
                        <th class="border-0 text-center" style="width: 140px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($products) > 0): ?>
                        <?php foreach ($products as $prod): ?>
                            <tr>
                                <td class="ps-4">
                                    <img src="../assets/img/<?= !empty($prod['image']) ? $prod['image'] : 'default.jpg'; ?>" 
                                         class="rounded-3 border" style="width: 55px; height: 55px; object-fit: contain; background: #fafafa;" alt="<?= htmlspecialchars($prod['name']); ?>">
                                </td>
                                <td>
                                    <div class="fw-bold text-dark mb-0"><?= htmlspecialchars($prod['name']); ?></div>
                                    <small class="text-muted d-block text-truncate" style="max-width: 280px;"><?= htmlspecialchars($prod['short_desc'] ?? 'Chưa cấu hình mô tả cụ thể...'); ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1.5 small">
                                        <?= htmlspecialchars($prod['category_name'] ?? 'Mặc định'); ?>
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-danger fs-6 pe-3">
                                    <?= number_format($prod['price'], 0, ',', '.'); ?> đ
                                </td>
                                <td class="text-center">
                                    <?php if (isset($prod['is_featured']) && $prod['is_featured'] == 1): ?>
                                        <span class="badge bg-danger-subtle text-danger rounded-pill px-2.5 py-1 small fw-bold">Bán chạy</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark rounded-pill px-2.5 py-1 small border">Thường</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group gap-1">
                                        <button class="btn btn-sm btn-outline-secondary rounded-3 border-0 px-2" 
                                                onclick='openEditModal(<?= json_encode($prod, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)' title="Chỉnh sửa">
                                            <i class="bi bi-pencil-square fs-5"></i>
                                        </button>
                                        <a href="admin-process.php?action=delete&id=<?= $prod['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger rounded-3 border-0 px-2" 
                                           onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này hoàn toàn khỏi Database?')" title="Xóa bỏ">
                                            <i class="bi bi-trash3 fs-5"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted small">Cơ sở dữ liệu trống. Hãy nhấn nút phía trên để khởi tạo sản phẩm đầu tiên!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="productFormModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-light border-0 py-3 px-4">
                <h5 class="modal-title fw-bold text-dark" id="modalComponentTitle">Thêm Sản Phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="admin-process.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="id" id="productId">
                    <input type="hidden" name="old_image" id="productOldImage">

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tên khối phô mai / Sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-3" name="name" id="productName" required placeholder="Ví dụ: Phô mai Parmesan Ý">
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Danh mục nhóm <span class="text-danger">*</span></label>
                            <select class="form-select rounded-3" name="category_id" id="productCategoryId" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id']; ?>"><?= htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Giá bán ra (đ) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control rounded-3" name="price" id="productPrice" required placeholder="180000">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Hình ảnh sản phẩm mới</label>
                        <input type="file" class="form-control rounded-3" name="image" accept="image/*">
                        <div id="imageHelpBlock" class="form-text small text-muted">Nếu sửa và giữ nguyên ảnh cũ, không cần chọn tệp tại ô này.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Mô tả tóm tắt</label>
                        <textarea class="form-control rounded-3" name="short_desc" id="productDesc" rows="3" placeholder="Nhập độ chín, xuất xứ hoặc cách chế biến khuyến nghị..."></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch py-1">
                            <input class="form-check-input" type="checkbox" role="switch" name="is_featured" id="productFeatured" value="1">
                            <label class="form-check-label small fw-bold text-dark" for="productFeatured">Đánh dấu là sản phẩm "Bán chạy nhất" (Ghim trang chủ)</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top mt-4">
                        <button type="button" class="btn btn-light rounded-pill px-3 text-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="submit" class="btn btn-gold text-white rounded-pill px-4">Xác nhận Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let modalInstance;

document.addEventListener("DOMContentLoaded", function() {
    modalInstance = new bootstrap.Modal(document.getElementById('productFormModal'));
});

function openAddModal() {
    document.getElementById('modalComponentTitle').innerText = "Thêm Sản Phẩm Khối Mới";
    document.getElementById('formAction').value = "add";
    document.getElementById('productId').value = "";
    document.getElementById('productOldImage').value = "";
    document.getElementById('productName').value = "";
    document.getElementById('productPrice').value = "";
    document.getElementById('productDesc').value = "";
    document.getElementById('productFeatured').checked = false;
    document.getElementById('imageHelpBlock').style.display = "none";
    modalInstance.show();
}

function openEditModal(prod) {
    document.getElementById('modalComponentTitle').innerText = "Cập Nhật Thông Tin Sản Phẩm";
    document.getElementById('formAction').value = "edit";
    document.getElementById('productId').value = prod.id;
    document.getElementById('productOldImage').value = prod.image ? prod.image : "";
    document.getElementById('productName').value = prod.name;
    document.getElementById('productCategoryId').value = prod.category_id;
    document.getElementById('productPrice').value = prod.price;
    document.getElementById('productDesc').value = prod.short_desc ? prod.short_desc : "";
    
    document.getElementById('productFeatured').checked = (prod.is_featured == 1);
    document.getElementById('imageHelpBlock').style.display = "block";
    modalInstance.show();
}
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
    transform: translateY(-1px);
}
.hover-gold:hover {
    color: #E5A93B !important;
}
</style>

<?php include_once 'includes/footer.php'; ?>