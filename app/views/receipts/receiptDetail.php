<?php $this->layout("layouts/admin", ["title" => APPNAME]) ?>
<?php $this->start("page") ?>

<style>
    .card {
        border-radius: 1rem;
        transition: 0.3s;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }

    .table th {
        background-color: #0d6efd;
        color: white;
        vertical-align: middle;
    }

    .table td {
        vertical-align: middle;
    }

    .back-btn {
        position: relative;
        top: -10px;
        margin-bottom: 1rem;
    }

    .card-header i {
        font-size: 1.2rem;
    }
</style>

<div class="container my-5">
    <h3 class="text-center mb-4 fw-bold text-primary">
        <i class="bi bi-clipboard-check me-2"></i>Chi tiết Phiếu Nhập
    </h3>

    <!-- Nút quay lại -->
    <div class="mb-3 back-btn">
        <a href="/receipt/index" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>

    <!-- Thẻ thông tin -->
    <div class="row row-cols-1 row-cols-md-3 g-4 mb-4">
        <!-- Phiếu Nhập -->
        <div class="col">
            <div class="card h-100 shadow-sm border-0 bg-light p-4 position-relative">
                <div class="border-start border-primary border-4 ps-3">
                    <h5 class="text-primary mb-3">
                        <i class="bi bi-file-earmark-text-fill me-2"></i>Phiếu Nhập
                    </h5>
                    <p class="mb-1"><strong>Mã phiếu:</strong> <?= htmlspecialchars($receipt->id_receipt) ?></p>
                    <p class="mb-0"><strong>Ngày tạo:</strong> <?= htmlspecialchars($receipt->created_at) ?></p>
                </div>
            </div>
        </div>

        <!-- Nhà Cung Cấp -->
        <div class="col">
            <div class="card h-100 shadow-sm border-0 bg-light p-4 position-relative">
                <div class="border-start border-success border-4 ps-3">
                    <h5 class="text-success mb-3">
                        <i class="bi bi-truck-front-fill me-2"></i>Nhà Cung Cấp
                    </h5>
                    <p class="mb-1"><strong>Mã NCC:</strong> <?= htmlspecialchars($supplier->id_supplier) ?></p>
                    <p class="mb-0"><strong>Tên NCC:</strong> <?= htmlspecialchars($supplier->name) ?></p>
                </div>
            </div>
        </div>

        <!-- Nhân viên nhập -->
        <div class="col">
            <div class="card h-100 shadow-sm border-0 bg-light p-4 position-relative">
                <div class="border-start border-info border-4 ps-3">
                    <h5 class="text-info mb-3">
                        <i class="bi bi-person-vcard-fill me-2"></i>Nhân viên nhập
                    </h5>
                    <div class="d-flex align-items-start">
                        <img src="/<?= htmlspecialchars($user->url) ?>" alt="Avatar" width="60"
                            class="rounded-circle border me-3 shadow-sm">
                        <div class="small">
                            <div><strong>ID:</strong> <?= htmlspecialchars($user->id_account) ?></div>
                            <div><strong>User:</strong> <?= htmlspecialchars($user->username) ?></div>
                            <div><strong>Email:</strong> <?= htmlspecialchars($user->email) ?></div>
                            <div><strong>Vai trò:</strong> <?= htmlspecialchars($user->role) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Danh sách sản phẩm -->
    <div class="card">
        <div class="card-header bg-warning text-dark fw-bold">
            <i class="bi bi-boxes me-2"></i>Danh sách sản phẩm
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle text-center mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Hình ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Đơn giá nhập</th>
                        <th>Đơn giá bán</th>
                        <th>Đơn vị</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    $index = 1;
                    foreach ($ProductReceiptDetails as $detail):
                        $prd = $detail['product'];
                        $receiptDetail = $detail['ProductReceiptDetail'];
                        $lineTotal = $receiptDetail->quantity * $receiptDetail->purchase_price;
                        $total += $lineTotal;
                        ?>
                        <tr>
                            <td><?= $index++ ?></td>
                            <td>
                                <?php if (!empty($prd->images[0])): ?>
                                    <img src="<?= htmlspecialchars($prd->images[0]) ?>" width="60" class="rounded">
                                <?php else: ?>
                                    <span class="text-muted">Không có ảnh</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($prd->name) ?></td>
                            <td><?= $receiptDetail->quantity ?></td>
                            <td><?= number_format($receiptDetail->purchase_price, 0, ',', '.') ?> đ</td>
                            <td><?= number_format($prd->price, 0, ',', '.') ?> đ</td>
                            <td><?= htmlspecialchars($prd->unit) ?></td>
                            <td><?= number_format($lineTotal, 0, ',', '.') ?> đ</td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="table-success fw-bold">
                        <td colspan="7" class="text-end">Tổng cộng:</td>
                        <td><?= number_format($total, 0, ',', '.') ?> đ</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $this->stop() ?>