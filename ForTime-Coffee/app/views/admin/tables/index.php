<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Bàn</title>
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
        <nav class="navbar navbar-light bg-white shadow-sm px-3 mb-4 sticky-top">
            <div class="d-flex align-items-center w-100">
                <button type="button" id="sidebarCollapse" class="btn btn-primary me-3"><i class="fas fa-bars"></i></button>
                <h4 class="text-primary mb-0 fw-bold">QUẢN LÝ BÀN</h4>
            </div>
        </nav>

        <div class="container-fluid px-4 pb-5">
            <div class="row g-4">
                
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white fw-bold border-bottom py-3 text-primary">
                            <i class="fas fa-plus-circle me-1"></i> Thông tin bàn
                        </div>
                        <div class="card-body">
                            <form id="tableForm" action="<?php echo URLROOT; ?>/table/add" method="post">
                                <div class="mb-3">
                                    <label class="form-label small text-muted fw-bold">ID</label>
                                    <input type="text" name="table_id" id="table_id" class="form-control bg-light" readonly placeholder="Tự động">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small text-muted fw-bold">TÊN BÀN <span class="text-danger">*</span></label>
                                    <input type="text" name="table_name" id="table_name" class="form-control" required placeholder="Ví dụ: Bàn 1, Tầng 2...">
                                    
                                    <?php if(isset($_SESSION['error_table_name'])): ?>
                                        <div class="text-danger small mt-1 fw-bold">
                                            <i class="fas fa-exclamation-triangle me-1"></i> 
                                            <?php 
                                                echo $_SESSION['error_table_name']; 
                                                unset($_SESSION['error_table_name']); 
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="button" onclick="resetTableForm()" class="btn btn-light me-md-2">Làm mới</button>
                                    <button type="submit" id="btnSave" class="btn btn-primary"><i class="fas fa-save"></i> Lưu lại</button>
                                    <button type="button" id="btnDelete" class="btn btn-danger d-none"><i class="fas fa-trash"></i> Xóa</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-primary"><i class="fas fa-list me-1"></i> Danh sách bàn</span>
                            <input type="text" id="searchTable" class="form-control form-control-sm w-auto" placeholder="Tìm nhanh...">
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th class="ps-3" style="width: 100px;">ID</th>
                                            <th>Tên bàn</th>
                                            <th>Trạng thái hiện tại</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableTableBody">
                                        <?php if (!empty($data['tables'])): ?>
                                            <?php foreach($data['tables'] as $table): ?>
                                            
                                            <tr class="table-row clickable-row" 
                                                data-json='<?php echo htmlspecialchars(json_encode($table), ENT_QUOTES, 'UTF-8'); ?>'>
                                                
                                                <td class="ps-3 text-muted small">
                                                    <?php echo $table->table_id; ?>
                                                </td>
                                                
                                                <td class="fw-bold text-dark">
                                                    <?php echo htmlspecialchars($table->table_name); ?>
                                                </td>
                                                
                                                <td>
                                                    <?php if($table->status == 'empty'): ?>
                                                        <span class="badge bg-success-subtle text-success rounded-pill">Trống</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning-subtle text-warning border border-warning rounded-pill">Có khách</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center py-4 text-muted">Chưa có bàn nào được tạo.</td>
                                            </tr>
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
<script>const URLROOT = '<?php echo URLROOT; ?>';</script>
<script src="<?php echo URLROOT; ?>/js/table.js"></script>

</body>
</html>