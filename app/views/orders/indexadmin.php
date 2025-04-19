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
        <h3 class="mb-0"><i class="bi bi-bag-check me-2"></i>Quản lý đơn hàng</h3>
    </div>

    <!-- Form tìm kiếm -->
    <div class="card mb-3 position-sticky" style="top: 72px; z-index: 100;">
        <div class="card-header bg-primary text-white text-center">
            <i class="bi bi-search me-2 fs-5"></i><strong>Tìm kiếm đơn hàng</strong>
        </div>
        <form action="/orders/searchadmin" id="search_form" method="post" class="row g-3 card-body align-items-center">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="col-md-4">
                <input type="text" class="form-control" name="id_order" placeholder="🔎 Theo ID đơn hàng..."
                    value="<?= $this->e($_POST['id_order'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <input type="number" min="2" class="form-control" name="id_account" placeholder="🔍 Theo ID tài khoản..."
                    value="<?= $this->e($_POST['id_account'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">🔎 Theo trạng thái</option>
                    <?php
                    $statuses = [
                        'Đã gửi đơn đặt hàng',
                        'Shop đang đóng gói đơn hàng',
                        'Đơn hàng đang giao tới bạn',
                        'Giao hàng thành công',
                        'Đơn hàng đã bị hủy'
                    ];
                    foreach ($statuses as $s):
                        $selected = (isset($_POST['status']) && $_POST['status'] == $s) ? 'selected' : '';
                        echo "<option value=\"$s\" $selected>$s</option>";
                    endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-12 mb-1 text-md-center">
                <div class="d-flex justify-content-end gap-2">
                    <button class="btn btn-primary btn-sm"><i class="bi bi-search"></i> Tìm kiếm</button>
                    <a href="/orders/admin" class="btn btn-outline-primary btn-sm"><i class="bi bi-list"></i> Xem tất
                        cả</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Bảng dữ liệu -->
    <div class="card table-responsive mb-3">
        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <?= is_array($errors) ? implode('<br>', array_map('htmlspecialchars', $errors)) : $this->e($errors) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif (isset($success) && !empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= is_array($success) ? implode('<br>', array_map('htmlspecialchars', $success)) : $this->e($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <table class="table table-hover text-center align-middle">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>ID đơn hàng</th>
                    <th>Ngày tạo</th>
                    <th>ID tài khoản</th>
                    <th>ID giao hàng</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="7" class="text-muted"><i class="fa fa-exclamation-circle"></i> Không có đơn hàng nào
                            phù hợp.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $index => $order): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= $this->e($order->id_order) ?></td>
                            <td><?= $this->e($order->created_at) ?></td>
                            <td><?= $this->e($order->id_account) ?></td>
                            <td><?= $this->e($order->id_delivery ?? '—') ?></td>
                            <td>
                                <?php
                                $status = $order->status;
                                $badgeColor = match ($status) {
                                    'Đơn hàng đã bị hủy' => '#f21b3f',
                                    'Đã gửi đơn đặt hàng' => '#08bdbd',
                                    'Giao hàng thành công' => '#08a045',
                                    default => '#ff9914',
                                };
                                ?>
                                <span class="badge text-white" style="background-color: <?= $badgeColor ?>;">
                                    <?= $this->e($status) ?>
                                </span>
                            </td>

                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="<?= '/orders/detail/' . $this->e($order->id_order) ?>"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Xem chi tiết
                                    </a>
                                    <a href="<?= '/orders/update/' . $this->e($order->id_order) ?>"
                                        class="btn btn-sm btn-primary">
                                        <i class="bi bi-pencil-square"></i> Cập nhật
                                    </a>
                                </div>
                            </td>

                        </tr>
                    <?php endforeach ?>
                <?php endif ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('search_form');
        const inputs = Array.from(form.querySelectorAll('input[name], select[name]')).filter(input => input.type !== 'hidden');
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