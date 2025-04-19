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

    <?php if (isset($success) && !empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php if (is_array($success)): ?>
                <ul>
                    <?php foreach ($success as $message): ?>
                        <li><?= htmlspecialchars((string) $message) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php elseif (is_string($success)): ?>
                <?= htmlspecialchars($success) ?>
            <?php else: ?>
                <p>Hoàn thành thành công.</p>
            <?php endif; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <form class="row mb-3" id="searchForm" action="/orders/search" method="post">
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
                <th><i class="fa fa-info-circle"></i> Phương thức thanh toán</th>
                <th><i class="fa fa-cogs"></i>Trạng thái thanh toán</th>
                <th><i class="fa fa-info-circle"></i> Trạng Thái đơn hàng</th>
                <th><i class="fa fa-cogs"></i> Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>
                            <i class="fa fa-receipt text-secondary"></i>
                            <?= htmlspecialchars($order['id_order']) ?>
                        </td>

                        <td>
                            <i class="fa fa-clock text-muted"></i>
                            <?= htmlspecialchars($order['created_at']) ?>
                        </td>

                        <td>
                            <i class="fa fa-money-bill-wave text-success"></i>
                            <?= number_format($order['total_price'], 0, ',', '.') . ' VND' ?>
                        </td>

                        <td>
                            <i class="fa fa-credit-card text-primary"></i>
                            <?= htmlspecialchars($order['payment_method']) ?>
                        </td>

                        <td>
                            <?php
                            $status = $order['payment_status'];
                            $badgeClass = '';
                            $icon = '';

                            switch ($status) {
                                case 'Đã thanh toán':
                                    $badgeClass = 'success';
                                    $icon = 'fa-check-circle';
                                    break;
                                case 'Thất bại':
                                    $badgeClass = 'danger';
                                    $icon = 'fa-times-circle';
                                    break;
                                default:
                                    $badgeClass = 'warning';
                                    $icon = 'fa-clock';
                            }
                            ?>
                            <span class="badge bg-<?= $badgeClass ?>">
                                <i class="fa <?= $icon ?>"></i> <?= htmlspecialchars($status) ?>
                            </span>
                        </td>

                        <td>
                            <?php
                            $orderStatus = $order['status'];
                            $statusClass = 'secondary';
                            $statusIcon = 'fa-box';

                            if ($orderStatus === 'Đã gửi đơn đặt hàng') {
                                $statusClass = 'info';
                                $statusIcon = 'fa-paper-plane';
                            } elseif ($orderStatus === 'Đang giao hàng') {
                                $statusClass = 'primary';
                                $statusIcon = 'fa-truck';
                            } elseif ($orderStatus === 'Đã nhận hàng') {
                                $statusClass = 'success';
                                $statusIcon = 'fa-check';
                            } elseif ($orderStatus === 'Đã hủy') {
                                $statusClass = 'danger';
                                $statusIcon = 'fa-times';
                            }
                            ?>
                            <span class="badge bg-<?= $statusClass ?>">
                                <i class="fa <?= $statusIcon ?>"></i> <?= htmlspecialchars($orderStatus) ?>
                            </span>
                        </td>

                        <td>
                            <a href="/orders/order_detail/<?= $order['id_order'] ?>" class="btn btn-outline-primary btn-sm">
                                <i class="fa fa-eye"></i> Xem chi tiết
                            </a>

                            <?php if ($order['status'] === 'Đã gửi đơn đặt hàng' && $order['payment_method'] !== 'Online'): ?>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal"
                                    onclick="setCancelData('<?= $order['id_order'] ?>', '<?= $order['total_price'] ?>', '<?= $order['status'] ?>')">
                                    <i class="fa fa-times-circle"></i> Hủy đơn
                                </button>
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


    </table>

    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Header -->
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="cancelModalLabel">Xác nhận hủy đơn hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <!-- Bước 1: Xác nhận thông tin -->
                    <div id="cancelStep1">
                        <p><strong>Mã đơn:</strong> <span id="modalOrderId"></span></p>
                        <p><strong>Tổng tiền:</strong> <span id="modalTotalPrice"></span></p>
                        <p><strong>Trạng thái:</strong> <span id="modalStatus"></span></p>
                        <div class="d-flex justify-content-between">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button class="btn btn-primary" id="nextToReasonBtn">Tiếp tục</button>
                        </div>
                    </div>

                    <!-- Bước 2: Chọn lý do -->
                    <div id="cancelStep2" style="display:none;">
                        <form action="/orders/cancel" method="post">
                            <input type="hidden" name="id_order" id="modalOrderId2">
                            <div class="mb-3">
                                <label for="cancelReason" class="form-label">Chọn lý do hủy đơn</label>
                                <select class="form-select" name="reason" id="cancelReason" required>
                                    <option value="">-- Chọn lý do --</option>
                                    <option value="Tôi thay đổi ý định, không muốn mua nữa.">Tôi thay đổi ý định
                                    </option>
                                    <option value="Tôi tìm thấy giá tốt hơn ở nơi khác.">Giá tốt hơn ở nơi khác
                                    </option>
                                    <option value="Tôi đặt nhầm sản phẩm hoặc số lượng.">Đặt nhầm sản phẩm</option>
                                    <option value="Tôi muốn thay đổi địa chỉ nhận hàng.">Thay đổi địa chỉ</option>
                                    <option value="Lý do khác.">Lý do khác</option>
                                </select>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary" id="backToStep1">Quay lại</button>
                                <button type="submit" class="btn btn-danger">Gửi yêu cầu hủy</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Chỉ lấy form tìm kiếm
        const searchForm = document.querySelector('#searchForm');
        const searchInputs = searchForm.querySelectorAll('input, select');

        // Giới hạn chọn 1 tiêu chí tìm kiếm
        searchInputs.forEach(input => {
            input.addEventListener('input', function () {
                searchInputs.forEach(other => {
                    if (other !== this) {
                        other.value = '';
                    }
                });
            });

            if (input.type === 'date') {
                input.addEventListener('change', function () {
                    searchInputs.forEach(other => {
                        if (other !== this) {
                            other.value = '';
                        }
                    });
                });
            }
        });

        searchForm.addEventListener('submit', function (e) {
            let hasValue = false;
            searchInputs.forEach(input => {
                if (input.value.trim() !== '') {
                    hasValue = true;
                }
            });

            if (!hasValue) {
                e.preventDefault();
                alert('Vui lòng nhập ít nhất một tiêu chí để tìm kiếm.');
            }
        });

        // Phần xử lý cancel modal giữ nguyên
        window.setCancelData = function (id, total, status) {
            document.getElementById("modalOrderId").textContent = id;
            document.getElementById("modalTotalPrice").textContent = Number(total).toLocaleString('vi-VN') + " VND";
            document.getElementById("modalStatus").textContent = status;
            document.getElementById("modalOrderId2").value = id;

            // Reset modal về bước 1
            document.getElementById("cancelStep1").style.display = "block";
            document.getElementById("cancelStep2").style.display = "none";
        };

        document.getElementById("nextToReasonBtn").addEventListener('click', function () {
            document.getElementById("cancelStep1").style.display = "none";
            document.getElementById("cancelStep2").style.display = "block";
        });

        document.getElementById("backToStep1").addEventListener('click', function () {
            document.getElementById("cancelStep1").style.display = "block";
            document.getElementById("cancelStep2").style.display = "none";
        });
    });

</script>


<?php $this->stop() ?>