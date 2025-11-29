<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khai báo đầu ca</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

    <div class="card shadow-lg border-0 rounded-4" style="width: 100%; max-width: 420px;">
        <div class="card-body p-5">
            
            <div class="text-center mb-4">
                <div class="mb-3 text-warning">
                    <i class="fas fa-sun fa-3x"></i>
                </div>
                <h4 class="fw-bold text-primary text-uppercase">Chào ngày mới!</h4>
                <p class="text-muted small">Vui lòng kiểm đếm và nhập số tiền đầu ca</p>
            </div>
            
            <form action="<?php echo URLROOT; ?>/shift/open" method="post">
                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary">
                        <i class="fas fa-coins me-1"></i> Tiền đầu ca (VNĐ)
                    </label>
                    <div class="input-group input-group-lg">
                        <input type="number" name="opening_cash" 
                               class="form-control fw-bold text-primary" 
                               placeholder="Ví dụ: 1.000.000" 
                               required autofocus min="0" step="1000">
                        <span class="input-group-text bg-white text-muted">đ</span>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                        <i class="fas fa-check-circle me-2"></i> XÁC NHẬN & BÁN HÀNG
                    </button>
                </div>
            </form>

            <div class="text-center mt-4">
                <a href="<?php echo URLROOT; ?>/auth/logout" class="text-decoration-none text-muted small hover-underline">
                    <i class="fas fa-sign-out-alt me-1"></i> Đăng xuất / Quay lại
                </a>
            </div>
        </div>
    </div>

</body>
</html>