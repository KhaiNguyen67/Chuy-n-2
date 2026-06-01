function generateSlug(val) {
    let slug = val.toLowerCase();
    slug = slug.replace(/á|à|ả|ạ|ã|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/gi, 'a');
    slug = slug.replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/gi, 'e');
    slug = slug.replace(/i|í|ì|ỉ|ĩ|ị/gi, 'i');
    slug = slug.replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/gi, 'o');
    slug = slug.replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/gi, 'u');
    slug = slug.replace(/ý|ỳ|ỷ|ỹ|ỵ/gi, 'y');
    slug = slug.replace(/đ/gi, 'd');
    slug = slug.replace(/[^a-z0-9 -]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
    document.getElementById('postSlug').value = slug;
}

// Hàm xử lý bật Modal khi thêm mới bài viết (Khởi tạo trực tiếp khi click để chống lỗi nạp thiếu thư viện)
function openAddPostModal() {
    document.getElementById('modalPostTitle').innerText = "Viết Bài Thảo Luận Mới";
    document.getElementById('formPostAction').value = "add";
    document.getElementById('postUserId').value = "";
    document.getElementById('postOldImage').value = "";
    document.getElementById('postTitle').value = "";
    document.getElementById('postSlug').value = "";
    document.getElementById('postSummary').value = "";
    document.getElementById('postContent').value = "";
    
    // Kiểm tra tính sẵn sàng của Bootstrap JS trước khi kích hoạt hiển thị
    if (typeof bootstrap !== 'undefined') {
        let postFormModalEl = document.getElementById('postFormModal');
        let myModal = bootstrap.Modal.getInstance(postFormModalEl) || new bootstrap.Modal(postFormModalEl);
        myModal.show();
    } else {
        alert("Lỗi hệ thống: Chưa tìm thấy thư viện Bootstrap JavaScript! Vui lòng kiểm tra lại xem file header.php hoặc footer.php đã nhúng Bootstrap đúng cách chưa.");
    }
}

// Hàm xử lý đổ dữ liệu và bật Modal khi sửa bài viết
function openEditPostModal(post) {
    document.getElementById('modalPostTitle').innerText = "Chỉnh Sửa Nội Dung Bài Viết";
    document.getElementById('formPostAction').value = "edit";
    document.getElementById('postUserId').value = post.id;
    document.getElementById('postOldImage').value = post.image ? post.image : "";
    document.getElementById('postTitle').value = post.title;
    document.getElementById('postSlug').value = post.slug;
    document.getElementById('postSummary').value = post.summary ? post.summary : "";
    document.getElementById('postContent').value = post.content;
    
    // Kiểm tra tính sẵn sàng của Bootstrap JS trước khi kích hoạt hiển thị
    if (typeof bootstrap !== 'undefined') {
        let postFormModalEl = document.getElementById('postFormModal');
        let myModal = bootstrap.Modal.getInstance(postFormModalEl) || new bootstrap.Modal(postFormModalEl);
        myModal.show();
    } else {
        alert("Lỗi hệ thống: Chưa tìm thấy thư viện Bootstrap JavaScript! Vui lòng kiểm tra lại xem file header.php hoặc footer.php đã nhúng Bootstrap đúng cách chưa.");
    }
}