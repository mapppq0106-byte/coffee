<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo kết ca</title>
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
            <h4 class="fw-bold text-primary mb-4">🌙 BÁO CÁO KẾT CA</h4>
            
            <div class="row mb-5">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white fw-bold py-3 border-bottom">
                            <i class="fas fa-info-circle me-1 text-info"></i> Thông tin ca hiện tại
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Giờ mở ca:</span>
                                    <strong><?php echo date('H:i d/m', strtotime($data['session']->start_time)); ?></strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between bg-light">
                                    <span>(+) Tiền đầu ca:</span>
                                    <strong class="text-primary">
                                        <?php echo number_format($data['session']->opening_cash); ?>đ
                                    </strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between bg-light">
                                    <span>(+) Doanh thu bán hàng:</span>
                                    <strong class="text-success">
                                        <?php echo number_format($data['sales']); ?>đ
                                    </strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between border-top border-2 mt-2">
                                    <span class="fw-bold">(=) TỔNG TIỀN TRONG KÉT (Lý thuyết):</span>
                                    <strong class="text-danger fs-5">
                                        <?php echo number_format($data['expected']); ?>đ
                                    </strong>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-primary text-white fw-bold py-3">
                            <i class="fas fa-check-double me-1"></i> Xác nhận & Chốt
                        </div>
                        <div class="card-body">
                            <form action="<?php echo URLROOT; ?>/shift/close" method="post" onsubmit="return confirm('Bạn chắc chắn muốn chốt ca? Hành động này sẽ đăng xuất tài khoản.');">
                                <input type="hidden" name="session_id" value="<?php echo $data['session']->session_id; ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tiền thực tế kiểm đếm:</label>
                                    <div class="input-group">
                                        <input type="number" name="actual_cash" class="form-control form-control-lg text-success fw-bold" 
                                               required placeholder="Nhập số tiền đếm được..." min="0">
                                        <span class="input-group-text">VNĐ</span>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Ghi chú (nếu có chênh lệch):</label>
                                    <textarea name="note" class="form-control" rows="3" placeholder="Ví dụ: Thiếu 10k do thối nhầm..."></textarea>
                                </div>

                                <button type="submit" class="btn btn-danger w-100 py-3 fw-bold shadow-sm">
                                    <i class="fas fa-lock me-2"></i> CHỐT CA & ĐĂNG XUẤT
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="fw-bold text-secondary mb-3"><i class="fas fa-history me-2"></i> Lịch sử các ca trước</h5>
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0 text-center">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Ca số</th>
                                    <th>Người phụ trách</th>
                                    <th>Bắt đầu</th>
                                    <th>Kết thúc</th>
                                    <th>Doanh thu</th>
                                    <th>Thực tế</th>
                                    <th>Chênh lệch</th>
                                    <th>Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($data['history'])): ?>
                                    <?php foreach($data['history'] as $s): ?>
                                        <?php 
                                            $diff = $s->actual_cash - ($s->opening_cash + $s->total_sales);
                                            $diffClass = ($diff < 0) ? 'text-danger' : (($diff > 0) ? 'text-success' : 'text-muted');
                                            $diffText = ($diff == 0) ? 'Khớp' : number_format($diff) . 'đ';
                                        ?>
                                        <tr>
                                            <td class="text-muted small">#<?php echo $s->session_id; ?></td>
                                            <td class="fw-bold text-primary"><?php echo htmlspecialchars($s->full_name); ?></td>
                                            <td><?php echo date('H:i d/m', strtotime($s->start_time)); ?></td>
                                            <td><?php echo date('H:i d/m', strtotime($s->end_time)); ?></td>
                                            <td class="fw-bold text-success"><?php echo number_format($s->total_sales); ?>đ</td>
                                            <td class="fw-bold"><?php echo number_format($s->actual_cash); ?>đ</td>
                                            <td class="fw-bold <?php echo $diffClass; ?>"><?php echo $diffText; ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-info border-0"
                                                        data-start="<?php echo $s->start_time; ?>"
                                                        data-end="<?php echo $s->end_time; ?>"
                                                        data-id="<?php echo $s->session_id; ?>"
                                                        data-note="<?php echo htmlspecialchars($s->note ?? '', ENT_QUOTES); ?>"
                                                        onclick="viewSessionDetail(this)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="8" class="py-4 text-muted">Chưa có lịch sử ca nào.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="sessionDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold">Chi tiết món bán trong Ca #<span id="modalSessionId"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                
                <div id="modalSessionNote" style="display: none;"></div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Tên món</th>
                                <th class="text-center" style="width: 100px;">Số lượng</th>
                                <th class="text-end pe-3" style="width: 150px;">Tổng tiền</th>
                            </tr>
                        </thead>
                        <tbody id="modalItemsBody">
                            </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>const URLROOT = '<?php echo URLROOT; ?>';</script>
<script src="<?php echo URLROOT; ?>/js/shift.js"></script>

</body>
</html>