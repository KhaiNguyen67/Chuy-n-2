let userModal;

document.addEventListener("DOMContentLoaded", function() {
    userModal = new bootstrap.Modal(document.getElementById('userFormModal'));
});

function openEditUserModal(user) {
    document.getElementById('userId').value = user.id;
    document.getElementById('userFullName').value = user.full_name;
    document.getElementById('userEmail').value = user.email;
    document.getElementById('userPhone').value = user.phone ? user.phone : "";
    document.getElementById('userRole').value = user.role.toLowerCase(); // Chuyển chữ thường để khớp option
    document.getElementById('userAddress').value = user.address ? user.address : "";
    
    userModal.show();
}