<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>

<?php $this->start("page_specific_css") ?>
<link href="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.0.8/r-3.0.2/sp-2.3.1/datatables.min.css" rel="stylesheet">
<?php $this->stop() ?>

<?php $this->start("page") ?>

<style>
    .cart-summary {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }
    .action-btns .btn {
        margin: 2px;
    }
</style>

<div class="container shopping-card cart-summary">
    <div class="card p-2">
        <h2 class="text-center mb-4 mt-2 text-success"><strong>GIỎ HÀNG CỦA BẠN</strong></h2>
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead class="table-success">
                    <tr>
                        <th>Hình ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Đơn giá</th>
                        <th>Khuyến mãi</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    
                    <tr>
                        <td><img src="product.jpg" alt="Sản phẩm" width="70"></td>
                        <td></td>
                        <td>50,000 VND</td>
                        <td>10%</td>
                        <td>1</td>
                        <td>45,000 VND</td>
                        <td class="action-btns">
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal">✏️ Sửa</button>
                            <button class="btn btn-danger btn-sm" onclick="confirmDelete(1)">&times; Xóa</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="cart-summary text-center mt-4">
        <h4>Tổng tiền tạm tính: <span class="text-danger">45,000 VND</span></h4>
        <button class="btn btn-primary mt-3">Tiến hành thanh toán</button>
    </div>
</div>

<!-- Modal chỉnh sửa số lượng -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModalLabel">Chỉnh sửa số lượng</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="editQuantity">Số lượng:</label>
                <input type="number" id="editQuantity" class="form-control" min="1">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="saveQuantity()">Lưu</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            </div>
        </div>
    </div>
</div>

<script>
    function saveQuantity() {
        alert("Số lượng đã được cập nhật!");
        var myModalEl = document.getElementById('editModal');
        var modal = bootstrap.Modal.getInstance(myModalEl);
        modal.hide();
    }

    function confirmDelete(id) {
        if (confirm("Bạn có chắc chắn muốn xóa sản phẩm này không?")) {
            alert("Sản phẩm đã bị xóa!");
        }
    }
</script>

<?php $this->stop() ?>