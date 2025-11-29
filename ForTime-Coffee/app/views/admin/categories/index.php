<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Danh mục</title>
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
                <button type="button" id="sidebarCollapse" class="btn btn-primary me-3">
                    <i class="fas fa-bars"></i>
                </button>
                <h4 class="text-primary mb-0 fw-bold">QUẢN LÝ DANH MỤC</h4>
            </div>
        </nav>

        <div class="container-fluid px-4 pb-5">
            <div class="row g-4">
                
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white fw-bold border-bottom py-3 text-primary">
                            <i class="fas fa-plus-circle me-1"></i> Thông tin danh mục
                        </div>
                        <div class="card-body">
                            <form id="categoryForm" action="<?php echo URLROOT; ?>/category/add" method="post">
                                <div class="mb-3">
                                    <label class="form-label small text-muted fw-bold">ID</label>
                                    <input type="text" name="category_id" id="category_id" class="form-control bg-light" readonly placeholder="Tự động">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label small text-muted fw-bold">TÊN DANH MỤC <span class="text-danger">*</span></label>
                                    <input type="text" name="category_name" id="category_name" class="form-control" required placeholder="Ví dụ: Cà phê, Trà sữa...">
                                    
                                    <?php if(isset($_SESSION['error_category'])): ?>
                                        <div class="text-danger small mt-1 fw-bold">
                                            <i class="fas fa-exclamation-triangle me-1"></i> 
                                            <?php 
                                                echo $_SESSION['error_category']; 
                                                unset($_SESSION['error_category']); 
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                    </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="button" onclick="resetCatForm()" class="btn btn-light me-md-2">Làm mới</button>
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
                            <span class="fw-bold text-primary"><i class="fas fa-list me-1"></i> Danh sách danh mục</span>
                            <input type="text" id="searchCat" class="form-control form-control-sm w-auto" placeholder="Tìm nhanh...">
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th class="ps-3" style="width: 100px;">ID</th>
                                            <th>Tên danh mục</th>
                                        </tr>
                                    </thead>
                                    <tbody id="catTableBody">
                                        <?php if (!empty($data['categories'])): ?>
                                            <?php foreach($data['categories'] as $cat): ?>
                                            <tr style="cursor: pointer;" 
                                                onclick='editCategory(<?php echo htmlspecialchars(json_encode($cat), ENT_QUOTES, 'UTF-8'); ?>)'>
                                                
                                                <td class="ps-3 text-muted small">
                                                    <?php echo $cat->category_id; ?>
                                                </td>
                                                
                                                <td class="fw-bold text-dark">
                                                    <?php echo htmlspecialchars($cat->category_name); ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="2" class="text-center text-muted py-4">Chưa có danh mục nào</td>
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
<script src="<?php echo URLROOT; ?>/js/category.js"></script>

</body>
</html>