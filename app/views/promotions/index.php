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

    .chart-container {
        width: 300px;
        height: auto;
    }
</style>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0"><i class="bi bi-percent me-2"></i>Quản lý khuyến mãi</h3>
    </div>

    <!-- Form tìm kiếm -->
    <div class="card mb-3 position-sticky" style="top: 72px; z-index: 100;">
        <div class="card-header bg-primary text-white text-center">
            <i class="bi bi-search me-2 fs-5"></i><strong>Tìm kiếm khuyến mãi</strong>
        </div>
        <form action="/promotions/search" id="search_form" method="post" class="row g-3 card-body align-items-center">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="col-md-3">
                <input type="text" class="form-control" name="id_promotion" placeholder="🔎 Theo ID khuyến mãi..."
                    value="<?= $this->e($_POST['id_promotion'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" name="name" placeholder="🔍 Theo tên khuyến mãi..."
                    value="<?= $this->e($_POST['name'] ?? '') ?>">
            </div>

            <div class="col-md-3">
                <input type="date" class="form-control" name="start_day" placeholder="🔎 Theo ngày bắt đầu"
                    value="<?= $this->e($_POST['start_day'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <input type="date" class="form-control" name="end_day" placeholder="🔍 Theo ngày kết thúc"
                    value="<?= $this->e($_POST['end_day'] ?? '') ?>">
            </div>

            <div class="col-md-12 mb-1 mb-1 text-md-center">

                <div class="d-flex justify-content-between">

                    <a href="/promotion/add" class="btn btn-outline-success btn-sm"><i class="bi bi-plus"></i>Thêm
                        khuyến mãi</a>
                    <div class="d-inline-flex gap-2">
                        <button class="btn btn-primary btn-sm"><i class="bi bi-search"></i> Tìm kiếm</button>
                        <a href="/promotion/admin" class="btn btn-outline-primary btn-sm"><i class="bi bi-list"></i> Xem
                            tất
                            cả</a>
                    </div>

                </div>
            </div>
        </form>
    </div>

    <!-- Bảng dữ liệu -->
    <div class="card table-responsive mb-3">
        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <?php if (is_array($errors)): ?>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= $this->e((string) $error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php elseif (is_string($errors)): ?>
                    <?= $this->e($errors) ?>
                <?php else: ?>
                    <p>Lỗi không xác định.</p>
                <?php endif; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php else: ?>
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
            <table class="table table-hover text-center align-middle">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>ID</th>
                        <th>Tên khuyến mãi</th>
                        <th>Mô tả</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày kết thúc</th>
                        <th>Giảm (%)</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($promotions)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                <i class="fa fa-exclamation-circle"></i> Không có khuyến mãi nào phù hợp.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($promotions as $index => $promotion): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= $this->e($promotion->id_promotion) ?></td>
                                <td><?= $this->e($promotion->name) ?></td>
                                <td><?= $this->e($promotion->description) ?></td>
                                <td><?= $this->e($promotion->start_day) ?></td>
                                <td><?= $this->e($promotion->end_day) ?></td>
                                <td><span class="badge bg-success"><?= $this->e($promotion->discount_rate) ?>%</span></td>
                                <td>
                                    <a href="/promotions/update/<?= $this->e($promotion->id_promotion) ?>"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Sửa
                                    </a>

                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('search_form');
        const inputs = Array.from(form.querySelectorAll('input[name]:not([type="hidden"])'));

        let lastInput = null;

        // Theo dõi input cuối cùng được người dùng thay đổi
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

            // Chỉ cho phép gửi duy nhất input cuối cùng
            inputs.forEach(input => {
                if (input !== lastInput) {
                    input.disabled = true;
                }
            });
        });
    });
</script>


<?php $this->stop() ?>