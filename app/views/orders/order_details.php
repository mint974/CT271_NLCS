<?php
if (AUTHGUARD()->user()->role === 'khách hàng') {
    $this->layout("layouts/default", ["title" => APPNAME]);
} else {
    $this->layout("layouts/admin", ["title" => APPNAME]);
}
?>

<?php $this->start("page_specific_css") ?>
<link href="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.0.8/r-3.0.2/sp-2.3.1/datatables.min.css" rel="stylesheet">
<?php $this->stop() ?>

<?php $this->start("page") ?>

<style>
    .box {
        background-color: #FFFFFF;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
    }

    .table th,
    .card-header {
        background-color: #08a045;
        color: white;
    }

    h3 {
        color: #08a045;
    }

    .order-summary {
        font-size: 1.2rem;
        font-weight: bold;
        color: #d9534f;
    }

    .alert {
        font-size: 1.1rem;
        font-weight: 500;
    }
</style>

<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header text-white text-center">
            <h2><i class="fa fa-file-invoice"></i> Chi Tiết Đơn Hàng</h2>
        </div>
        <div class="card-body">

            <div class="mb-4 row d-flex justify-content-center">
                <!-- Giao hàng -->
                <div class="box col-lg-4 col-md-6 m-2">
                    <h3 class="text-center"><i class="fa fa-truck"></i> Thông Tin Giao Hàng</h3>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Tên người nhận:</strong>
                            <?php echo htmlspecialchars($orderInfo->receiver_name); ?>
                        </li>
                        <li class="list-group-item"><strong>Số điện thoại:</strong>
                            <?php echo htmlspecialchars($orderInfo->receiver_phone); ?>
                        </li>
                        <li class="list-group-item"><strong>Địa chỉ:</strong>
                            <?php echo htmlspecialchars($orderInfo->house_number . ', ' . $orderInfo->ward . ', ' . $orderInfo->district . ', ' . $orderInfo->city); ?>
                        </li>
                        <li class="list-group-item"><strong>Phí giao hàng:</strong>
                            <?php echo number_format($orderInfo->shipping_fee, 0, ',', '.') . ' VND'; ?>
                        </li>
                    </ul>
                </div>

                <!-- Thanh toán -->
                <?php if (isset($payment)): ?>
                    <div class="box col-lg-3 col-md-5 m-2">
                        <h3 class="text-center"><i class="fa fa-credit-card"></i> Thông Tin Thanh Toán</h3>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Mã thanh toán:</strong>
                                <?php echo htmlspecialchars($payment->id_payment); ?>
                            </li>
                            <li class="list-group-item"><strong>Phương thức:</strong>
                                <?php echo htmlspecialchars($payment->payment_method); ?>
                            </li>
                            <li class="list-group-item"><strong>Trạng thái:</strong>
                                <span
                                    class="text-success fw-bold"><?php echo htmlspecialchars($payment->payment_status); ?></span>
                            </li>
                            <li class="list-group-item"><strong>Mã giao dịch:</strong>
                                <?php echo htmlspecialchars($payment->transaction_code); ?>
                            </li>
                            <li class="list-group-item"><strong>Thời gian thanh toán:</strong>
                                <?php echo date('H:i:s d/m/Y', strtotime($payment->payment_time)); ?>
                            </li>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Trạng thái & Tổng tiền -->
                <div class="box col-lg-3 col-md-5 m-2">
                    <div class="text-center mb-3">
                        <h3><i class="fa fa-receipt"></i> Trạng Thái Đơn Hàng</h3>
                        <div class="alert 
                <?php echo ($order->status === 'Giao hàng thành công') ? 'alert-success' :
                    (($order->status === 'Đơn hàng đã bị hủy') ? 'alert-danger' :
                        'alert-secondary'); ?>">
                            <?php echo htmlspecialchars($order->status); ?>
                        </div>
                    </div>

                    <hr>

                    <div class="text-center mt-3">
                        <h3><i class="fa fa-money-check-alt"></i> Tổng Đơn Hàng</h3>
                        <p class="order-summary text-danger fs-4 fw-bold">
                            <?php echo number_format($totalPrice, 0, ',', '.') . ' VND'; ?>
                        </p>
                    </div>
                </div>
            </div>


            <!-- Danh sách sản phẩm -->
            <div class="box">
                <h3 class="mb-3 text-center"><i class="fa fa-shopping-cart"></i> Sản Phẩm Trong Đơn Hàng</h3>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><i class="fa fa-image"></i> Hình Ảnh</th>
                            <th><i class="fa fa-box"></i> Tên Sản Phẩm</th>
                            <th class="text-center"><i class="fa fa-sort-numeric-up"></i> Số Lượng</th>
                            <th class="text-end"><i class="fa fa-money-bill-wave"></i> Đơn Giá</th>
                            <th class="text-center"><i class="fa fa-tag"></i> Giảm Giá</th>
                            <th class="text-end"><i class="fa fa-tags"></i> Giá Sau Giảm</th>
                            <th class="text-end"><i class="fa fa-coins"></i> Tổng Tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderProducts as $item): ?>
                            <tr>
                                <td>
                                    <img src="<?php echo htmlspecialchars($item['image'] ?? 'default.jpg'); ?>" width="50"
                                        height="50" class="rounded" alt="Sản phẩm">
                                </td>
                                <td><?php echo htmlspecialchars($item['product_name'] ?? 'Không xác định'); ?></td>
                                <td class="text-center"><?php echo htmlspecialchars($item['quantity'] ?? 0); ?></td>
                                <td class="text-end">
                                    <?php echo number_format((float) ($item['price'] ?? 0), 0, ',', '.') . ' VND'; ?>
                                </td>
                                <td class="text-center text-success">
                                    <?php echo ((float) $item['discount_rate'] > 0) ? ('-' . rtrim(rtrim($item['discount_rate'], '0'), '.') . '%') : 'Không'; ?>
                                </td>
                                <td class="text-end">
                                    <?php echo number_format((float) ($item['discount_price'] ?? 0), 0, ',', '.') . ' VND'; ?>
                                </td>
                                <td class="text-end text-danger order-summary">
                                    <?php echo number_format((float) ($item['total_price'] ?? 0), 0, ',', '.') . ' VND'; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>

            </div>

            <?php if (AUTHGUARD()->user()->role === 'khách hàng'): ?>
                <div class="d-flex justify-content-center mt-3">
                    <a href="/orders/index" class="btn btn-success ">Quay lại</a>
                </div>

            <?php else: ?>
                <div class="d-flex justify-content-center mt-3">
                    <a href="/orders/admin" class="btn btn-success ">Quay lại</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $this->stop() ?>