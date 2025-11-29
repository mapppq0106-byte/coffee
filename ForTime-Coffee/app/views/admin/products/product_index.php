<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Món</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/pos.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/sidebar.css">
</head>
<body>

<div class="wrapper">
   <?php require_once APPROOT . '/views/Layouts/sidebar.php'; ?>

    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-3 mb-3">
            <button type="button" id="sidebarCollapse" class="btn btn-primary">
                <i class="fas fa-bars"></i>
            </button>
            <span class="navbar-brand mb-0 h1 fw-bold text-primary ms-3">☕ QUẢN LÝ MÓN ĂN</span>
        </nav>

        <div class="container-fluid px-4">
            <div class="row g-3">
                
                <div class="col-md-4">
                    <div class="card-box shadow-sm p-3 bg-white rounded">
                        <h5 class="text-primary mb-4 border-bottom pb-2">Thông tin món</h5>
                        
                        <form id="productForm" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Mã món</label>
                                <input type="text" name="product_id" id="product_id" class="form-control bg-light" readonly placeholder="Tự động">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tên món</label>
                                <input type="text" name="product_name" id="product_name" class="form-control" required>
                                
                                <?php if(isset($_SESSION['error_product_name'])): ?>
                                    <div class="text-danger small mt-1 fw-bold">
                                        <i class="fas fa-exclamation-triangle me-1"></i> 
                                        <?php echo $_SESSION['error_product_name']; unset($_SESSION['error_product_name']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Danh mục</label>
                                <select name="category_id" id="category_id" class="form-select">
                                    <?php foreach($data['categories'] as $cat): ?>
                                        <option value="<?php echo $cat->category_id; ?>" 
                                                <?php echo ($cat->is_deleted == 1) ? 'class="text-danger bg-light"' : ''; ?>>
                                            <?php 
                                                echo htmlspecialchars($cat->category_name); 
                                                echo ($cat->is_deleted == 1) ? ' (Đã xóa)' : ''; 
                                            ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Giá bán</label>
                                <input type="number" name="price" id="price" class="form-control" required min="1000">
                                
                                <?php if(isset($_SESSION['error_product_price'])): ?>
                                    <div class="text-danger small mt-1 fw-bold">
                                        <i class="fas fa-exclamation-triangle me-1"></i> 
                                        <?php echo $_SESSION['error_product_price']; unset($_SESSION['error_product_price']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Hình ảnh</label>
                                <input type="file" name="image" class="form-control">
                                <div id="current_image_preview" class="mt-2 text-center"></div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Trạng thái</label>
                                <select name="is_available" id="is_available" class="form-select">
                                    <option value="1">Đang bán (Sử dụng)</option>
                                    <option value="0">Ngừng bán</option>
                                </select>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" formaction="<?php echo URLROOT; ?>/product/add" class="btn btn-info text-white flex-grow-1">
                                    <i class="fas fa-plus"></i> THÊM
                                </button>
                                <button type="button" id="btnEdit" class="btn btn-warning text-white flex-grow-1">
                                    <i class="fas fa-edit"></i> SỬA
                                </button>
                                <button type="button" id="btnDelete" class="btn btn-danger text-white flex-grow-1">
                                    <i class="fas fa-trash"></i> XÓA
                                </button>
                            </div>
                            <div class="mt-3 text-center">
                                <button type="button" onclick="resetForm()" class="btn btn-sm btn-outline-secondary">Làm mới form</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card-box shadow-sm p-3 bg-white rounded">
                        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                            <h5 class="text-primary mb-0">Danh sách món</h5>
                            <input type="text" id="searchInput" class="form-control w-50" placeholder="Tìm kiếm món ăn...">
                        </div>
                        
                        <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th style="width: 50px;">Mã</th>
                                        <th style="width: 80px;">Ảnh</th> 
                                        <th>Tên món</th>
                                        <th>Danh mục</th>
                                        <th>Giá</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody id="productTableBody">
                                    <?php if(!empty($data['products'])): ?>
                                        <?php foreach($data['products'] as $product): ?>
                                        
                                        <tr class="table-row" onclick='selectProduct(this, <?php echo htmlspecialchars(json_encode($product), ENT_QUOTES, 'UTF-8'); ?>)'>
                                            
                                            <td><?php echo $product->product_id; ?></td>
                                            
                                            <td class="text-center">
                                                <?php if($product->image): ?>
                                                    <img src="<?php echo URLROOT . '/public/uploads/' . htmlspecialchars($product->image); ?>" 
                                                         class="thumb-img" style="width: 40px; height: 40px; object-fit: cover;">
                                                <?php else: ?>
                                                    <span class="text-muted small">No img</span>
                                                <?php endif; ?>
                                            </td>
                                            
                                            <td class="fw-bold">
                                                <?php echo htmlspecialchars($product->product_name); ?>
                                            </td>
                                            
                                            <td>
                                                <?php echo htmlspecialchars($product->category_name); ?>
                                            </td>
                                            
                                            <td class="text-danger fw-bold">
                                                <?php echo number_format($product->price); ?>
                                            </td>
                                            
                                            <td>
                                                <?php if($product->is_available): ?>
                                                    <span class="badge bg-success">Sử dụng</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Ngừng</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">Chưa có món ăn nào.</td>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Định nghĩa biến toàn cục URLROOT để file JS bên dưới sử dụng
    const URLROOT = '<?php echo URLROOT; ?>';
</script>

<script src="<?php echo URLROOT; ?>/js/product.js"></script>

</body>
</html>