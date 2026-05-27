<?php
// ============================================================
// File: blog.php
// Chức năng: Gợi ý các công thức, món ăn chế biến từ phô mai
// ============================================================
include_once 'config/db.php';
include_once 'includes/header.php';

// Mảng dữ liệu các bài viết công thức nấu ăn (Dữ liệu mẫu chuẩn Âu cho cửa hàng)
$blogs = [
    [
        'id' => 2,
        'title' => 'Bí Quyết Làm Lẩu Phô Mai (Fondue) Thụy Sĩ Tan Chảy Mượt Mà',
        'category' => 'Món Chính',
        'image' => 'https://images.unsplash.com/photo-1541014741259-de529411b96a?q=80&w=500',
        'summary' => 'Học cách phối trộn phô mai Gruyère và Emmental cùng một chút rượu vang trắng để tạo nên nồi lẩu phô mai kéo sợi thơm lừng cho ngày đông.',
        'time' => '25 phút',
        'level' => 'Trung bình',
        'author' => 'Emma Nguyễn'
    ],
    [
        'id' => 3,
        'title' => 'Mì Ý Sốt Carbonara Truyền Thống Không Dùng Kem Tươi',
        'category' => 'Món Cổ Điển',
        'image' => 'https://images.unsplash.com/photo-1612874742237-6526221588e3?q=80&w=500',
        'summary' => 'Công thức Carbonara nguyên bản từ Roma chỉ sử dụng lòng đỏ trứng gà, thịt má heo guanciale và một lượng lớn phô mai Parmesan bào nhuyễn.',
        'time' => '20 phút',
        'level' => 'Dễ',
        'author' => 'Chef Mario'
    ],
    [
        'id' => 4,
        'title' => 'Nâng Tầm Bánh Mì Nướng Với Phô Mai Brie Và Mật Ong',
        'category' => 'Ăn Nhẹ / Brunch',
        'image' => 'https://images.unsplash.com/photo-1541532713592-79a0317b6b77?q=80&w=500',
        'summary' => 'Lớp phô mai Brie de Meaux béo ngậy như kem được nướng chảy nhẹ trên lát bánh mì baguette, nhấn nhá thêm chút mật ong hoa rừng ngọt dịu.',
        'time' => '15 phút',
        'level' => 'Dễ',
        'author' => 'Minh Thư'
    ],
    [
        'id' => 5,
        'title' => 'Pizza 4 Loại Phô Mai (Quattro Formaggi) Bằng Chảo Tại Nhà',
        'category' => 'Món Chính',
        'image' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?q=80&w=500',
        'summary' => 'Hướng dẫn cách kết hợp hài hòa Mozzarella dẻo, Cheddar đậm đà, Gorgonzola xanh nồng nàn và Parmesan thơm lừng trên một chiếc đế bánh Pizza.',
        'time' => '30 phút',
        'level' => 'Khó',
        'author' => 'Alex Trần'
    ],
];
?>

<div class="container my-5">
    <div class="text-center mb-5" data-aos="fade-up">
        <span class="text-uppercase text-warning fw-bold tracking-wider" style="font-size: 0.85rem; letter-spacing: 2px;">Góc Sáng Tạo Ẩm Thực</span>
        <h1 class="fw-bold mt-1">Gợi Ý Chế Biến & Công Thức</h1>
        <p class="text-muted max-w-xl mx-auto">Biến những khối phô mai thượng hạng thành những tác phẩm nghệ thuật vị giác ngay tại căn bếp nhỏ của bạn.</p>
    </div>
    <div class="row g-4">
        <?php foreach ($blogs as $blog): ?>
            <div class="col-md-6 col-lg-4" data-aos="fade-up">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white product-card-hover d-flex flex-column">
                    
                    <div class="position-relative overflow-hidden" style="height: 220px;">
                        <span class="position-absolute top-0 start-0 m-3 badge bg-dark text-white px-3 py-1.5 rounded-pill small" style="z-index: 2; font-size: 0.75rem;">
                            <?php echo $blog['category']; ?>
                        </span>
                        <img src="<?php echo $blog['image']; ?>" class="w-100 h-100" style="object-fit: cover; transition: transform 0.5s ease;" alt="<?php echo $blog['title']; ?>">
                    </div>

                    <div class="card-body p-4 d-flex flex-column flex-grow-1">
                        <div class="d-flex gap-3 text-muted small mb-2" style="font-size: 0.8rem;">
                            <span><i class="bi bi-clock me-1 text-warning"></i><?php echo $blog['time']; ?></span>
                            <span><i class="bi bi-bar-chart me-1 text-warning"></i><?php echo $blog['level']; ?></span>
                            <span class="ms-auto"><i class="bi bi-person me-1"></i><?php echo $blog['author']; ?></span>
                        </div>

                        <h5 class="fw-bold my-2 text-dark lh-base">
                            <a href="chitietblog.php?id=<?php echo $blog['id']; ?>" class="text-dark text-decoration-none hover-gold">
                                <?php echo $blog['title']; ?>
                            </a>
                        </h5>

                        <p class="text-muted small mb-4 flex-grow-1 lh-relaxed">
                            <?php echo $blog['summary']; ?>
                        </p>

                        <div class="pt-3 border-top mt-auto">
                            <a href="chitietblog.php?id=<?php echo $blog['id']; ?>" class="btn btn-link text-warning p-0 fw-bold text-decoration-none small d-flex align-items-center gap-1 hover-gold">
                                Xem công thức chi tiết <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.product-card-hover:hover img {
    transform: scale(1.08);
}
.hover-gold:hover {
    color: #cca43b !important;
}
</style>

<?php include_once 'includes/footer.php'; ?>