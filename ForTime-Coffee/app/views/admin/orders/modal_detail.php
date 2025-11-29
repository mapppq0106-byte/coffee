<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-receipt"></i> Chi tiết đơn hàng <span id="modalOrderId">#</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3 border-bottom pb-2">
                    <div class="col-6">
                        <p><strong>Thu ngân:</strong> <span id="modalStaffName">...</span></p>
                        <p><strong>Thời gian:</strong> <span id="modalTime">...</span></p>
                    </div>
                    <div class="col-6 text-end">
                        <h4 class="text-danger fw-bold" id="modalTotal">0đ</h4>
                        <span class="badge bg-success">Đã thanh toán</span>
                    </div>
                </div>
                
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Tên món</th>
                            <th class="text-center">SL</th>
                            <th class="text-end">Đơn giá</th>
                            <th class="text-end">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody id="modalOrderItems">
                        </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary"><i class="fas fa-print"></i> In lại hóa đơn</button>
            </div>
        </div>
    </div>
</div>