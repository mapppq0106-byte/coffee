<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hiệu suất: <?php echo htmlspecialchars($data['user']->full_name); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/sidebar.css">
</head>
<body>

<div class="wrapper">
    <?php require_once APPROOT . '/views/Layouts/sidebar.php'; ?>

    <div id="content" class="p-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="<?php echo URLROOT; ?>/staff" class="btn btn-outline-secondary btn-sm mb-2">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
                <h4 class="fw-bold text-primary m-0">
                    <i class="fas fa-user-chart me-2"></i> BÁO CÁO HIỆU SUẤT
                </h4>
                <span class="text-muted">
                    Nhân viên: <strong id="staffName" class="text-dark"><?php echo htmlspecialchars($data['user']->full_name); ?></strong>
                </span>
            </div>
            
            <div class="d-flex gap-2">
                <form action="" method="GET" class="d-flex gap-2 align-items-center bg-white p-2 rounded shadow-sm">
                    <input type="date" name="from" id="dateFrom" class="form-control form-control-sm" 
                           value="<?php echo $data['from_date']; ?>" required>
                    <span>-</span>
                    <input type="date" name="to" id="dateTo" class="form-control form-control-sm" 
                           value="<?php echo $data['to_date']; ?>" required>
                    <button type="submit" class="btn btn-primary btn-sm" title="Lọc dữ liệu">
                        <i class="fas fa-filter"></i>
                    </button>
                </form>
                
                <button onclick="exportKpiExcel()" class="btn btn-success shadow-sm">
                    <i class="fas fa-file-excel"></i> Xuất Excel
                </button>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white border-0 shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-white-50 fw-bold">TỔNG DOANH THU</div>
                            <h3 class="fw-bold mb-0" id="valRevenue">
                                <?php echo number_format($data['stats']['revenue']); ?>đ
                            </h3>
                        </div>
                        <i class="fas fa-wallet fa-3x text-white-50"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-success text-white border-0 shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-white-50 fw-bold">ĐƠN HÀNG ĐÃ BÁN</div>
                            <h3 class="fw-bold mb-0" id="valOrders">
                                <?php echo $data['stats']['orders']; ?>
                            </h3>
                        </div>
                        <i class="fas fa-receipt fa-3x text-white-50"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-warning text-dark border-0 shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-dark-50 fw-bold">TỔNG GIỜ LÀM</div>
                            <h3 class="fw-bold mb-0" id="valHours">
                                <?php echo $data['stats']['hours']; ?>h
                            </h3>
                        </div>
                        <i class="fas fa-clock fa-3x text-dark-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white fw-bold py-3">
                <i class="fas fa-history me-1"></i> Chi tiết ca làm việc
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 text-center align-middle" id="tableShifts">
                        <thead class="table-light">
                            <tr>
                                <th>Ngày</th>
                                <th>Bắt đầu</th>
                                <th>Kết thúc</th>
                                <th>Thời lượng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($data['shifts'])): ?>
                                <?php foreach($data['shifts'] as $shift): ?>
                                <tr>
                                    <td>
                                        <?php echo date('d/m/Y', strtotime($shift->login_time)); ?>
                                    </td>
                                    
                                    <td class="text-success fw-bold">
                                        <?php echo date('H:i', strtotime($shift->login_time)); ?>
                                    </td>
                                    
                                    <td class="text-danger">
                                        <?php echo $shift->logout_time ? date('H:i', strtotime($shift->logout_time)) : '--:--'; ?>
                                    </td>
                                    
                                    <td>
                                        <?php if ($shift->logout_time): ?>
                                            <span class="fw-bold text-dark"><?php echo $shift->duration; ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-success blink">
                                                <i class="fas fa-circle me-1" style="font-size: 8px;"></i> Đang làm việc
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="py-5 text-muted">
                                        <i class="fas fa-calendar-times fa-2x mb-3 opacity-50"></i><br>
                                        Không tìm thấy dữ liệu chấm công trong khoảng thời gian này.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>const URLROOT = '<?php echo URLROOT; ?>';</script>
<script src="<?php echo URLROOT; ?>/js/stats.js"></script>

</body>
</html>