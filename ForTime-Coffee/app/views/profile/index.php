<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông tin tài khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/sidebar.css"> </head>
<body>

<div class="wrapper">
    <?php require_once APPROOT . '/views/Layouts/sidebar.php'; ?>
    
    <div id="content">
        <nav class="navbar navbar-light bg-white shadow-sm px-3 mb-4 d-md-none">
            <button type="button" id="sidebarCollapse" class="btn btn-primary">
                <i class="fas fa-bars"></i>
            </button>
        </nav>

        <div class="container-fluid p-4">
            <h4 class="fw-bold text-primary mb-4">👤 HỒ SƠ CÁ NHÂN</h4>
            
            <div class="row g-4">
                <div class="col-md-5">
                    
                    <div class="card shadow-sm border-0 text-center p-4 mb-3">
                        <div class="mb-3">
                            <img src="https://ui-avatars.com/api/?name=<?php echo htmlspecialchars($data['user']->full_name); ?>&background=random&size=128" 
                                 class="rounded-circle shadow" width="100" height="100" alt="Avatar">
                        </div>
                        <h5 class="fw-bold text-dark">
                            <?php echo htmlspecialchars($data['user']->full_name); ?>
                        </h5>
                        <p class="badge bg-light text-dark border">
                            <?php echo ($data['user']->role_id == 1) ? 'Quản trị viên' : 'Nhân viên'; ?>
                        </p>
                        <hr>
                        <div class="d-flex justify-content-between text-start px-3">
                            <span class="text-muted"><i class="fas fa-user-tag me-2"></i> Username:</span>
                            <span class="fw-bold text-primary">
                                <?php echo htmlspecialchars($data['user']->username); ?>
                            </span>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 bg-primary text-white p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-white-50">Doanh số cá nhân hôm nay</small>
                                <h3 class="fw-bold mb-0">
                                    <?php echo number_format($data['sales_today']); ?>đ
                                </h3>
                            </div>
                            <i class="fas fa-trophy fa-2x text-white-50"></i>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white fw-bold border-bottom py-3">
                            <i class="fas fa-clock me-1 text-info"></i> Lịch sử chấm công (10 lần gần nhất)
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 text-center small align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Ngày</th>
                                            <th>Vào ca</th>
                                            <th>Tan ca</th>
                                            <th>Thời gian</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(!empty($data['shifts'])): ?>
                                            <?php foreach($data['shifts'] as $shift): ?>
                                            <tr>
                                                <td><?php echo date('d/m', strtotime($shift->login_time)); ?></td>
                                                
                                                <td class="text-success fw-bold">
                                                    <?php echo date('H:i', strtotime($shift->login_time)); ?>
                                                </td>
                                                
                                                <td class="text-danger">
                                                    <?php echo $shift->logout_time ? date('H:i', strtotime($shift->logout_time)) : '--:--'; ?>
                                                </td>
                                                
                                                <td class="fw-bold text-secondary">
                                                    <?php if ($shift->logout_time): ?>
                                                        <?php echo $shift->duration; ?>
                                                    <?php else: ?>
                                                        <span class="badge bg-success blink">Đang làm</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="4" class="text-muted py-3">Chưa có dữ liệu chấm công</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white fw-bold py-3 text-danger">
                            <i class="fas fa-key me-1"></i> Đổi mật khẩu
                        </div>
                        <div class="card-body">
                            <form action="<?php echo URLROOT; ?>/profile/change_password" method="post">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Mật khẩu hiện tại</label>
                                    <input type="password" name="old_pass" class="form-control" required placeholder="Nhập mật khẩu cũ...">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Mật khẩu mới</label>
                                    <input type="password" name="new_pass" class="form-control" required placeholder="Nhập mật khẩu mới...">
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Nhập lại mật khẩu mới</label>
                                    <input type="password" name="confirm_pass" class="form-control" required placeholder="Xác nhận mật khẩu mới...">
                                </div>
                                
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-save me-1"></i> Cập nhật
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo URLROOT; ?>/js/profile.js"></script>

</body>
</html>