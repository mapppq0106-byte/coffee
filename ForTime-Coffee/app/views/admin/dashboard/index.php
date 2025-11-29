<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Quản trị</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/pos.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/dashboard.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/sidebar.css">
</head>
<body class="dashboard-page">

<div class="wrapper dashboard-wrapper">
    
    <?php require_once APPROOT . '/views/Layouts/sidebar.php'; ?>

    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-3 mb-4 sticky-top">
            <button type="button" id="sidebarCollapse" class="btn btn-primary"><i class="fas fa-bars"></i></button>
            <span class="navbar-brand mb-0 h1 fw-bold text-primary ms-3">📊 TỔNG QUAN & LỊCH SỬ</span>
            
            <div class="ms-auto">
                <span class="text-secondary fw-bold">
                    Xin chào, <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Admin'); ?>
                </span>
            </div>
        </nav>

        <div class="container-fluid px-4 pb-5">
            
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card bg-gradient-primary">
                        <p>Hôm nay</p>
                        <h3><?php echo number_format($data['revenue_today']); ?>đ</h3>
                        <div class="icon"><i class="fas fa-calendar-day"></i></div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card bg-gradient-success">
                        <p>Đơn hàng</p>
                        <h3><?php echo $data['orders_today']; ?></h3>
                        <div class="icon"><i class="fas fa-receipt"></i></div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card bg-gradient-warning">
                        <p>Tháng này</p>
                        <h3><?php echo number_format($data['revenue_month']); ?>đ</h3>
                        <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card bg-gradient-danger">
                        <p>Trạng thái</p>
                        <h3>POS</h3>
                        <div class="icon"><i class="fas fa-signal"></i></div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white fw-bold border-bottom py-3">
                            <i class="fas fa-chart-area me-1 text-primary"></i> Biểu đồ doanh thu
                        </div>
                        <div class="card-body">
                            <div style="height: 300px;">
                                <canvas id="revenueChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white fw-bold border-bottom py-3">
                            <i class="fas fa-chart-pie me-1 text-success"></i> Nguồn doanh thu (Top món)
                        </div>
                        <div class="card-body d-flex flex-column justify-content-center align-items-center">
                            <div style="height: 250px; width: 100%;">
                                <canvas id="sourceChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body py-3">
                        <form action="" method="GET" class="row g-3 align-items-center">
                            <div class="col-auto">
                                <select name="type" class="form-select fw-bold text-primary" onchange="this.form.submit()">
                                    <option value="day" <?php echo ($data['filter_type'] == 'day') ? 'selected' : ''; ?>>Xem theo Ngày</option>
                                    <option value="month" <?php echo ($data['filter_type'] == 'month') ? 'selected' : ''; ?>>Xem theo Tháng</option>
                                    <option value="year" <?php echo ($data['filter_type'] == 'year') ? 'selected' : ''; ?>>Xem theo Năm</option>
                                </select>
                            </div>

                            <div class="col-auto">
                                <span class="fw-bold text-muted">|</span>
                            </div>

                            <div class="col-auto">
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="far fa-calendar-alt text-primary"></i></span>
                                    <input type="date" name="from" class="form-control" value="<?php echo $data['from_date']; ?>" required>
                                </div>
                            </div>
                            <div class="col-auto">
                                <span class="fw-bold text-muted">-</span>
                            </div>
                            <div class="col-auto">
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="far fa-calendar-alt text-primary"></i></span>
                                    <input type="date" name="to" class="form-control" value="<?php echo $data['to_date']; ?>" required>
                                </div>
                            </div>

                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-filter"></i> Lọc</button>
                                <a href="<?php echo URLROOT; ?>/dashboard" class="btn btn-light border ms-1" title="Đặt lại"><i class="fas fa-sync-alt"></i></a>
                            </div>
                        </form>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white fw-bold border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <span><i class="fas fa-history me-1 text-info"></i> Lịch sử đơn hàng</span>
                            
                            <form action="" method="GET" class="position-relative" style="min-width: 250px;">
                                <input type="hidden" name="from" value="<?php echo $data['from_date']; ?>">
                                <input type="hidden" name="to" value="<?php echo $data['to_date']; ?>">
                                
                                <input type="text" name="search" class="form-control form-control-sm rounded-pill ps-4 bg-light" 
                                       placeholder="Tìm mã đơn, nhân viên..." 
                                       value="<?php echo isset($data['search_keyword']) ? htmlspecialchars($data['search_keyword']) : ''; ?>">
                                
                                <button type="submit" class="btn position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent" style="right: 10px;">
                                    <i class="fas fa-search text-muted" style="font-size: 0.8rem;"></i>
                                </button>
                            </form>
                        </div>
                        
                        <div class="card-body">
                            <div class="mb-3 d-flex gap-2 justify-content-end">
                                <button onclick="exportToExcel()" class="btn btn-success btn-sm text-white shadow-sm"><i class="fas fa-file-excel me-1"></i> Excel</button>
                                <button onclick="exportToCSV()" class="btn btn-info btn-sm text-white shadow-sm"><i class="fas fa-file-csv me-1"></i> CSV</button>
                                <button onclick="exportToPDF()" class="btn btn-danger btn-sm text-white shadow-sm"><i class="fas fa-file-pdf me-1"></i> PDF</button>
                                
                            </div>

                            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                <table id="exportTable" class="table table-hover table-bordered align-middle mb-0">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th class="ps-3">Mã đơn</th>
                                            <th>Người bán</th>
                                            <th class="text-end">Tổng tiền</th>
                                            <th class="text-center">Thời gian</th>
                                            <th class="text-center no-export">Chi tiết</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ordersTableBody">
                                        <?php if(!empty($data['orders'])): ?>
                                            <?php foreach($data['orders'] as $order): ?>
                                            
                                            <tr class="clickable-row" data-id="<?php echo $order->order_id; ?>" style="cursor: pointer;">
                                                <td class="ps-3 fw-bold text-primary">
                                                    #<?php echo $order->order_id; ?>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($order->staff_name ?? 'Admin'); ?>
                                                </td>
                                                <td class="text-end">
                                                    <?php if($order->total_amount > $order->final_amount): ?>
                                                        <div class="text-decoration-line-through text-muted small">
                                                            <?php echo number_format($order->total_amount); ?>đ
                                                        </div>
                                                        <div class="fw-bold text-danger">
                                                            <?php echo number_format($order->final_amount); ?>đ
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="fw-bold text-danger">
                                                            <?php echo number_format($order->final_amount); ?>đ
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center text-muted small">
                                                    <?php echo date('H:i d/m/Y', strtotime($order->order_time)); ?>
                                                </td>
                                                <td class="text-center no-export">
                                                    <button class="btn btn-sm btn-outline-info border-0" onclick="showOrderDetail(<?php echo $order->order_id; ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    <i class="fas fa-inbox fa-2x mb-3 opacity-50"></i><br>
                                                    Chưa có đơn hàng nào.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php if($data['total_pages'] > 1): ?>
                            <div class="d-flex justify-content-end p-3 border-top">
                                <nav>
                                    <ul class="pagination pagination-sm mb-0">
                                        <li class="page-item <?php echo ($data['current_page'] <= 1) ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="?from=<?= $data['from_date'] ?>&to=<?= $data['to_date'] ?>&search=<?= isset($data['search_keyword']) ? $data['search_keyword'] : '' ?>&page=<?= $data['current_page'] - 1 ?>">
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        </li>

                                        <?php for($i = 1; $i <= $data['total_pages']; $i++): ?>
                                            <li class="page-item <?php echo ($i == $data['current_page']) ? 'active' : ''; ?>">
                                                <a class="page-link" href="?from=<?= $data['from_date'] ?>&to=<?= $data['to_date'] ?>&search=<?= isset($data['search_keyword']) ? $data['search_keyword'] : '' ?>&page=<?= $i ?>">
                                                    <?= $i ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>

                                        <li class="page-item <?php echo ($data['current_page'] >= $data['total_pages']) ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="?from=<?= $data['from_date'] ?>&to=<?= $data['to_date'] ?>&search=<?= isset($data['search_keyword']) ? $data['search_keyword'] : '' ?>&page=<?= $data['current_page'] + 1 ?>">
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                            <?php endif; ?>
                            
                            <div id="noResult" class="text-center py-4 text-muted" style="display: none;">
                                Không tìm thấy kết quả phù hợp.
                            </div>
                        </div>
                    </div>
                </div>
            </div> </div> </div> </div> <input type="hidden" id="topProductsData" value='<?php echo isset($data['top_products']) ? htmlspecialchars(json_encode($data['top_products']), ENT_QUOTES, 'UTF-8') : "[]"; ?>'>
<input type="hidden" id="chartLabels" value='<?php echo htmlspecialchars(json_encode($data['chart_labels']), ENT_QUOTES, 'UTF-8'); ?>'>
<input type="hidden" id="chartValues" value='<?php echo htmlspecialchars(json_encode($data['chart_values']), ENT_QUOTES, 'UTF-8'); ?>'>

<?php require_once APPROOT . '/views/admin/orders/modal_detail.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>const URLROOT = '<?php echo URLROOT; ?>';</script>

<script src="<?php echo URLROOT; ?>/js/dashboard.js"></script>
<script src="<?php echo URLROOT; ?>/js/order_detail.js"></script>
<script src="<?php echo URLROOT; ?>/js/export.js"></script>

</body>
</html>