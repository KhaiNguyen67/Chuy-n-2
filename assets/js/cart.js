// ============================================================
// File: assets/js/cart.js
// Chức năng: Tương tác trang Giỏ Hàng
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

    // ===== Helpers =====

    /**
     * Format số tiền kiểu Việt Nam: 685000 → "685.000 đ"
     */
    function formatVND(amount) {
        return amount.toLocaleString('vi-VN') + ' đ';
    }

    /**
     * Tính lại tổng tiền từ tất cả các dòng và cập nhật phần tóm tắt
     */
    function recalcTotal() {
        let grandTotal = 0;

        document.querySelectorAll('#cart-table tbody tr').forEach(function (row) {
            const price    = parseInt(row.dataset.price, 10);
            const qtyInput = row.querySelector('.qty-input');
            const qty      = parseInt(qtyInput.value, 10) || 1;
            const subtotal = price * qty;

            row.querySelector('.item-subtotal').textContent = formatVND(subtotal);
            grandTotal += subtotal;
        });

        document.getElementById('summary-subtotal').textContent = formatVND(grandTotal);
        document.getElementById('summary-total').textContent    = formatVND(grandTotal);
    }

    /**
     * Gửi request cập nhật số lượng lên server (cart-action.php)
     */
    function syncQtyToServer(productId, qty) {
        fetch(`cart-action.php?action=update&id=${productId}&qty=${qty}`, {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).catch(function (err) {
            console.warn('Không thể đồng bộ giỏ hàng:', err);
        });
    }

    // ===== Nút tăng / giảm số lượng =====

    document.querySelectorAll('.btn-plus, .btn-minus').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id       = this.dataset.id;
            const row      = document.querySelector(`tr[data-id="${id}"]`);
            const input    = row.querySelector('.qty-input');
            let qty        = parseInt(input.value, 10) || 1;

            if (this.classList.contains('btn-plus')) {
                qty = Math.min(qty + 1, 99);
            } else {
                qty = Math.max(qty - 1, 1);
            }

            input.value = qty;
            recalcTotal();
            syncQtyToServer(id, qty);
        });
    });

    // ===== Nhập thẳng số lượng =====

    document.querySelectorAll('.qty-input').forEach(function (input) {
        // Chặn giá trị không hợp lệ khi blur
        input.addEventListener('blur', function () {
            let qty = parseInt(this.value, 10);
            if (isNaN(qty) || qty < 1) qty = 1;
            if (qty > 99) qty = 99;
            this.value = qty;
            recalcTotal();
            syncQtyToServer(this.dataset.id, qty);
        });

        // Cập nhật realtime khi gõ
        input.addEventListener('input', function () {
            const qty = parseInt(this.value, 10);
            if (!isNaN(qty) && qty >= 1) {
                recalcTotal();
            }
        });
    });

    // ===== Xác nhận xóa từng sản phẩm =====

    document.querySelectorAll('.btn-delete').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const name = this.dataset.name || 'sản phẩm này';
            const href = this.getAttribute('href');

            if (confirm(`Bạn có chắc muốn xóa "${name}" khỏi giỏ hàng?`)) {
                window.location.href = href;
            }
        });
    });

    // ===== Xác nhận xóa toàn bộ giỏ hàng =====

    const btnClear = document.querySelector('.btn-clear-cart');
    if (btnClear) {
        btnClear.addEventListener('click', function (e) {
            e.preventDefault();
            if (confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?')) {
                window.location.href = this.getAttribute('href');
            }
        });
    }

});