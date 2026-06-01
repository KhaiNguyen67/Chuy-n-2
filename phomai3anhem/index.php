<?php
// ============================================================
// File: index.php
// Chức năng: Trang chủ hoàn chỉnh tích hợp Banner 50vh,
//            3 Sản phẩm bán chạy nhất & Câu chuyện thương hiệu.
// ============================================================
include_once 'config/db.php';
include_once 'includes/header.php';

// 1. Lấy ra tối đa 3 sản phẩm nổi bật/bán chạy (is_featured = 1)
$stmt_prod = $pdo->query("SELECT p.*, c.name AS category_name FROM products p 
                          JOIN categories c ON p.category_id = c.id 
                          WHERE p.is_active = 1 AND p.is_featured = 1 
                          ORDER BY p.id DESC LIMIT 3");
$featured_products = $stmt_prod->fetchAll();

// 2. Đường dẫn xử lý ảnh nền Banner nội bộ trên máy của bạn
$banner_path = 'assets/img/background.jpg'; 
if (!file_exists($banner_path)) {
    // Ảnh nền phụ cao cấp tự động kích hoạt nếu máy bạn chưa có file ảnh kia
    $banner_path = 'https://images.rawpixel.com/image_800/cHJpdmF0ZS9sci9pbWFnZXMvd2Vic2l0ZS8yMDIyLTA1L2stcGYtcG9tLTEyNDcta2EuanBn.jpg';
}
?>

<!-- BANNER ĐẦU TRANG -->
<section class="position-relative d-flex align-items-center overflow-hidden" 
         style="height: 50vh; background: url('<?php echo $banner_path; ?>') center/cover no-repeat;">
    
    <div class="position-absolute top-0 start-0 w-100 h-100" 
         style="background: linear-gradient(90deg, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.4) 60%, rgba(0,0,0,0.1) 100%); z-index: 1;">
    </div>
    
    <div class="container position-relative text-white" style="z-index: 2;" data-aos="fade-right">
        <div class="row">
            <div class="col-md-8 col-lg-6">
                <span class="badge bg-warning text-dark text-uppercase fw-bold px-3 py-2 rounded-pill mb-3" 
                      style="font-size: 0.75rem; letter-spacing: 1px; box-shadow: 0 4px 10px rgba(229,169,59,0.3);">
                    Tinh Hoa Ẩm Thực Âu Châu
                </span>
                
                <h1 class="display-4 fw-bold mb-3 text-white" style="text-shadow: 2px 2px 12px rgba(0,0,0,0.8); letter-spacing: -0.5px;">
                    Cheese and Wine
                </h1>
                
                <p class="lead mb-4 text-white-100" style="font-weight: 400; max-width: 95%;">
                    Sự kết hợp hoàn hảo cho những bữa tiệc sang trọng. Khám phá hương vị phô mai thượng hạng được ủ chín tự nhiên độc quyền.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- DANH SÁCH SẢN PHẨM BÁN CHẠY -->
<section class="container my-5 py-4">
    <div class="text-center mb-5" data-aos="fade-up">
        <span class="text-warning text-uppercase fw-bold small" style="letter-spacing: 1.5px;">Gợi Ý Tuyển Chọn</span>
        <h2 class="fw-bold text-dark mt-1">Sản Phẩm Bán Chạy Nhất</h2>
        <div class="mx-auto bg-warning mt-2" style="width: 60px; height: 3px; border-radius: 2px;"></div>
    </div>

    <div class="row g-4 justify-content-center">
        <?php if (count($featured_products) > 0): ?>
            <?php foreach($featured_products as $prod): ?>
                <div class="col-md-4 col-sm-6" data-aos="fade-up">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white product-card-hover">
                        <div class="position-relative bg-light text-center p-3 d-flex align-items-center justify-content-center" style="height: 220px;">
                            <span class="position-absolute top-0 start-0 m-3 badge bg-danger px-2 py-1 small rounded-pill" style="z-index: 2;">Bán chạy</span>
                            
                            <a href="chitietsanpham.php?id=<?php echo $prod['id']; ?>" class="w-100 h-100 d-flex align-items-center justify-content-center home-img-container">
                                <?php 
                                $img = (!empty($prod['image']) && file_exists('assets/img/'.$prod['image'])) ? 'assets/img/'.$prod['image'] : 'https://images.unsplash.com/photo-1528750994863-30f4a7c05267?q=80&w=400'; 
                                ?>
                                <img src="<?php echo $img; ?>" class="img-fluid rounded-3" style="max-height: 180px; object-fit: contain;" alt="<?php echo htmlspecialchars($prod['name']); ?>">
                            </a>
                        </div>
                        
                        <div class="card-body d-flex flex-column p-4">
                            <span class="text-uppercase text-warning fw-bold mb-1" style="font-size: 0.72rem;"><?php echo htmlspecialchars($prod['category_name']); ?></span>
                            <h5 class="fw-bold my-1 home-prod-title">
                                <a href="chitietsanpham.php?id=<?php echo $prod['id']; ?>" class="text-decoration-none text-dark hover-gold transition-color">
                                    <?php echo htmlspecialchars($prod['name']); ?>
                                </a>
                            </h5>
                            <p class="text-muted small mb-3 flex-grow-1 text-truncate-2"><?php echo htmlspecialchars($prod['short_desc']); ?></p>
                            
                            <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-auto">
                                <span class="fw-bold text-danger fs-5"><?php echo number_format($prod['price'], 0, ',', '.'); ?> đ</span>
                                <form class="ajax-home-cart">
                                    <input type="hidden" name="product_id" value="<?php echo $prod['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-sm btn-gold p-2 lh-1 rounded-circle" title="Thêm vào giỏ hàng">
                                        <i class="bi bi-cart-plus fs-5"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<!-- CÂU CHUYỆN THƯƠNG HIỆU -->
<section id="about" class="container my-5 py-5 border-top">
    <div class="glass-card p-4 p-md-5 position-relative border-start border-warning border-4 shadow-sm bg-white" data-aos="fade-up">
        <div class="row align-items-center g-4">
            <div class="col-lg-4 text-center text-lg-start">
                <span class="text-warning text-uppercase fw-bold small d-block mb-1" style="letter-spacing: 1px;">Chúng Tôi Là Ai</span>
                <h2 class="fw-bold text-dark mb-3">Câu Chuyện <br>Phô Mai 3 Anh Em</h2>
                <p class="text-muted small">Bắt nguồn từ niềm đam mê bất bất tận với nền ẩm thực lâu đời của các quốc gia phương Tây, chúng tôi mang sứ mệnh đem đến những khối phô mai nguyên bản tốt nhất.</p>
            </div>
            <div class="col-lg-8 border-lg-start ps-lg-4">
                <div class="text-secondary lh-lg small" style="text-align: justify;">
                    <p>Chào mừng bạn hành trình khám phá thế giới phô mai cao cấp! Được hình thành từ khát vọng kết nối văn hóa ẩm thực tinh tế của Châu Âu về gần hơn với bàn ăn Việt, mỗi sản phẩm tại hệ thống đều trải qua quá trình tuyển chọn, kiểm định xuất xứ nghiêm ngặt từ Anh, Pháp, Ý.</p>
                    <p class="mb-0">Từ những khối phô mai xanh Stilton Blue đượm hương nồng nàn, Cheddar đậm đà bền bỉ cho tới sự béo ngậy mềm mịn khó cưỡng, chúng tôi tin rằng phô mai không chỉ đơn thuần là một món ăn, đó là cả một nghệ thuật thưởng thức, một sợi dây kết nối niềm vui trọn vẹn bên những người thân yêu.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SCRIPT XỬ LÝ GIỎ HÀNG AJAX -->
<script>
document.querySelectorAll('.ajax-home-cart').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        fetch('cart.php?action=add', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                let badge = document.querySelector('.cart-badge');
                if(badge) {
                    badge.innerText = data.total_items;
                    badge.classList.remove('d-none');
                }
                alert('Đã thêm sản phẩm vào giỏ hàng thành công!');
            }
        });
    });
});
</script>

<style>
/* Style phụ trợ định vị và hiệu ứng card */
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.home-img-container img {
    transition: transform 0.4s ease;
}
.product-card-hover:hover .home-img-container img {
    transform: scale(1.06);
}
.style-contain {
    object-fit: contain !important;
}
</style>

<?php include_once 'includes/footer.php'; ?>