<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - ForTime Coffee</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/login.css">
</head>
<body>

    <div class="login-container">
        
        <div class="login-banner">
            <div class="banner-content">
                <div class="mb-3">
                    <i class="fas fa-mug-hot fa-4x"></i>
                </div>
                <h1 class="banner-title">FORTIME COFFEE</h1>
                <p class="banner-text">Hệ thống quản lý bán hàng chuyên nghiệp</p>
            </div>
        </div>

        <div class="login-form-section">
            
            <div class="brand-logo-mobile">
                <i class="fas fa-mug-hot me-2"></i>ForTime
            </div>

            <h2 class="form-title">Chào mừng trở lại! 👋</h2>
            
            <?php if(!empty($data['error'])): ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <div><?php echo htmlspecialchars($data['error']); ?></div>
                </div>
            <?php endif; ?>

            <form action="<?php echo URLROOT; ?>/auth/login" method="post">
                <div class="mb-3">
                    <label class="form-label">Tên đăng nhập</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-user"></i></span>
                        <input type="text" name="username" class="form-control border-start-0 ps-0" 
                               value="<?php echo htmlspecialchars($data['username']); ?>" 
                               placeholder="Nhập username..."
                               required autofocus autocomplete="username">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control border-start-0 ps-0" 
                               placeholder="Nhập mật khẩu..."
                               required autocomplete="current-password">
                    </div>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-login text-uppercase fw-bold rounded-pill">
                        Đăng Nhập <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>
            
            <div class="text-center mt-4">
                <small class="text-muted">
                    &copy; <?php echo date('Y'); ?> <strong>ForTime Coffee</strong>. All rights reserved.
                </small>
            </div>
        </div>
    </div>

</body>
</html>