<?php
// ============================================================
// File: includes/header.php
// Chức năng: Khởi tạo Session, thiết kế Header & CSS Glassmorphism
// ============================================================
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Tính tổng số lượng item thực tế trong giỏ hàng để hiển thị chính xác lên icon
$total_cart_items = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        // Cộng dồn thuộc tính quantity nếu giỏ hàng lưu dạng mảng đa chiều
        $total_cart_items += isset($item['quantity']) ? $item['quantity'] : 1;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phô Mai 3 Anh Em - Trải Nghiệm Ẩm Thực Cao Cấp</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-gold: #E5A93B;    /* Vàng mật ong ấm áp làm điểm nhấn */
            --bg-cream: #FDFBF7;        /* Nền trắng kem tối giản dịu mắt */
            --text-charcoal: #2C2C2C;   /* Chữ đen charcoal sang trọng */
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.4);
            --glass-blur: blur(12px);
        }

        body {
            background-color: var(--bg-cream);
            color: var(--text-charcoal);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        /* Thanh điều hướng cố định phong cách Kính Mờ */
        .glass-nav {
            background: var(--glass-bg);
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            border-bottom: 1px solid var(--glass-border);
        }

        /* Khung thông tin Glassmorphism tinh tế */
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.04);
        }

        /* Nút bấm Custom màu vàng mật ong */
        .btn-gold {
            background-color: var(--primary-gold);
            color: #ffffff;
            border-radius: 30px;
            padding: 10px 24px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-gold:hover {
            background-color: #C98F2A;
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(229, 169, 59, 0.35);
        }

        /* Hiệu ứng tương tác Nổi bật / 3D nhẹ khi di chuột vào thẻ sản phẩm */
        .product-card-hover {
            transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.4s ease;
        }
        
        .product-card-hover:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        }

        .hover-gold:hover {
            color: var(--primary-gold) !important;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top glass-nav py-3">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="index.php">
            🧀 <span style="color: var(--primary-gold);">Phô Mai</span> 3 Anh Em
        </a>
        
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-semibold">
                <li class="nav-item px-2"><a class="nav-link text-dark hover-gold" href="index.php">Trang Chủ</a></li>
                <li class="nav-item px-2"><a class="nav-link text-dark hover-gold" href="product.php">Sản Phẩm</a></li>
                <li class="nav-item px-2"><a class="nav-link text-dark hover-gold" href="index.php#about">Câu Chuyện</a></li>
                <li class="nav-item px-2"><a class="nav-link text-dark hover-gold" href="Blog.php">Blog</a></li>
                
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1): ?>
                    <li class="nav-item px-2">
                        <a class="nav-link text-danger fw-bold hover-gold" href="admin/dashboard.php">
                            <i class="bi bi-speedometer2 me-1"></i> Quản Trị
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            
            <div class="d-flex align-items-center gap-3">
                <a href="cart.php" class="position-relative text-dark fs-5 me-2 text-decoration-none">
                    <i class="bi bi-bag-heart"></i>
                    <span class="cart-badge position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger <?php echo $total_cart_items == 0 ? 'd-none' : ''; ?>" style="font-size: 0.7rem;">
                        <?php echo $total_cart_items; ?>
                    </span>
                </a>
                
                <?php if(isset($_SESSION['user_name'])): ?>
                    <span class="me-2 small fw-bold">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    <a href="change-password.php" class="btn btn-sm btn-outline-secondary rounded-pill me-1" title="Đổi mật khẩu"><i class="bi bi-key"></i></a>
                    <a href="logout.php" class="btn btn-sm btn-outline-danger rounded-pill" title="Đăng xuất"><i class="bi bi-box-arrow-right"></i></a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-sm btn-outline-dark rounded-pill px-3">Đăng Nhập</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<main style="min-height: 75vh;">