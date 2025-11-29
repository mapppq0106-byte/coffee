<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Bán Hàng - ForTime Coffee</title>
    
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

        <nav class="navbar navbar-expand-lg navbar-pos mb-3 d-flex justify-content-between">
            <button type="button" id="mobileSidebarCollapse" class="btn btn-light text-primary d-inline-block d-md-none me-3 shadow-sm rounded-circle" style="width: 40px; height: 40px;">
                <i class="fas fa-bars"></i>
            </button>

            <div class="d-flex align-items-center">
                <div class="brand-logo-icon"><i class="fas fa-mug-hot"></i></div>
                <div>
                    <div class="brand-title">FORTIME COFFEE</div>
                    <div class="brand-subtitle">POS SYSTEM</div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="d-none d-md-block text-end me-2">
                    <div class="fw-bold text-dark" id="clock-time" style="font-size: 1.1rem;">--:--</div>
                    <div class="small text-muted" id="clock-date">--/--/----</div>
                </div>

                <div class="user-badge">
                    <img src="https://ui-avatars.com/api/?name=<?php echo htmlspecialchars($_SESSION['full_name']); ?>&background=4e73df&color=fff" class="user-avatar">
                    <div class="d-none d-sm-block pe-2">
                        <div class="fw-bold text-dark small" style="line-height: 1.2;">
                            <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?>
                        </div>
                        <div class="text-muted" style="font-size: 0.7rem;">
                            <?php echo ($_SESSION['role_id'] == 1) ? 'Quản trị viên' : 'Nhân viên'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container-fluid main-container">
            <div class="row row-full-height g-2">
                
                <div class="col-lg-3 col-md-4 col-12 col-full-height">
                    <div class="card-scroll-wrapper">
                        <div class="card-header-sticky p-3 fw-bold text-primary">
                            <i class="fas fa-th-large"></i> SƠ ĐỒ BÀN
                        </div>
                        <div class="card-body-scroll">
                            <div class="row g-2">
                                <?php if(!empty($data['tables'])): ?>
                                    <?php foreach($data['tables'] as $table): ?>
                                        <?php 
                                            $isEmpty = ($table->status == 'empty');
                                            $bgClass = $isEmpty ? 'bg-white text-dark border-success' : 'bg-danger text-white border-danger';
                                            $iconClass = $isEmpty ? 'text-success' : 'text-white';
                                            $textStatus = $isEmpty ? 'Trống' : 'Có khách';
                                            $borderClass = $isEmpty ? 'border border-2' : 'border-0';
                                        ?>
                                        <div class="col-4 col-lg-4 col-md-6">
                                            <div class="card table-box shadow-sm <?php echo $bgClass . ' ' . $borderClass; ?>" 
                                                 data-id="<?php echo $table->table_id; ?>"
                                                 title="<?php echo htmlspecialchars($table->table_name); ?>">
                                                
                                                <i class="fas <?php echo $isEmpty ? 'fa-chair' : 'fa-user'; ?> fa-2x mb-1 <?php echo $iconClass; ?>"></i>
                                                
                                                <small class="fw-bold text-truncate w-100 d-block">
                                                    <?php echo htmlspecialchars($table->table_name); ?>
                                                </small>
                                                
                                                <span class="position-absolute top-0 end-0 badge rounded-pill <?php echo $isEmpty ? 'bg-success' : 'bg-warning text-dark'; ?>" 
                                                      style="font-size: 8px; margin: 4px;">
                                                    <?php echo $textStatus; ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center text-muted p-3">Chưa có bàn nào.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 col-md-8 col-12 col-full-height">
                    <div class="card-scroll-wrapper">
                        <div class="card-header-sticky p-3 bg-light">
                            <div style="position: relative; margin-bottom: 15px;">
                                <i class="fas fa-search search-icon"></i>
                                <input id="searchProduct" type="text" class="form-control search-input-custom" placeholder="Tìm món theo tên...">
                            </div>

                            <ul class="nav nav-pills flex-nowrap category-scroll">
                                <li class="nav-item">
                                    <button class="nav-link category-pill active" onclick="filterCategory('all', this)">
                                        <i class="fas fa-th-large me-1"></i> Tất cả
                                    </button>
                                </li>
                                <?php foreach($data['categories'] as $cat): ?>
                                <li class="nav-item">
                                    <button class="nav-link category-pill" onclick="filterCategory(<?php echo $cat->category_id; ?>, this)">
                                        <?php echo htmlspecialchars($cat->category_name); ?>
                                    </button>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <div class="card-body-scroll">
                            <div class="row g-2">
                                <?php foreach($data['products'] as $product): ?>
                                    <?php if(!$product->is_available) continue; ?>
                                    
                                    <div class="col-6 col-md-4 col-lg-4 product-item" data-cat="<?php echo $product->category_id; ?>">
                                        <div class="card product-card h-100 p-0"
                                             data-id="<?php echo $product->product_id; ?>" 
                                             data-price="<?php echo $product->price; ?>">
                                            
                                            <div class="img-container">
                                                <?php if($product->image): ?>
                                                    <img src="<?php echo URLROOT . '/public/uploads/' . htmlspecialchars($product->image); ?>" class="img-product">
                                                <?php else: ?>
                                                    <div class="placeholder-icon"><i class="fas fa-camera fa-2x"></i></div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="p-2 text-center">
                                                <h6 class="product-name-text text-truncate" title="<?php echo htmlspecialchars($product->product_name); ?>">
                                                    <?php echo htmlspecialchars($product->product_name); ?>
                                                </h6>
                                                <div class="badge bg-white text-danger border border-danger rounded-pill px-2">
                                                    <?php echo number_format($product->price); ?>đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-12 col-12 col-full-height">
                    <div class="card-scroll-wrapper">
                        <div class="card-header-sticky p-3 bg-primary text-white fw-bold d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-receipt"></i> HÓA ĐƠN</span>
                            
                            <button id="btn-change-table" class="btn btn-sm btn-warning text-dark fw-bold border-white" style="display: none;">
                                <i class="fas fa-exchange-alt"></i> Chuyển
                            </button>
                        </div>
                        
                        <div class="bg-primary text-white px-3 pb-2 text-end">
                            <span id="selected-table-name" class="badge bg-white text-primary shadow-sm">
                                Chưa chọn bàn
                            </span>
                        </div>
                        
                        <div id="bill-body" class="card-body card-body-scroll bg-white">
                            <div class="text-center text-muted mt-5">
                                <i class="fas fa-shopping-basket fa-4x mb-3 text-black-50"></i>
                                <p>Vui lòng chọn bàn để gọi món</p>
                            </div>
                        </div>

                        <div class="p-3 bg-light border-top">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-tags text-primary"></i></span>
                                <input type="text" id="discount-code" class="form-control border-start-0" placeholder="Mã giảm giá...">
                                <button class="btn btn-outline-primary" type="button" id="btn-apply-discount">Áp dụng</button>
                            </div>
                            <div id="discount-info" class="text-end mt-1 small text-success fw-bold" style="display: none;">
                                - <span id="discount-value">0</span>
                            </div>
                        </div>

                        <div class="card-header-sticky p-3 bg-white border-top shadow-lg">
                            <div class="d-flex justify-content-between align-items-end mb-3">
                                <span class="fw-bold text-secondary">Tổng tiền:</span>
                                <span id="total-amount" class="fw-bold text-danger fs-2" style="line-height: 1;">0 đ</span>
                            </div>
                            
                            <button id="btn-pay" class="btn btn-success w-100 py-3 fw-bold text-uppercase shadow-sm d-flex justify-content-between align-items-center px-4" disabled>
                                <span>THANH TOÁN</span>
                                <i class="fas fa-print"></i>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="changeTableModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title fw-bold">🔄 Chuyển / Gộp Bàn</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Đang chọn chuyển từ: <strong id="lbl-from-table" class="text-primary">...</strong></p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Chọn bàn muốn chuyển đến:</label>
                        <select id="select-to-table" class="form-select"></select>
                    </div>
                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle"></i> Lưu ý: Nếu bàn đích <b>đang có khách</b>, đơn hàng sẽ được <b>GỘP</b> vào bàn đó.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" id="btn-confirm-change" class="btn btn-primary">Xác nhận chuyển</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="productOptionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-edit me-2"></i>Sửa món: <span id="optModalTitle">Tên món</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <input type="hidden" id="optOrderDetailId"> 
                    <input type="hidden" id="optOrderId">       
                    <input type="hidden" id="optBasePrice">     

                    <div class="mb-3">
                        <label class="fw-bold text-secondary mb-2">Chọn kích cỡ (Size):</label>
                        <div class="d-flex gap-3">
                            <div class="form-check custom-radio-btn flex-fill">
                                <input class="form-check-input btn-check" type="radio" name="optSize" id="sizeM" value="M" checked onchange="calcTotal()">
                                <label class="btn btn-outline-primary w-100 fw-bold" for="sizeM">Size M (+0đ)</label>
                            </div>
                            <div class="form-check custom-radio-btn flex-fill">
                                <input class="form-check-input btn-check" type="radio" name="optSize" id="sizeL" value="L" onchange="calcTotal()">
                                <label class="btn btn-outline-primary w-100 fw-bold" for="sizeL">Size L (+5.000đ)</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-secondary mb-2">Thêm Topping:</label>
                        <div class="row g-2" style="max-height: 200px; overflow-y: auto;">
                            <?php if(!empty($data['toppings'])): ?>
                                <?php foreach($data['toppings'] as $top): ?>
                                    <div class="col-6">
                                        <input type="checkbox" class="btn-check opt-topping" 
                                               id="top_<?php echo $top->product_id; ?>" 
                                               value="<?php echo htmlspecialchars($top->product_name); ?>" 
                                               data-price="<?php echo $top->price; ?>" 
                                               onchange="calcTotal()">
                                        
                                        <label class="btn btn-outline-secondary w-100 text-start d-flex justify-content-between align-items-center px-2 py-2" 
                                               for="top_<?php echo $top->product_id; ?>">
                                            <span class="text-truncate" style="font-size: 0.9rem;"><?php echo htmlspecialchars($top->product_name); ?></span>
                                            <span class="badge bg-light text-dark border ms-1">+<?php echo number_format($top->price/1000); ?>k</span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted small fst-italic">Chưa có topping. Vui lòng thêm món vào danh mục "Topping".</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mt-3 border-top pt-3">
                        <label class="fw-bold text-secondary mb-2">Ghi chú khác (Đường/Đá):</label>
                        <input type="text" id="optCustomNote" class="form-control" placeholder="VD: Ít đá, 50% đường, mang về...">
                    </div>

                </div>
                
                <div class="modal-footer bg-light d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Đơn giá mới:</small>
                        <h4 class="fw-bold text-danger m-0" id="optTotalPrice">0đ</h4>
                    </div>
                    
                    <button type="button" class="btn btn-warning text-white fw-bold px-4" onclick="updateItemSubmit()">
                        <i class="fas fa-save me-2"></i> CẬP NHẬT
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>const URLROOT = '<?php echo URLROOT; ?>';</script>
    
    <script src="<?php echo URLROOT; ?>/js/pos.js"></script>

</div>
</body>
</html>