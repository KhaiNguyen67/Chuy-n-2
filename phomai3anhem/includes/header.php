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
    
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/aos.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-gold: #E5A93B;    /* Vàng mật ong ấm áp làm điểm nhấn */
            --bg-cream: #FDFBF7;        /* Nền trắng kem tối giản dịu mắt */
            --text-charcoal: #2C2C2C;   /* Chữ đen charcoal sang trọng */
            --glass-bg: rgba(255, 255, 255, 0.8);
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

        /* Khối hình chữ nhật bo tròn cho Menu */
        .glass-nav .nav-link {
            padding: 8px 16px !important; 
            border-radius: 50px;          
            transition: all 0.3s ease-in-out; 
        }

        /* Hiệu ứng khi di chuột vào (Hover) các thẻ a trên thanh điều hướng */
        .glass-nav .nav-link:hover {
            background-color: var(--primary-gold); 
            color: #ffffff !important;             
            box-shadow: 0 4px 10px rgba(229, 169, 59, 0.25); 
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

        /* ── CSS RESPONSIVE TÙY BIẾN MỚI CHO DI ĐỘNG & MÁY TÍNH BẢNG ── */
        .navbar-brand-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            transition: all 0.3s ease;
        }

        /* Khử đường viền xanh mặc định khi chạm nút hamburger trên điện thoại */
        .navbar-toggler:focus {
            box-shadow: none;
        }

        /* Responsive dựa trên Breakpoints chuẩn */
        @media (max-width: 991.98px) {
            /* Menu xổ xuống dạng dọc phủ kính mờ đồng bộ */
            .navbar-collapse {
                background: rgba(253, 251, 247, 0.95);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                border-radius: 16px;
                padding: 15px;
                margin-top: 10px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
                border: 1px solid rgba(237, 230, 214, 0.5);
            }
            .glass-nav .nav-link {
                border-radius: 8px; /* Giảm bo tròn khi xếp dọc nhìn sẽ tự nhiên hơn */
                margin-bottom: 4px;
                padding: 10px 16px !important;
            }
            .navbar-brand-img {
                width: 55px;
                height: 55px;
            }
            .brand-text {
                font-size: 1.25rem !important;
            }
        }

        @media (min-width: 992px) {
            /* Trên màn hình PC lớn, trả lại kích thước logo thương hiệu rộng rãi */
            .navbar-brand-img {
                width: 80px;
                height: 80px;
            }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top glass-nav py-2">
    <div class="container">
        <!-- Thương hiệu / Logo: Tối ưu co giãn text và hình ảnh -->
        <a class="navbar-brand fw-bold fs-3 d-flex align-items-center text-decoration-none text-dark" href="index.php">
            <img src="assets/img/logo.jpg" alt="Logo Phô Mai 3 Anh Em" class="rounded-circle me-2 navbar-brand-img">
            <span class="brand-text">
                <span style="color: var(--primary-gold);">Phô Mai</span> 3 Anh Em
            </span>
        </a>
        
        <!-- Tổ hợp Tiện ích: Đưa Giỏ hàng ra ngoài đứng cạnh Hamburger khi thu nhỏ -->
        <div class="d-flex align-items-center gap-3 order-lg-last ms-auto me-3 me-lg-0">
            <a href="cart.php" class="position-relative text-dark fs-5 text-decoration-none me-1 hover-gold">
                <i class="bi bi-bag-heart"></i>
                <span class="cart-badge position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger <?php echo $total_cart_items == 0 ? 'd-none' : ''; ?>" style="font-size: 0.65rem;">
                    <?php echo $total_cart_items; ?>
                </span>
            </a>
            
            <!-- Khu vực User: Ẩn bớt text, chỉ giữ nút chức năng icon gọn gàng trên Mobile -->
            <?php if(isset($_SESSION['user_name'])): ?>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <a href="admin/index.php" class="btn btn-sm btn-gold rounded-pill px-2.5 px-sm-3" title="Quản trị">
                        <i class="bi bi-speedometer2"></i> <span class="d-none d-sm-inline ms-1">Quản Trị</span>
                    </a>
                <?php endif; ?>

                <span class="small fw-bold d-none d-md-inline text-secondary">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="change-password.php" class="btn btn-sm btn-outline-secondary rounded-pill" title="Đổi mật khẩu"><i class="bi bi-key"></i></a>
                <a href="logout.php" class="btn btn-sm btn-outline-danger rounded-pill" title="Đăng xuất"><i class="bi bi-box-arrow-right"></i></a>
            <?php else: ?>
                <a href="login.php" class="btn btn-sm btn-outline-dark rounded-pill px-3">Đăng Nhập</a>
            <?php endif; ?>
        </div>

        <!-- Nút Hamburger nguyên bản -->
        <button class="navbar-toggler border-0 p-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Danh sách các danh mục điều hướng -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-semibold text-center text-lg-start">
                <li class="nav-item px-1"><a class="nav-link text-dark" href="index.php">Trang Chủ</a></li>
                <li class="nav-item px-1"><a class="nav-link text-dark" href="product.php">Sản Phẩm</a></li>
                <li class="nav-item px-1"><a class="nav-link text-dark" href="index.php#about">Câu Chuyện</a></li>
                <li class="nav-item px-1"><a class="nav-link text-dark" href="Blog.php">Blog</a></li>
                <li class="nav-item px-1"><a class="nav-link text-dark" href="contact.php">Liên hệ</a></li>
                <li class="nav-item px-1"><a class="nav-link text-dark" href="order-history.php">Lịch sử đơn hàng</a></li>
            </ul>
        </div>
    </div>
</nav>

<main style="min-height: 75vh;">