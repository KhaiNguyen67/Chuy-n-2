function changeQty(amount) {
    let input = document.getElementById('quantity_input');
    let current = parseInt(input.value) || 1;
    let nextValue = current + amount;
    let maxStock = parseInt(input.getAttribute('max')) || 1;
    if (nextValue >= 1 && nextValue <= maxStock) { input.value = nextValue; }
}

document.getElementById('detail-cart-form').addEventListener('submit', function(e) {
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
            alert('Đã cập nhật sản phẩm vào giỏ hàng thành công!');
        }
    });
});