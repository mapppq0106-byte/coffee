<?php
    $current_uri = $_SERVER['REQUEST_URI'];

    // Hàm kiểm tra Active Menu
    function setActive($keyword, $uri) {
        return (strpos($uri, $keyword) !== false) ? 'active' : '';
    }

    // Kiểm tra nhóm menu "Quản trị" có đang mở không
    $manageKeywords = ['/dashboard', '/product', '/category', '/table', '/staff', '/discount'];
    $isManageGroupActive = false;
    foreach ($manageKeywords as $key) {
        if (strpos($current_uri, $key) !== false) {
            $isManageGroupActive = true;
            break;
        }
    }
?>

<nav id="sidebar">
    <div class="sidebar-header">
        <a href="<?php echo URLROOT; ?>/dashboard" class="text-decoration-none">
            <div class="d-flex align-items-center text-white">
                <i class="fas fa-mug-hot fa-2x me-2 text-warning"></i>
                <div style="line-height: 1.1;">
                    <div class="fw-bold" style="font-size: 1.1rem; letter-spacing: 1px;">FORTIME</div>
                    <div style="font-size: 0.75rem; opacity: 0.8;">COFFEE & TEA</div>
                </div>
            </div>
        </a>
    </div>
    
    <ul class="list-unstyled components">
        <p class="text-warning opacity-75">QUẢN LÝ CHUNG</p>
        
        <li class="<?php echo setActive('/pos', $current_uri); ?>">
            <a href="<?php echo URLROOT; ?>/pos"><i class="fas fa-cash-register me-2"></i> Màn hình POS</a>
        </li>
        
        <?php if(isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>
        <p class="text-warning opacity-75">QUẢN TRỊ VIÊN</p>
        
        <li class="<?php echo $isManageGroupActive ? 'active' : ''; ?>">
            <a href="#homeSubmenu" data-bs-toggle="collapse" aria-expanded="<?php echo $isManageGroupActive ? 'true' : 'false'; ?>" class="dropdown-toggle d-flex justify-content-between align-items-center">
                <span><i class="fas fa-user-cog me-2"></i> Quản lý</span>
                <i class="fas fa-chevron-down small"></i>
            </a>
            <ul class="collapse list-unstyled <?php echo $isManageGroupActive ? 'show' : ''; ?>" id="homeSubmenu">
                <li class="<?php echo setActive('/dashboard', $current_uri); ?>">
                    <a href="<?php echo URLROOT; ?>/dashboard"><i class="fas fa-chart-line me-2"></i>Doanh thu</a>
                </li>
                <li class="<?php echo setActive('/product', $current_uri); ?>">
                    <a href="<?php echo URLROOT; ?>/product"><i class="fas fa-coffee me-2"></i>Món</a>
                </li>
                <li class="<?php echo setActive('/category', $current_uri); ?>">
                    <a href="<?php echo URLROOT; ?>/category"><i class="fas fa-tags me-2"></i> Danh mục</a>
                </li>
                <li class="<?php echo setActive('/table', $current_uri); ?>">
                    <a href="<?php echo URLROOT; ?>/table"><i class="fas fa-chair me-2"></i> Bàn</a>
                </li>
                <li class="<?php echo setActive('/discount', $current_uri); ?>">
                    <a href="<?php echo URLROOT; ?>/discount"><i class="fas fa-ticket-alt me-2"></i> Mã giảm giá</a>
                </li>
                <li class="<?php echo setActive('/staff', $current_uri); ?>">
                    <a href="<?php echo URLROOT; ?>/staff"><i class="fas fa-users me-2"></i>Tài khoản</a>
                </li>
            </ul>
        </li>
        <?php endif; ?>

        <p class="text-warning opacity-75">CÀI ĐẶT</p>
        
        <li class="<?php echo setActive('/shift', $current_uri); ?>">
            <a href="<?php echo URLROOT; ?>/shift"><i class="fas fa-file-invoice-dollar me-2"></i> Báo cáo kết ca</a>
        </li>

        <li class="<?php echo setActive('/profile', $current_uri); ?>">
            <a href="<?php echo URLROOT; ?>/profile"><i class="fas fa-user me-2"></i> Tài khoản</a>
        </li>
        
        <li>
            <a href="<?php echo URLROOT; ?>/auth/logout" class="text-danger bg-white bg-opacity-10 mt-3">
                <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
            </a>
        </li>
    </ul>
</nav>