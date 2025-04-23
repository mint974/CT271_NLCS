<?php $this->layout("layouts/admin", ["title" => APPNAME]) ?>
<?php $this->start("page") ?>

<style>
    .card {
        border-radius: 16px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    }

    .card:hover {
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .table th {
        background-color: #0d6efd;
        color: white;
        vertical-align: middle;
    }

    .table td {
        vertical-align: middle;
    }
</style>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0"><i class="bi bi-box-seam me-2"></i>Quản lý Phiếu Nhập</h3>
    </div>

    <!-- Form tìm kiếm -->
    <div class="card mb-3 position-sticky" style="top: 72px; z-index: 100;">
        <div class="card-header bg-primary text-white text-center">
            <i class="bi bi-search me-2 fs-5"></i><strong>Tìm kiếm phiếu nhập</strong>
        </div>
        <?php if (isset($success) && !empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <!-- thông báo lỗi -->
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
        <form action="/receipts/search" id="search_form" method="post" class="row g-3 card-body align-items-center">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="col-md-4">
                <input type="text" class="form-control" name="id_receipt" placeholder="🔎 Theo mã phiếu..."
                    value="<?= htmlspecialchars($_POST['id_receipt'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control" name="supplier_name" placeholder="🔍 Theo tên nhà cung cấp..."
                    value="<?= htmlspecialchars($_POST['supplier_name'] ?? '') ?>">
            </div>

            <div class="col-md-4">
                <input type="date" class="form-control" name="receipt_date" placeholder="🔎 Theo ngày nhập"
                    value="<?= htmlspecialchars($_POST['receipt_date'] ?? '') ?>">
            </div>

            <div class="col-md-12 mb-1 text-md-center">
                <div class="d-flex justify-content-between">
                    <div class="d-inline-flex gap-2">
                        <a href="/receipt/add" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-plus-circle"></i> Nhập hàng mới
                        </a>
                        <a href="/receipt/update" class="btn btn-success btn-sm">
                            <i class="bi bi-plus-circle"></i> Nhập hàng đang kinh doanh
                        </a>
                    </div>
                    <div class="d-inline-flex gap-2">
                        <button class="btn btn-primary btn-sm"><i class="bi bi-search"></i> Tìm kiếm</button>
                        <a href="/receipt/index" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-list"></i> Xem tất cả
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Bảng dữ liệu -->
    <div class="card table-responsive mb-3">
        <table class="table table-hover text-center align-middle">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Mã phiếu</th>
                    <th>Mã nhà cung cấp</th>
                    <th>Tên nhà cung cấp</th>
                    <th>Ngày nhập</th>
                    <th>Người nhập</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($receipts)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            <i class="bi bi-exclamation-circle"></i> Không có phiếu nhập nào phù hợp.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($receipts as $index => $item): ?>
                        <?php $receipt = $item['receipt']; ?>
                        <?php $supplier = $item['supplier']; ?>

                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td class="fw-bold text-success"><?= htmlspecialchars($receipt->id_receipt) ?></td>
                            <td><?= htmlspecialchars($receipt->id_supplier) ?></td>
                            <td><?= htmlspecialchars($supplier->name) ?></td>
                            <td><?= htmlspecialchars($receipt->created_at) ?></td>
                            <td><?= htmlspecialchars($receipt->id_account) ?></td>
                            <td>
                                <a href="/receipts/detail/<?= htmlspecialchars($receipt->id_receipt) ?>"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Xem thêm
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('search_form');
        const inputs = Array.from(form.querySelectorAll('input[name]:not([type="hidden"])'));

        let lastInput = null;


        inputs.forEach(input => {
            input.addEventListener('input', () => {
                if (input.value.trim() !== '') {
                    lastInput = input;
                }
            });
            input.addEventListener('change', () => {
                if (input.value.trim() !== '') {
                    lastInput = input;
                }
            });
        });

        form.addEventListener('submit', function (e) {

            if (!lastInput || lastInput.value.trim() === '') {
                e.preventDefault();
                alert('⚠️ Bạn chưa nhập vào thông tin tìm kiếm!');
                return;
            }


            inputs.forEach(input => {
                if (input !== lastInput) {
                    input.disabled = true;
                }
            });
        });
    });
</script>

<?php $this->stop() ?>