<?php 
include_once '../config/db.php'; 

// Xử lý xóa sản phẩm nhận lệnh từ phương thức GET
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $del_id = (int)$GET['id'];
    $pdo->prepare("UPDATE products SET is_active = 0 WHERE id = ?")->execute([$del_id]);
    header("Location: products.php");
    exit();
}

include_once 'includes/header.php'; 
$prods = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.is_active = 1 ORDER BY p.id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0 text-dark"><i class="bi bi-box-seam me-2"></i>Quản Lý Kho Hàng Phô Mai</h4>
    <button class="btn btn-gold text-white btn-sm px-3 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#modalAddProduct">
        <i class="bi bi-plus-circle me-1"></i> Thêm sản phẩm mới
    </button>
</div>

<div class="card admin-card bg-white p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle text-center m-0">
            <thead class="table-light">
                <tr>
                    <th>Mã sản phẩm</th>
                    <th>Hình ảnh</th>
                    <th class="text-start">Tên phô mai</th>
                    <th>Phân loại</th>
                    <th>Giá bán</th>
                    <th>Tồn kho</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($prods as $p): ?>
                <tr>
                    <td><strong>#<?php echo $p['id']; ?></strong></td>
                    <td>
                        <img src="../assets/img/<?php echo $p['image']; ?>" class="rounded-2 border bg-light" style="width: 50px; height: 50px; object-fit: contain;" onerror="this.src='https://images.unsplash.com/photo-1528750994863-30f4a7c05267?q=80&w=100'">
                    </td>
                    <td class="text-start fw-bold"><?php echo htmlspecialchars($p['name']); ?></td>
                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($p['category_name']); ?></span></td>
                    <td class="text-danger fw-bold"><?php echo number_format($p['price'], 0, ',', '.'); ?>đ</td>
                    <td>
                        <span class="badge <?php echo $p['stock'] > 10 ? 'bg-success' : 'bg-danger'; ?>">
                            <?php echo $p['stock']; ?> chiếc
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></button>
                        <a href="products.php?action=delete&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xác nhận dừng bán sản phẩm này?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalAddProduct" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form class="modal-content" method="POST" action="process_product.php" enctype="multipart/form-data">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Thêm Mới Phô Mai Vào Hệ Thống</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Tên sản phẩm *</label>
                    <input type="text" name="name" class="form-control" required placeholder="Ví dụ: Gouda Aged">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Danh mục phân loại *</label>
                    <select name="category_id" class="form-select">
                        <?php $cates = $pdo->query("SELECT * FROM categories")->fetchAll(); foreach($cates as $c): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Giá bán hiện tại (đ) *</label>
                    <input type="number" name="price" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Trọng lượng (gram) *</label>
                    <input type="number" name="weight_gram" class="form-control" value="200" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Số lượng nhập kho *</label>
                    <input type="number" name="stock" class="form-control" value="50" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label small fw-bold">Hình ảnh đại diện sản phẩm</label>
                    <input type="file" name="image" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
                <button type="submit" name="btn_add_product" class="btn btn-gold btn-sm text-white">Lưu dữ liệu</button>
            </div>
        </form>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>