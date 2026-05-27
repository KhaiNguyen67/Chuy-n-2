-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:2333
-- Generation Time: May 27, 2026 at 10:59 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `phomai3anhem`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `created_at`) VALUES
(1, 'Phô Mai Cứng', 'pho-mai-cung', 'Phô mai được ủ lâu năm, kết cấu chắc, hương vị đậm đà và phức tạp.', 'bi-gem', '2026-05-25 15:19:14'),
(2, 'Phô Mai Tươi', 'pho-mai-tuoi', 'Phô mai chưa qua ủ, kết cấu mềm mịn, hương vị thanh nhẹ, béo nhẹ.', 'bi-droplet', '2026-05-25 15:19:14'),
(3, 'Phô Mai Xanh', 'pho-mai-xanh', 'Phô mai có vân xanh từ nấm mốc Penicillium, hương vị nồng nàn độc đáo.', 'bi-wind', '2026-05-25 15:19:14'),
(4, 'Phô Mai Nửa Cứng', 'pho-mai-nua-cung', 'Phô mai ủ vừa, kết cấu dẻo, cân bằng giữa mềm và cứng.', 'bi-layers', '2026-05-25 15:19:14');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `order_code` varchar(20) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(150) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `shipping_address` text NOT NULL,
  `total_amount` decimal(14,0) NOT NULL,
  `shipping_fee` decimal(10,0) NOT NULL DEFAULT 0,
  `grand_total` decimal(14,0) NOT NULL,
  `payment_method` enum('cod','bank_transfer','momo') NOT NULL DEFAULT 'cod',
  `payment_status` enum('pending','paid','failed') NOT NULL DEFAULT 'pending',
  `order_status` enum('pending','confirmed','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `note` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `product_img` varchar(255) DEFAULT NULL,
  `unit_price` decimal(12,0) NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `subtotal` decimal(14,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `description` text DEFAULT NULL,
  `short_desc` varchar(300) DEFAULT NULL,
  `price` decimal(12,0) NOT NULL,
  `original_price` decimal(12,0) DEFAULT NULL,
  `stock` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `weight_gram` int(10) UNSIGNED DEFAULT NULL,
  `origin` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `short_desc`, `price`, `original_price`, `stock`, `weight_gram`, `origin`, `image`, `is_featured`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Parmesan Reggiano 24 Tháng', 'parmesan-reggiano-24-thang', 'Parmigiano-Reggiano là \"vua của các loại phô mai\" đến từ vùng Emilia-Romagna, Ý. Được ủ tối thiểu 24 tháng trong hầm đá truyền thống, phô mai có kết cấu hạt mịn đặc trưng, tan chảy trong miệng với hậu vị ngọt nhẹ, bơ béo và nấm umami sâu. Đây là loại phô mai bắt buộc phải có trong bếp của mọi người yêu ẩm thực.', 'Vua phô mai Ý, ủ 24 tháng — kết cấu hạt, hương vị umami sâu.', 685000, 750000, 50, 200, 'Ý (Emilia-Romagna)', 'parmesan-reggiano.jpg', 1, 1, '2026-05-25 15:19:14', '2026-05-25 15:19:14'),
(2, 1, 'Cheddar Vintage 18 Tháng', 'cheddar-vintage-18-thang', 'Cheddar Vintage từ vùng Somerset, Anh — ủ trong 18 tháng để đạt được vị chua nhẹ đặc trưng, hương bơ đậm, kết cấu cứng dẻo với những tinh thể muối nhỏ li ti. Tuyệt vời khi dùng kèm táo xanh, hạt óc chó hoặc nhai cùng bánh mì nướng.', 'Cheddar Anh ủ 18 tháng — béo ngậy, tinh thể muối, vị chua dịu.', 420000, 480000, 80, 250, 'Anh (Somerset)', 'cheddar-vintage.jpg', 1, 1, '2026-05-25 15:19:14', '2026-05-25 15:19:14'),
(3, 1, 'Gruyère AOP Thụy Sĩ', 'gruyere-aop-thuy-si', 'Gruyère AOP là phô mai Thụy Sĩ chính gốc được chứng nhận bảo hộ địa lý. Ủ trong hầm đá 12–18 tháng, phô mai có kết cấu đặc, không có lỗ hổng, hương vị phức hợp gồm bơ, hạt, và một chút trái cây ngọt. Đây là nguyên liệu quan trọng trong món Fondue và French Onion Soup.', 'Phô mai Thụy Sĩ AOP — kết cấu đặc, hương bơ và hạt, hoàn hảo cho Fondue.', 520000, NULL, 60, 200, 'Thụy Sĩ (Fribourg)', 'gruyere-aop.jpg', 0, 1, '2026-05-25 15:19:14', '2026-05-25 15:19:14'),
(4, 1, 'Manchego Curado 6 Tháng', 'manchego-curado-6-thang', 'Manchego Curado được làm từ sữa cừu Manchega thuần chủng ở vùng La Mancha, Tây Ban Nha. Ủ trong 6 tháng với lớp vỏ đặc trưng hình zigzag, phô mai có kết cấu cứng vừa, hương vị đậm đà, béo, với nốt thảo mộc và caramel nhẹ. Hoàn hảo với quince paste và rượu vang đỏ.', 'Phô mai cừu Tây Ban Nha — kết cấu cứng, hương caramel và thảo mộc.', 395000, 430000, 45, 200, 'Tây Ban Nha (La Mancha)', 'manchego-curado.jpg', 0, 1, '2026-05-25 15:19:14', '2026-05-25 15:19:14'),
(5, 2, 'Mozzarella di Bufala Campana', 'mozzarella-di-bufala-campana', 'Mozzarella di Bufala Campana DOP — làm từ sữa trâu nước 100% tại vùng Campania, Ý. Kết cấu mềm dẻo co giãn đặc trưng, vị sữa tươi béo ngậy, khi cắt ra có nước whey trong chảy ra. Dùng ăn sống với cà chua, dầu olive và húng quế — hoàn hảo tuyệt đối cho món Caprese.', 'Mozzarella trâu Ý DOP — mềm dẻo, sữa tươi, hoàn hảo cho Caprese.', 280000, NULL, 100, 125, 'Ý (Campania)', 'mozzarella-bufala.jpg', 1, 1, '2026-05-25 15:19:14', '2026-05-25 15:19:14'),
(6, 2, 'Burrata Fresca Puglia', 'burrata-fresca-puglia', 'Burrata là \"túi\" Mozzarella bao bọc bên ngoài, bên trong chứa hỗn hợp kem tươi và sợi Stracciatella. Cắt ra, kem béo ngậy chảy ra mềm mại — đây là trải nghiệm phô mai xa hoa nhất. Nên ăn trong vòng 48 giờ sau sản xuất để cảm nhận độ tươi tuyệt hảo.', 'Burrata Puglia — vỏ Mozzarella, nhân kem chảy, xa hoa và tươi ngon.', 320000, NULL, 60, 150, 'Ý (Puglia)', 'burrata-puglia.jpg', 1, 1, '2026-05-25 15:19:14', '2026-05-25 15:19:14'),
(7, 2, 'Ricotta Fresca Italiana', 'ricotta-fresca-italiana', 'Ricotta (nghĩa đen: \"nấu lại\") được làm từ whey còn sót lại sau khi sản xuất các phô mai khác. Kết cấu xốp nhẹ, kem trắng tinh, vị ngọt thanh, ít mặn. Đa năng trong bếp: dùng làm nhân ravioli, bánh cheesecake, hoặc phết lên bánh mì nướng cùng mật ong.', 'Ricotta Ý tươi — xốp nhẹ, thanh ngọt, đa năng trong nấu ăn.', 180000, NULL, 70, 250, 'Ý', 'ricotta-fresca.jpg', 0, 1, '2026-05-25 15:19:14', '2026-05-25 15:19:14'),
(8, 2, 'Cream Cheese Philadelphia Style', 'cream-cheese-philadelphia-style', 'Cream Cheese mịn màng, béo ngậy — được làm theo phong cách Philadelphia với hàm lượng kem cao. Kết cấu phết dễ dàng ngay khi lấy ra từ tủ lạnh. Hoàn hảo cho cheesecake, bagel hoặc làm frosting cho bánh.', 'Cream Cheese kem cao cấp — mịn màng, béo nhẹ, hoàn hảo cho cheesecake.', 145000, 160000, 120, 200, 'Nhập khẩu (Đan Mạch)', 'cream-cheese.jpg', 0, 1, '2026-05-25 15:19:14', '2026-05-25 15:19:14'),
(9, 3, 'Gorgonzola Piccante DOP', 'gorgonzola-piccante-dop', 'Gorgonzola Piccante là phô mai xanh nổi tiếng nhất nước Ý, có chứng nhận DOP. Ủ trong 6–12 tháng, vân xanh lam chạy dọc khắp phô mai từ nấm Penicillium glaucum. Hương vị mạnh mẽ, cay nồng, mặn đậm với hậu vị peppery. Hoàn hảo với mật ong hoa cam, quả lê và Prosecco.', 'Gorgonzola Ý DOP — vân xanh, hương nồng, hoàn hảo với mật ong.', 490000, 540000, 40, 200, 'Ý (Lombardia/Piemonte)', 'gorgonzola-piccante.jpg', 1, 1, '2026-05-25 15:19:14', '2026-05-25 15:19:14'),
(10, 3, 'Roquefort AOP Pháp', 'roquefort-aop-phap', 'Roquefort — \"vua của các loại phô mai xanh\" — được làm từ sữa cừu và ủ trong hang đá vôi tự nhiên ở Combalou, miền Nam nước Pháp. Phô mai có màu ngà trắng với vân xanh đặc trưng, kết cấu mềm ẩm, hương vị cay, mặn, nồng và phức hợp tuyệt vời.', 'Vua phô mai xanh Pháp AOP — sữa cừu, hang đá, hương vị phức hợp.', 580000, NULL, 30, 150, 'Pháp (Aveyron)', 'roquefort-aop.jpg', 1, 1, '2026-05-25 15:19:14', '2026-05-25 15:19:14'),
(11, 3, 'Stilton Blue Cheese PDO', 'stilton-blue-cheese-pdo', '-Đặc điểm hương vị và kết cấu:\r\n+Đậm đà, béo ngậy vị kem, xen lẫn vị mặn, hương hạt dẻ tinh tế và một chút cay nồng đặc trưng từ nấm mốc Penicillium roqueforti.\r\n+Kết cấu: Mềm mịn như kem nhưng lại dễ vụn, tạo cảm giác tan ngay trong miệng.\r\n+Thời gian ủ: Thường được ủ chín tối thiểu từ 9 tuần trở lên để đạt được hương vị chuẩn mực. \r\n\r\n-Cách thưởng thức phổ biến:\r\n+Ăn trực tiếp: Kết hợp hoàn hảo với các loại trái cây ngọt (lê, táo, sung ngọt), mật ong và các loại hạt như hạt óc chó trên một khay phô mai (cheeseboard).\r\n+Kết hợp đồ uống: Thường được nhâm nhi cùng rượu Port (rượu vang cường hóa) hoặc rượu vang đỏ đậm đà.\r\n+Chế biến món ăn: Bóp vụn để trộn salad, làm nước xốt cho các món bít tết, hoặc thêm vào mì Ý (pasta) và cơm Risotto để tăng độ béo\r\n', 'Stilton là phô mai xanh danh tiếng của Anh, được bảo hộ địa lý PDO — chỉ được sản xuất tại 3 hạt Derbyshire, Leicestershire và Nottinghamshire. \r\n', 510000, 560000, 25, 200, 'Anh (Leicestershire)', 'stilton-pdo.jpg', 0, 1, '2026-05-25 15:19:14', '2026-05-27 08:46:43'),
(12, 4, 'Gouda Aged 12 Tháng', 'gouda-aged-12-thang', 'Gouda Aged từ Hà Lan — ủ 12 tháng để đạt màu vàng caramel đặc trưng bên trong, kết cấu dẻo nhưng có tinh thể muối nhỏ. Hương vị ngọt, caramel, bơ và hạt phức hợp. Đây là phiên bản cao cấp hơn nhiều so với Gouda thông thường bán ở siêu thị.', 'Gouda Hà Lan ủ 12 tháng — caramel ngọt, tinh thể muối, dẻo thơm.', 360000, 400000, 75, 250, 'Hà Lan', 'gouda-aged.jpg', 1, 1, '2026-05-25 15:19:14', '2026-05-25 15:19:14'),
(13, 4, 'Emmental Grand Cru', 'emmental-grand-cru', 'Emmental Grand Cru là phô mai Thụy Sĩ nổi tiếng với những lỗ hổng tròn lớn đặc trưng (do khí CO₂ từ vi khuẩn tạo ra trong quá trình ủ). Hương vị nhẹ nhàng, ngọt, bơ và hạt. Tan chảy tuyệt hảo — là nguyên liệu cổ điển cho sandwich nóng, Croque Monsieur và Fondue.', 'Emmental Thụy Sĩ Grand Cru — lỗ tròn đặc trưng, ngọt nhẹ, tan chảy tuyệt hảo.', 340000, NULL, 90, 250, 'Thụy Sĩ', 'emmental-grand-cru.jpg', 0, 1, '2026-05-25 15:19:14', '2026-05-25 15:19:14'),
(14, 4, 'Brie de Meaux AOP', 'brie-de-meaux-aop', 'Brie de Meaux — \"nữ hoàng phô mai Pháp\" — là phô mai mềm với lớp vỏ trắng mốc mịn như nhung (Penicillium camemberti). Bên trong mềm chảy kem ở nhiệt độ phòng, hương vị nhẹ nhàng, bơ béo, nấm đất và một chút amoniac. Dùng cùng bánh baguette, nho xanh và champagne.', 'Brie de Meaux AOP là loại phô mai mềm làm từ sữa bò thô nguyên chất, nổi tiếng với danh hiệu \"Vua của các loại phô mai\" (Le Roi des Fromages) được phong tặng tại Hội nghị Vienna năm 1815. Chữ AOP (Appellation d\'Origine Protégée - Chỉ dẫn địa lý được bảo hộ của Châu Âu) đảm bảo phô mai chỉ được sản x', 450000, 500000, 35, 200, 'Pháp (Île-de-France)', 'brie-de-meaux.jpg', 0, 1, '2026-05-25 15:19:14', '2026-05-27 15:40:28');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('customer','admin') NOT NULL DEFAULT 'customer',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `phone`, `address`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Quản Trị Viên', 'admin@phomai3anhem.vn', '$2y$12$RoMYzE0jEYpKaTHDqU9OUuXDAPiL3X5Kph.u7aSyVx1pQf2u9Tz2K', '0901234567', NULL, 'admin', '2026-05-25 15:19:14', '2026-05-25 15:19:14'),
(4, 'Tung', 'Tung@gmail.com', '$2y$10$uB7Eo2.IKy7GTfZ3bFxYCOppSSayenfRz.fT4SAHH35ziy41awFlO', '', '', 'admin', '2026-05-27 07:19:04', '2026-05-27 13:05:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_code` (`order_code`),
  ADD KEY `idx_order_status` (`order_status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_price` (`price`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_featured` (`is_featured`);
ALTER TABLE `products` ADD FULLTEXT KEY `idx_search` (`name`,`description`,`short_desc`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_item_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_item_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
