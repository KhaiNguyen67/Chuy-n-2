let modalInstance;

document.addEventListener("DOMContentLoaded", function() {
    modalInstance = new bootstrap.Modal(document.getElementById('productFormModal'));
});

function openAddModal() {
    document.getElementById('modalComponentTitle').innerText = "Thêm Sản Phẩm Khối Mới";
    document.getElementById('formAction').value = "add";
    document.getElementById('productId').value = "";
    document.getElementById('productOldImage').value = "";
    document.getElementById('productName').value = "";
    document.getElementById('productPrice').value = "";
    document.getElementById('productDesc').value = "";
    document.getElementById('productFeatured').checked = false;
    document.getElementById('imageHelpBlock').style.display = "none";
    modalInstance.show();
}

function openEditModal(prod) {
    document.getElementById('modalComponentTitle').innerText = "Cập Nhật Thông Tin Sản Phẩm";
    document.getElementById('formAction').value = "edit";
    document.getElementById('productId').value = prod.id;
    document.getElementById('productOldImage').value = prod.image ? prod.image : "";
    document.getElementById('productName').value = prod.name;
    document.getElementById('productCategoryId').value = prod.category_id;
    document.getElementById('productPrice').value = prod.price;
    document.getElementById('productDesc').value = prod.short_desc ? prod.short_desc : "";
    
    document.getElementById('productFeatured').checked = (prod.is_featured == 1);
    document.getElementById('imageHelpBlock').style.display = "block";
    modalInstance.show();
}