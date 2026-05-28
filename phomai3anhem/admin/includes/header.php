<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
// Bảo mật: Chặn nếu không phải là admin (Dựa trên database.sql của bạn)
// if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') { header('Location: ../index.php'); exit(); }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hệ thống Quản trị - Phô Mai 3 Anh Em</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --sidebar-width: 260px; --gold-color: #cca43b; }
        body { background-color: #f8f9fa; font-family: 'Segoe UI', system-ui, sans-serif; }
        .sidebar { width: var(--sidebar-width); height: 100vh; position: fixed; top: 0; left: 0; background: #1e1e2d; z-index: 1000; transition: all 0.3s; }
        .sidebar .nav-link { color: #a2a3b7; padding: 12px 25px; display: flex; align-items: center; gap: 12px; transition: all 0.2s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background: #1b1b28; border-left: 4px solid var(--gold-color); }
        .main-content { margin-left: var(--sidebar-width); padding: 30px; min-height: 100vh; }
        .admin-card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="sidebar d-flex flex-column pt-3">
    <div class="text-center mb-4 px-3">
        <h5 class="text-white fw-bold text-uppercase m-0" style="letter-spacing: 1px;"> 3 Anh Em Admin</h5>
        <small class="text-muted">Hệ thống quản lý cửa hàng</small>
    </div>
    <hr class="border-secondary opacity-25 mx-3">
    <ul class="nav flex-column flex-grow-1">
        <li class="nav-item"><a href="index.php" class="nav-link"><i class="bi bi-speedometer2"></i> Trang tổng quan</a></li>
        <li class="nav-item"><a href="admin-products.php" class="nav-link"><i class="bi bi-journal-text"></i> Quản Lý Sản Phẩm</a></li>
        <li class="nav-item"><a href="admin-categories.php" class="nav-link"><i class="bi bi-tags"></i> Quản lý Danh mục</a></li>
        <li class="nav-item"><a href="orders.php" class="nav-link"><i class="bi bi-receipt"></i> Quản lý Đơn hàng</a></li>
        <li class="nav-item"><a href="admin-users.php" class="nav-link"><i class="bi bi-people"></i> Quản lý Người dùng</a></li>
        <li class="nav-item"><a href="admin-posts.php" class="nav-link"><i class="bi bi-journal-text"></i> Quản lý Bài viết</a></li>
    </ul>
    <div class="p-3 mt-auto">
        <a href="../index.php" class="btn btn-sm btn-outline-secondary w-100 mb-2"><i class="bi bi-arrow-left"></i> Xem Website</a>
        <a href="../logout.php" class="btn btn-sm btn-danger w-100"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a>
    </div>
</div>

<div class="main-content">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>