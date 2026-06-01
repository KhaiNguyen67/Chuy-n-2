<?php
// ============================================================
// File: admin-process.php
// Chức năng: Tập trung xử lý lõi dữ liệu CRUD bằng đối tượng PDO
// ============================================================

// ĐÃ SỬA: Thêm ../ để lùi ra ngoài thư mục gốc kết nối Database
include_once '../config/db.php';

// Kiểm tra quyền thực thi (Xóa bằng GET, Thêm/Sửa bằng POST)
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $action = $_POST['action'] ?? '';

    // Lấy và chuẩn hóa dữ liệu đầu vào
    $name = trim($_POST['name']);
    $category_id = (int)$_POST['category_id'];
    $price = (float)$_POST['price'];
    $short_desc = trim($_POST['short_desc']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    // --------------------------------------------------------
    // NGHIỆP VỤ 1: THÊM MỚI SẢN PHẨM
    // --------------------------------------------------------
    if ($action === 'add') {
        $image_name = 'default.jpg'; // Ảnh mặc định dự phòng

        // Tiến hành kiểm tra và xử lý tệp tin ảnh tải lên từ máy tính
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            // Tạo chuỗi tên file ngẫu nhiên dựa trên mốc thời gian để tránh trùng tên
            $image_name = 'cheese_' . time() . '.' . $ext;
            
            // ĐÃ SỬA: Thêm ../ để lưu ảnh chạy trúng vào thư mục assets/img ở ngoài gốc
            move_uploaded_file($_FILES['image']['tmp_name'], '../assets/img/' . $image_name);
        }

        $sql = "INSERT INTO products (name, category_id, price, image, short_desc, is_featured) 
                VALUES (:name, :category_id, :price, :image, :short_desc, :is_featured)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'name'        => $name,
            'category_id' => $category_id,
            'price'       => $price,
            'image'       => $image_name,
            'short_desc'  => $short_desc,
            'is_featured' => $is_featured
        ]);

        header("Location: admin-products.php?status=success_add");
        exit();
    }

    // --------------------------------------------------------
    // NGHIỆP VỤ 2: CẬP NHẬT CHỈNH SỬA SẢN PHẨM
    // --------------------------------------------------------
    if ($action === 'edit') {
        $id = (int)$_POST['id'];
        $image_name = $_POST['old_image']; // Mặc định giữ lại tên file ảnh cũ

        // Nếu người quản trị thực hiện chọn tệp ảnh mới thay thế
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_name = 'cheese_' . time() . '.' . $ext;
            
            // ĐÃ SỬA: Thêm ../ để lưu ảnh chạy trúng vào thư mục assets/img ở ngoài gốc
            move_uploaded_file($_FILES['image']['tmp_name'], '../assets/img/' . $image_name);
        }

        $sql = "UPDATE products SET 
                    name = :name, 
                    category_id = :category_id, 
                    price = :price, 
                    image = :image, 
                    short_desc = :short_desc, 
                    is_featured = :is_featured 
                WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'name'        => $name,
            'category_id' => $category_id,
            'price'       => $price,
            'image'       => $image_name,
            'short_desc'  => $short_desc,
            'is_featured' => $is_featured,
            'id'          => $id
        ]);

        header("Location: admin-products.php?status=success_edit");
        exit();
    }

} elseif ($method === 'GET') {
    $action = $_GET['action'] ?? '';

    // --------------------------------------------------------
    // NGHIỆP VỤ 3: XÓA SẢN PHẨM KHỎI DATABASE
    // --------------------------------------------------------
    if ($action === 'delete' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];

        $sql = "DELETE FROM products WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        header("Location: admin-products.php?status=success_delete");
        exit();
    }
}

// Chuyển hướng dự phòng nếu truy cập file sai mục đích
header("Location: admin-products.php");
exit();
?>