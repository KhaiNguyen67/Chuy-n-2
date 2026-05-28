<?php
// ============================================================
// File: admin-users.php
// Chức năng: Giao diện danh sách & quản lý thành viên (PDO)
// ============================================================
include_once 'admin-check.php'; // Chặn tài khoản thường truy cập
include_once '../config/db.php';
include_once 'includes/header.php'; // Navbar quản trị chung

// Lấy danh sách toàn bộ người dùng trong hệ thống (Mới nhất xếp lên đầu)
$stmt = $pdo->query("SELECT id, full_name, email, phone, address, role, created_at FROM users ORDER BY id DESC");
$users = $stmt->fetchAll();
?>

<div class="container my-5 pt-4">
    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] == 'success_edit'): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-3 small" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>Cập nhật thông tin và phân quyền thành viên thành công!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($_GET['status'] == 'success_delete'): ?>
            <div class="alert alert-warning alert-dismissible fade show rounded-3 small" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>Đã xóa tài khoản người dùng khỏi hệ thống!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($_GET['status'] == 'error_self_delete'): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-3 small" role="alert">
                <i class="bi bi-x-circle-fill me-2"></i>Không thể thực thi! Bạn không được phép tự xóa tài khoản chính mình khi đang đăng nhập.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="mb-4">
        <h3 class="fw-bold text-dark mb-1">Quản Lý Người Dùng</h3>
        <span class="badge bg-dark-subtle text-dark rounded-pill px-3 py-1.5 small text-uppercase">Danh sách thành viên hệ thống</span>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.5px;">
                    <tr>
                        <th class="border-0 ps-4">Họ và Tên / Email</th>
                        <th class="border-0">Số điện thoại</th>
                        <th class="border-0">Địa chỉ giao hàng</th>
                        <th class="border-0 text-center">Vai Trò</th>
                        <th class="border-0 text-center" style="width: 140px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark mb-0">
                                        <?= htmlspecialchars($u['full_name']); ?>
                                        <?php if($u['id'] == $_SESSION['user_id']): ?>
                                            <span class="badge bg-primary rounded-pill small ms-1" style="font-size: 10px;">Bạn</span>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted text-lowercase"><?= htmlspecialchars($u['email']); ?></small>
                                </td>
                                <td>
                                    <span class="text-dark small fw-semibold"><?= !empty($u['phone']) ? htmlspecialchars($u['phone']) : '---'; ?></span>
                                </td>
                                <td>
                                    <small class="text-muted d-block text-truncate" style="max-width: 250px;" title="<?= htmlspecialchars($u['address']); ?>">
                                        <?= !empty($u['address']) ? htmlspecialchars($u['address']) : 'Chưa cập nhật địa chỉ...'; ?>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <?php if (strtolower($u['role']) === 'admin'): ?>
                                        <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1.5 small fw-bold text-uppercase">Quản Trị Viên</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1.5 small text-uppercase">Khách Hàng</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group gap-1"> 
                                        <?php if($u['id'] != $_SESSION['user_id']): ?>
                                            <a href="admin-user-process.php?action=delete&id=<?= $u['id']; ?>" 
                                               class="btn btn-sm btn-outline-danger rounded-3 border-0 px-2" 
                                               onclick="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn tài khoản thành viên này? Người dùng sẽ không thể đăng nhập được nữa.')" title="Xóa tài khoản">
                                                <i class="bi bi-trash3 fs-5"></i>
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-outline-light rounded-3 border-0 px-2 text-muted" disabled title="Không thể xóa chính mình">
                                                <i class="bi bi-trash3 fs-5 text-black-30"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted small">Hệ thống chưa có tài khoản thành viên nào đăng ký.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="userFormModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-light border-0 py-3 px-4">
                <h5 class="modal-title fw-bold text-dark">Cập Nhật Tài Khoản Thành Viên</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="admin-user-process.php" method="POST">
                    <input type="hidden" name="id" id="userId">

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Họ và tên thành viên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-3" name="full_name" id="userFullName" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Địa chỉ Email (Tên đăng nhập)</label>
                        <input type="email" class="form-control rounded-3 bg-light text-muted" id="userEmail" readonly disabled title="Email cố định không được sửa">
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Số điện thoại</label>
                            <input type="text" class="form-control rounded-3" name="phone" id="userPhone">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Phân quyền hệ thống <span class="text-danger">*</span></label>
                            <select class="form-select rounded-3 fw-bold" name="role" id="userRole" required>
                                <option value="customer">Khách Hàng (customer)</option>
                                <option value="admin">Quản Trị Viên (admin)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Địa chỉ nhận hàng mặc định</label>
                        <textarea class="form-control rounded-3" name="address" id="userAddress" rows="2"></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top mt-4">
                        <button type="button" class="btn btn-light rounded-pill px-3 text-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="submit" class="btn btn-gold text-white rounded-pill px-4">Xác nhận cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let userModal;

document.addEventListener("DOMContentLoaded", function() {
    userModal = new bootstrap.Modal(document.getElementById('userFormModal'));
});

function openEditUserModal(user) {
    document.getElementById('userId').value = user.id;
    document.getElementById('userFullName').value = user.full_name;
    document.getElementById('userEmail').value = user.email;
    document.getElementById('userPhone').value = user.phone ? user.phone : "";
    document.getElementById('userRole').value = user.role.toLowerCase(); // Chuyển chữ thường để khớp option
    document.getElementById('userAddress').value = user.address ? user.address : "";
    
    userModal.show();
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
</style>

<?php?>