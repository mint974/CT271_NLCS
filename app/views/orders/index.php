<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>

<?php $this->start("page_specific_css") ?>
<link href="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.0.8/r-3.0.2/sp-2.3.1/datatables.min.css" rel="stylesheet">
<?php $this->stop() ?>

<?php $this->start("page") ?>
<style>
    :root {
        --bg-dark-green-color: #08a045;
        --bg-green-color: #29bf12;
        --bg-light-green-color: #abff4f;
        --yellow-color: #f3de2c;
        --blue-color: #08bdbd;
        --light-blue-color: #a8d5e2;
        --red-color: #f21b3f;
        --orange-color: #ff9914;
        --white-color: #FFFFFF;
        --bg-1-color: #e8fccf;
        --bg-2-color: #f5fdc6;
    }

    .box {
        background-color: var(--white-color);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
    }

    .table th {
        background-color: var(--bg-dark-green-color);
        color: var(--white-color);
    }
</style>


<div class="container box">
    <h2 class="text-center text-success">Danh Sách Đơn Hàng</h2>
    <?php if (isset($errors) && !empty($errors)): ?>

        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <?php if (is_array($errors)): ?>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars((string) $error) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php elseif (is_string($errors)): ?>
                <?= htmlspecialchars($errors) ?>
            <?php else: ?>
                <p>Lỗi không xác định.</p>
            <?php endif; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <form class="row mb-3" action="/orders/search" method="post">
        <div class="col-md-4 mb-2">
            <label for="search-date" class="form-label">Tìm theo ngày:</label>
            <input type="date" id="search-date" name="search_date" class="form-control">
        </div>

        <div class="col-md-4 mb-2">
            <label for="search-total" class="form-label">Tìm theo tổng tiền:</label>
            <select id="search-total" name="search_total" class="form-control">
                <option value="">-- Chọn khoảng giá --</option>
                <option value="under_300">Dưới 300,000 VND</option>
                <option value="between_300_800">Từ 300,000 đến 800,000 VND</option>
                <option value="above_800">Trên 800,000 VND</option>
            </select>
        </div>

        <div class="col-md-4 mb-2">
            <label for="search-status" class="form-label">Tìm theo trạng thái:</label>
            <select id="search-status" name="search_status" class="form-control">
                <option value="">-- Chọn trạng thái --</option>
                <option value="Đã gửi đơn đặt hàng">Đã gửi đơn đặt hàng</option>
                <option value="Shop đang đóng gói đơn hàng">Shop đang đóng gói đơn hàng</option>
                <option value="Đơn hàng đang giao tới bạn">Đơn hàng đang giao tới bạn</option>
                <option value="Giao hàng thành công">Giao hàng thành công</option>
                <option value="Đơn hàng đã bị hủy">Đơn hàng đã bị hủy</option>
            </select>
        </div>

        <div class="col-md-12 gap-3 d-flex align-items-end justify-content-center">

            <button type="submit" class="btn btn-success "><i class="fa fa-search"></i> Tìm kiếm</button>
            <a href="/orders/index" class="btn btn-outline-success"><i class="fa fa-list"></i> Xem tất cả</a>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th><i class="fa fa-receipt"></i> Mã Đơn Hàng</th>
                <th><i class="fa fa-calendar-alt"></i> Ngày Đặt</th>
                <th><i class="fa fa-money-bill-wave"></i> Tổng Tiền</th>
                <th><i class="fa fa-info-circle"></i> Trạng Thái</th>
                <th><i class="fa fa-cogs"></i> Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['id_order']); ?></td>
                        <td><?php echo htmlspecialchars($order['created_at']); ?></td>
                        <td><?php echo number_format($order['total_price'], 0, ',', '.') . ' VND'; ?></td>
                        <td><?php echo htmlspecialchars($order['status']); ?></td>
                        <td>
                            <a href="/orders/order_detail/<?= $order['id_order'] ?>" class="btn btn-outline-primary btn-sm ">
                                <i class="fa fa-eye"></i> Xem chi tiết
                            </a>
                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal"
                                onclick="setCancelData('<?= $order['id_order'] ?>', '<?= $order['total_price'] ?>', '<?= $order['status'] ?>')">
                                <i class="fa fa-times-circle"></i> Hủy đơn
                            </button>
                            <?php if ($order['status'] == 'Đã gửi đơn đặt hàng'): ?>

                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center text-muted">
                        <i class="fa fa-exclamation-circle"></i> Không có đơn hàng nào.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
        <!-- Modal Hủy Đơn Hàng -->
        <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="cancelModalLabel">Xác nhận hủy đơn hàng</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Mã đơn:</strong> <span id="modalOrderId"></span></p>
                        <p><strong>Tổng tiền:</strong> <span id="modalTotalPrice"></span></p>
                        <p><strong>Trạng thái:</strong> <span id="modalStatus"></span></p>
                        <div class="d-flex justify-content-between">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reasonModal"
                                data-bs-dismiss="modal">Tiếp tục</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Chọn Lý Do Hủy -->
        <div class="modal fade" id="reasonModal" tabindex="-1" aria-labelledby="reasonModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title" id="reasonModalLabel">Chọn lý do hủy đơn</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="/orders/cancel" method="post">
                            <input type="text" class="hidden" name="id_order" id="modalOrderId2">
                            <select class="form-select" name="reason" id="cancelReason">
                                <option>Tôi thay đổi ý định, không muốn mua nữa.</option>
                                <option>Tôi tìm thấy giá tốt hơn ở nơi khác.</option>
                                <option>Tôi đặt nhầm sản phẩm hoặc số lượng.</option>
                                <option>Tôi muốn thay đổi địa chỉ nhận hàng.</option>
                                <option>Lý do khác.</option>
                            </select>
                            <div class="mt-3 d-flex justify-content-between">
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-danger">Gửi yêu cầu hủy</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </table>


</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form');
        const inputs = document.querySelectorAll('input, select');

        // Xóa các trường khác khi nhập vào một trường
        inputs.forEach(input => {
            input.addEventListener('input', function () {
                inputs.forEach(other => {
                    if (other !== this) {
                        other.value = ''; // Xóa giá trị của các trường khác
                    }
                });
            });

            // Xử lý riêng cho input type="date"
            if (input.type === 'date') {
                input.addEventListener('change', function () {
                    inputs.forEach(other => {
                        if (other !== this) {
                            other.value = '';
                        }
                    });
                });
            }
        });

        // Kiểm tra trước khi submit
        form.addEventListener('submit', function (e) {
            let hasValue = false;

            inputs.forEach(input => {
                if (input.value.trim() !== '') {
                    hasValue = true;
                }
            });

            if (!hasValue) {
                e.preventDefault();
                alert('Vui lòng nhập ít nhất một tiêu chí để tìm kiếm.');
            }
        });
    });

    function setCancelData(id, total, status) {
        document.getElementById("modalOrderId").textContent = id;
        document.getElementById("modalTotalPrice").textContent = total;
        document.getElementById("modalStatus").textContent = status;
        document.getElementById("modalOrderId2").value = id;
    }

</script>
<?php $this->stop() ?>