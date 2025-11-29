<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Mã giảm giá</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/sidebar.css">
</head>
<body>

<div class="wrapper">
    <?php require_once APPROOT . '/views/Layouts/sidebar.php'; ?>

    <div id="content">
        <nav class="navbar navbar-light bg-white shadow-sm px-3 mb-4">
            <button type="button" id="sidebarCollapse" class="btn btn-primary me-3"><i class="fas fa-bars"></i></button>
            <h4 class="text-primary mb-0 fw-bold">🎟️ QUẢN LÝ MÃ GIẢM GIÁ</h4>
        </nav>

        <div class="container-fluid px-4">
            <div class="row g-4">
                
                <div class="col-md-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white fw-bold py-3 text-primary">
                            <i class="fas fa-plus-circle me-1"></i> Tạo mã mới
                        </div>
                        <div class="card-body">
                             <form action="<?php echo URLROOT; ?>/discount/add" method="post">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Mã Code (Ví dụ: SALE10)</label>
                                        <input type="text" name="code" class="form-control text-uppercase" required placeholder="Nhập mã...">
                                        
                                        <?php if(isset($_SESSION['error_discount_code'])): ?>
                                            <div class="text-danger small mt-1 fw-bold">
                                                <i class="fas fa-exclamation-triangle me-1"></i> 
                                                <?php echo $_SESSION['error_discount_code']; unset($_SESSION['error_discount_code']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Loại giảm giá</label>
                                        <select name="type" class="form-select">
                                            <option value="fixed">Giảm theo tiền mặt (VNĐ)</option>
                                            <option value="percentage">Giảm theo phần trăm (%)</option>
                                        </select>
                                    </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Giá trị giảm</label>
                                            <div class="input-group">
                                                <input type="number" name="value" class="form-control" required min="1">
                                                <span class="input-group-text" id="value-unit">VNĐ</span>
                                            </div>
                                            <?php if(isset($_SESSION['error_discount_value'])): ?>
                                                <div class="text-danger small mt-1 fw-bold">
                                                    <i class="fas fa-exclamation-triangle me-1"></i> 
                                                    <?php echo $_SESSION['error_discount_value']; unset($_SESSION['error_discount_value']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="mb-3 p-3 bg-light border rounded">
                                            <label class="form-label fw-bold mb-2">Điều kiện áp dụng:</label>
                                            
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="condition_type" id="cond_none" value="none" checked>
                                                <label class="form-check-label" for="cond_none">
                                                    Không có điều kiện (Áp dụng mọi đơn)
                                                </label>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="condition_type" id="cond_min" value="min">
                                                <label class="form-check-label" for="cond_min">
                                                    Có điều kiện: Áp dụng cho đơn hàng từ...
                                                </label>
                                            </div>

                                            <div class="mt-2 ps-4" id="box-min-value" style="display: none;">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">Tổng tiền ></span>
                                                    <input type="number" name="min_order_value" class="form-control" placeholder="Nhập số tiền tối thiểu...">
                                                    <span class="input-group-text">VNĐ</span>
                                                </div>
                                                <?php if(isset($_SESSION['error_discount_min'])): ?>
                                                    <div class="text-danger small mt-1 fw-bold">
                                                        <?php echo $_SESSION['error_discount_min']; unset($_SESSION['error_discount_min']); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                    <div class="mb-4 form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked>
                                        <label class="form-check-label" for="isActive">Kích hoạt ngay</label>
                                    </div>

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary fw-bold">Lưu mã giảm giá</button>
                                    </div>
                                </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white fw-bold py-3">
                            <i class="fas fa-list me-1"></i> Danh sách mã hiện có
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">Code</th>
                                            <th>Loại</th>
                                            <th>Giá trị</th>
                                            <th>Trạng thái</th>
                                            <th class="text-end pe-4">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(!empty($data['discounts'])): ?>
                                            <?php foreach($data['discounts'] as $d): ?>
                                            <tr>
                                                <td class="ps-4 fw-bold text-primary">
                                                    <span class="badge bg-light text-primary border border-primary border-dashed px-3 py-2">
                                                        <?php echo htmlspecialchars($d->code); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php echo ($d->type == 'fixed') ? 'Tiền mặt' : 'Phần trăm'; ?>
                                                </td>
                                                <td class="fw-bold text-success">
                                                    -<?php echo ($d->type == 'fixed') ? number_format($d->value).'đ' : $d->value.'%'; ?>
                                                </td>
                                                <td>
                                                    <?php if($d->is_active): ?>
                                                        <span class="badge bg-success">Đang bật</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Đã ngưng</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <?php if($d->is_active): ?>
                                                        <a href="<?php echo URLROOT; ?>/discount/toggle/<?php echo $d->discount_id; ?>/0" class="btn btn-sm btn-warning text-white me-1"><i class="fas fa-ban"></i></a>
                                                    <?php else: ?>
                                                        <a href="<?php echo URLROOT; ?>/discount/toggle/<?php echo $d->discount_id; ?>/1" class="btn btn-sm btn-success me-1"><i class="fas fa-check"></i></a>
                                                    <?php endif; ?>

                                                    <a href="<?php echo URLROOT; ?>/discount/delete/<?php echo $d->discount_id; ?>" 
                                                       class="btn btn-sm btn-outline-danger"
                                                       onclick="return confirm('⚠️ Bạn có chắc chắn muốn xóa vĩnh viễn không?');">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="5" class="text-center py-4 text-muted">Chưa có mã giảm giá nào.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="<?php echo URLROOT; ?>/js/discount.js"></script>

</body>
</html>