<?php
/**
 * ============================================================
 *  FILE: config/db.php
 *  MỤC ĐÍCH: Kết nối cơ sở dữ liệu MySQL bằng PDO
 *  PHÔ MAI 3 ANH EM
 * ============================================================
 *
 *  Tại sao dùng PDO thay vì mysqli?
 *  - Hỗ trợ nhiều loại DB (MySQL, SQLite, PostgreSQL...)
 *  - Prepared statements phòng chống SQL Injection tốt hơn
 *  - Xử lý lỗi qua Exception — dễ debug hơn
 * ============================================================
 */

// ── Thông tin kết nối ─────────────────────────────────────────
// THAY ĐỔI CÁC GIÁ TRỊ NÀY theo môi trường của bạn
define('DB_HOST',    'localhost');   // Máy chủ MySQL
define('DB_PORT',    '2333');        // Cổng mặc định MySQL
define('DB_NAME',    'phomai3anhem'); // Tên database
define('DB_USER',    'root');        // Username MySQL
define('DB_PASS',    '');            // Password MySQL (đổi lại khi deploy)
define('DB_CHARSET', 'utf8mb4');     // Bộ mã ký tự (hỗ trợ tiếng Việt + emoji)

// ── DSN (Data Source Name) ────────────────────────────────────
$dsn = sprintf(
    'mysql:host=%s;port=%s;dbname=%s;charset=%s',
    DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
);

// ── Tùy chọn PDO ─────────────────────────────────────────────
$pdoOptions = [
    // Ném Exception khi có lỗi SQL (thay vì trả về false âm thầm)
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,

    // Trả kết quả query dưới dạng mảng kết hợp (associative array)
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

    // Tắt emulate prepared statements — dùng prepared statements thật của MySQL
    // → An toàn hơn, hiệu năng tốt hơn với MySQL 5.1+
    PDO::ATTR_EMULATE_PREPARES   => false,

    // Persistent connection — tái sử dụng kết nối (giảm overhead mở kết nối mới)
    // Bật khi traffic cao; tắt nếu gặp vấn đề transaction
    // PDO::ATTR_PERSISTENT => true,
];

// ── Khởi tạo kết nối ─────────────────────────────────────────
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $pdoOptions);

} catch (PDOException $e) {
    /**
     * XỬ LÝ LỖI KẾT NỐI:
     * - Ghi lỗi vào log (không lộ thông tin nhạy cảm ra ngoài)
     * - Hiển thị trang lỗi thân thiện với người dùng
     *
     * QUAN TRỌNG: Trong môi trường production, KHÔNG bao giờ
     * echo ra $e->getMessage() vì có thể lộ cấu trúc DB/password!
     */

    // Ghi log lỗi (PHP sẽ ghi vào error_log mặc định)
    error_log('[DB ERROR] ' . $e->getMessage());

    // Hiển thị thông báo lỗi thân thiện
    http_response_code(503);
    die(<<<HTML
        <!DOCTYPE html>
        <html lang="vi">
        <head>
            <meta charset="UTF-8">
            <title>Lỗi Hệ Thống — Phô Mai 3 Anh Em</title>
            <style>
                body { font-family: sans-serif; display:flex; justify-content:center;
                       align-items:center; height:100vh; background:#fffbf0; margin:0; }
                .box { text-align:center; padding:40px; border-radius:12px;
                       background:#fff; box-shadow:0 4px 20px rgba(0,0,0,.08); }
                h2  { color:#c0392b; }
                p   { color:#666; }
            </style>
        </head>
        <body>
            <div class="box">
                <h2>⚠️ Không thể kết nối cơ sở dữ liệu</h2>
                <p>Chúng tôi đang gặp sự cố kỹ thuật.<br>
                   Vui lòng thử lại sau hoặc liên hệ admin.</p>
            </div>
        </body>
        </html>
    HTML);
}

// ── Hàm helper: Lấy instance PDO (dùng trong các file khác) ──
/**
 * Trả về đối tượng $pdo toàn cục.
 * Sử dụng: $db = getDB();
 *
 * @return PDO
 */
function getDB(): PDO
{
    global $pdo;
    return $pdo;
}
