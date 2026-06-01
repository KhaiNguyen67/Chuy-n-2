// ============================================================
// File: assets/js/about.js
// Chức năng: Tương tác trang Giới Thiệu / Trang Chủ
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

    // ===== Khởi tạo AOS (Animate On Scroll) =====
    // Đảm bảo AOS đã được load từ header/footer trước
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 700,       // ms mỗi animation
            easing: 'ease-out-quad',
            once: true,          // chỉ chạy 1 lần khi scroll vào
            offset: 60,          // khoảng cách trigger (px từ bottom viewport)
        });
    }

    // ===== Smooth scroll cho anchor link #about =====
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            const target   = document.querySelector(targetId);
            if (!target) return;

            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    // ===== Parallax nhẹ cho Hero Banner =====
    const heroBanner = document.querySelector('.hero-banner');
    if (heroBanner) {
        window.addEventListener('scroll', function () {
            const scrollY  = window.scrollY;
            const maxShift = 80; // px tối đa dịch chuyển
            const shift    = Math.min(scrollY * 0.25, maxShift);
            heroBanner.style.backgroundPositionY = `calc(center + ${shift}px)`;
        }, { passive: true });
    }

    // ===== Hover ripple cho các Commitment Card =====
    document.querySelectorAll('.commitment-card').forEach(function (card) {
        card.addEventListener('mouseenter', function () {
            this.style.transition = 'transform 0.25s ease, box-shadow 0.25s ease';
        });
    });

});
