// ============================================================
// File: assets/js/checkout.js
// Chức năng: Tương tác trang Thanh Toán (AJAX cập nhật giỏ hàng)
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

    // ===== Helpers =====

    /** Format số tiền kiểu Việt Nam: 685000 → "685.000 đ" */
    function formatVND(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + ' đ';
    }

    /** Lấy row cha .product-row từ một element con bất kỳ */
    function getRow(el) {
        return el.closest('.product-row');
    }

    // ===== AJAX cập nhật số lượng lên server =====

    /**
     * Gửi số lượng mới lên update_cart_ajax.php, sau đó cập nhật
     * thành tiền từng dòng và tổng tiền toàn đơn.
     *
     * @param {string} productId
     * @param {number} qty
     * @param {HTMLElement} inputEl   - ô nhập số lượng
     * @param {HTMLElement} subtotalEl - span hiển thị thành tiền
     */
    function updateCartAjax(productId, qty, inputEl, subtotalEl) {
        if (qty < 1) return;

        // Disable các nút trong row để tránh click liên tục
        const row   = inputEl.closest('.product-row');
        const btns  = row.querySelectorAll('.btn-qty-minus, .btn-qty-plus');
        btns.forEach(b => (b.disabled = true));

        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity',   qty);

        fetch('update_cart_ajax.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    // Cập nhật ô số lượng
                    inputEl.value = qty;

                    // Cập nhật thành tiền dòng
                    if (subtotalEl) {
                        subtotalEl.textContent = formatVND(data.item_subtotal);
                    }

                    // Cập nhật tạm tính + tổng cộng
                    const elSubtotal   = document.getElementById('txt-subtotal');
                    const elGrandTotal = document.getElementById('txt-grand-total');
                    if (elSubtotal)   elSubtotal.textContent   = formatVND(data.grand_total);
                    if (elGrandTotal) elGrandTotal.textContent = formatVND(data.grand_total);

                    // Cập nhật badge giỏ hàng trên navbar
                    const badge = document.querySelector('.navbar .badge');
                    if (badge && data.cart_count !== undefined) {
                        badge.textContent = data.cart_count;
                    }
                } else {
                    alert(data.message || 'Có lỗi xảy ra khi cập nhật giỏ hàng.');
                    // Rollback số lượng hiển thị về giá trị cũ
                    inputEl.value = inputEl.dataset.prev || inputEl.value;
                }
            })
            .catch(err => {
                console.error('Lỗi AJAX checkout:', err);
            })
            .finally(() => {
                // Re-enable nút sau khi xử lý xong
                btns.forEach(b => (b.disabled = false));
            });
    }

    // ===== Nút Giảm (-) =====

    document.querySelectorAll('.btn-qty-minus').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const row      = getRow(this);
            const pId      = row.dataset.id;
            const inputEl  = row.querySelector('.input-qty');
            const subtotal = row.querySelector('.item-subtotal');
            let   qty      = parseInt(inputEl.value, 10);

            if (qty > 1) {
                inputEl.dataset.prev = qty; // lưu để rollback nếu lỗi
                updateCartAjax(pId, qty - 1, inputEl, subtotal);
            } else {
                if (confirm('Bạn có muốn xóa sản phẩm phô mai này khỏi đơn hàng?')) {
                    window.location.href = 'cart-action.php?action=remove&id=' + pId;
                }
            }
        });
    });

    // ===== Nút Tăng (+) =====

    document.querySelectorAll('.btn-qty-plus').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const row      = getRow(this);
            const pId      = row.dataset.id;
            const inputEl  = row.querySelector('.input-qty');
            const subtotal = row.querySelector('.item-subtotal');
            const qty      = parseInt(inputEl.value, 10);

            inputEl.dataset.prev = qty;
            updateCartAjax(pId, qty + 1, inputEl, subtotal);
        });
    });

    // ===== Xác nhận trước khi submit đặt hàng =====

    const form = document.getElementById('checkout-form');
    if (form) {
        form.addEventListener('submit', function (e) {
            const btnSubmit = document.getElementById('btn-submit-order');
            if (btnSubmit) {
                btnSubmit.disabled    = true;
                btnSubmit.innerHTML   = '<span class="spinner-border spinner-border-sm me-2"></span> Đang xử lý...';
            }
        });
    }

});
// 1. Hàm đồng bộ thông tin từ Người mua sang Người nhận khi tích chọn nút
function syncBuyerToReceiver() {
    let isChecked = document.getElementById('chkSameAsBuyer').checked;
    
    let buyerName = document.getElementById('buyerName').value;
    let buyerPhone = document.getElementById('buyerPhone').value;

    if (isChecked) {
        document.getElementById('receiverName').value = buyerName;
        document.getElementById('receiverPhone').value = buyerPhone;
    } else {
        document.getElementById('receiverName').value = "";
        document.getElementById('receiverPhone').value = "";
    }
}

// Lắng nghe sự kiện gõ trực tiếp ở ô mua, nếu đang tích checkbox thì cập nhật song song theo thời gian thực
document.getElementById('buyerName').addEventListener('input', function() {
    if (document.getElementById('chkSameAsBuyer').checked) {
        document.getElementById('receiverName').value = this.value;
    }
});
document.getElementById('buyerPhone').addEventListener('input', function() {
    if (document.getElementById('chkSameAsBuyer').checked) {
        document.getElementById('receiverPhone').value = this.value;
    }
});

// 2. Kích hoạt tính năng kiểm tra thuộc tính required của Form hợp lệ trước khi submit
function triggerSubmitForm() {
    let form = document.getElementById('formCheckout');
    if (form.checkValidity()) {
        executeOrder();
    } else {
        form.reportValidity(); // Tự động báo đỏ/hiện thông báo thiếu trường dữ liệu bắt buộc (*)
    }
}

// 3. GỬI DỮ LIỆU LƯU CSDL QUA AJAX & HIỂN THỊ MÃ QR CODE REALTIME
function executeOrder() {
    // Gom dữ liệu từ các ô Input để chuẩn bị gửi lên Server
    let orderData = {
        receiver_name: document.getElementById('receiverName').value,
        receiver_phone: document.getElementById('receiverPhone').value,
        receiver_address: document.getElementById('receiverAddress').value,
        order_notes: document.getElementById('orderNotes').value
    };

    // Hiển thị trạng thái đang xử lý trên nút bấm để tránh khách click liên tục
    let btnSubmit = document.querySelector('button[onclick="triggerSubmitForm()"]');
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang khởi tạo đơn hàng...';

    // Tiến hành gửi ngầm dữ liệu sang file save-order.php bằng Fetch API
    fetch('save-order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(orderData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Đổi mã đơn hàng hiển thị thành ID thực tế trong CSDL
            document.getElementById('txtOrderCode').innerText = '#' + data.order_id;
            
            // Ẩn vùng điền thông tin, hiển thị vùng quét mã QR mượt mà
            document.getElementById('checkoutFormSection').classList.add('d-none');
            document.getElementById('qrPaymentSection').classList.remove('d-none');
            
            // Cuộn màn hình lên đầu để khách dễ nhìn mã QR ngân hàng
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            alert('Có lỗi xảy ra: ' + data.message);
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="bi bi-wallet2 me-2"></i> Thanh Tán Ngay';
        }
    })
    .catch(error => {
        console.error('Lỗi kết nối kết xuất dữ liệu:', error);
        alert('Không thể kết nối đến máy chủ. Vui lòng thử lại!');
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = '<i class="bi bi-wallet2 me-2"></i> Thanh Toán Ngay';
    });
}