<?php
// ============================================================
// File: save-order.php
// Chức năng: Tiếp nhận dữ liệu AJAX từ checkout.php và lưu vào CSDL
// ============================================================
session_start();
include_once 'config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nhận dữ liệu thô từ Fetch API gửi lên
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        echo json_encode(['status' => 'error', 'message' => 'Không nhận được dữ liệu!']);
        exit;
    }

    // Lấy thông tin người nhận hàng để lưu vào đơn hàng theo cấu trúc CSDL của bạn
    $full_name = htmlspecialchars($input['receiver_name']);
    $phone = htmlspecialchars($input['receiver_phone']);
    $address = htmlspecialchars($input['receiver_address']);
    $notes = htmlspecialchars($input['order_notes']);
    
    // Tính toán tổng tiền từ Giỏ hàng trong Session để đảm bảo an toàn bảo mật (không lấy tiền từ Client gửi lên)
    $total_money = 0;
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total_money += $item['price'] * $item['quantity'];
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Giỏ hàng trống rỗng!']);
        exit;
    }

    try {
        // Chèn dữ liệu vào bảng orders (Mặc định trạng thái mới là 'pending')
        $sql = "INSERT INTO orders (full_name, phone, address, notes, total_money, status, created_at) 
                VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$full_name, $phone, $address, $notes, $total_money]);
        
        // Lấy ra ID của đơn hàng vừa tạo tự động
        $order_id = $pdo->lastInsertId();

        // [TÙY CHỌN BỔ SUNG]: Nếu bạn có bảng chi tiết đơn hàng (order_details), bạn có thể chèn vòng lặp giỏ hàng vào đây
        /*
        foreach ($_SESSION['cart'] as $item) {
            $pdo->prepare("INSERT INTO order_details (order_id, product_id, price, quantity) VALUES (?, ?, ?, ?)")
                ->execute([$order_id, $item['id'], $item['price'], $item['quantity']]);
        }
        */

        // Xóa giỏ hàng sau khi đã lưu đơn thành công
        unset($_SESSION['cart']);

        // Trả về kết quả thành công kèm mã đơn hàng thực tế cho giao diện Client
        echo json_encode([
            'status' => 'success',
            'order_id' => $order_id
        ]);
        exit;

    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Phương thức không hợp lệ!']);
    exit;
}