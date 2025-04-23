<?php $this->layout("layouts/admin", ["title" => APPNAME]) ?>
<?php $this->start("page") ?>

<?php
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);
?>

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
        <h3 class="mb-0"><i class="bi bi-collection me-2"></i>Quản lý danh mục sản phẩm</h3>
    </div>

    <!-- Form tìm kiếm -->
    <div class="card mb-3 position-sticky" style="top: 72px; z-index: 100;">
        <div class="card-header bg-primary text-white text-center">
            <i class="bi bi-search me-2 fs-5"></i><strong>Tìm kiếm danh mục</strong>
        </div>
        <form action="/catalogs/search" id="search_form" method="post" class="row g-3 card-body align-items-center">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="col-md-6">
                <input type="text" class="form-control" name="id_catalog" placeholder="🔎 Theo ID danh mục..."
                    value="<?= htmlspecialchars($_POST['id_catalog'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control" name="name" placeholder="🔍 Theo tên danh mục..."
                    value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
            </div>

            <div class="col-md-12 mb-1 text-md-center">
                <div class="d-flex justify-content-between">
                    <a href="/catalogs/add" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-plus"></i> Thêm danh mục
                    </a>
                    <div class="d-inline-flex gap-2">
                        <button class="btn btn-primary btn-sm"><i class="bi bi-search"></i> Tìm kiếm</button>
                        <a href="/catalogs/admin" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-list"></i> Xem tất cả
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Bảng dữ liệu -->
    <div class="card table-responsive mb-3">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <?php if (is_array($errors)): ?>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <?= htmlspecialchars($errors) ?>
                <?php endif; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= is_array($success) ? implode('<br>', array_map('htmlspecialchars', $success)) : htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <table class="table table-hover text-center align-middle">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>ID danh mục</th>
                    <th>Tên danh mục</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($catalogs)): ?>
                    <tr>
                        <td colspan="4" class="text-muted">
                            <i class="fa fa-exclamation-circle"></i> Không có danh mục nào phù hợp.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($catalogs as $index => $catalog): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($catalog->id_catalog) ?></td>
                            <td><?= htmlspecialchars($catalog->name) ?></td>
                            <td>
                                <a href="/catalogs/update/<?= htmlspecialchars($catalog->id_catalog) ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i> Sửa
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
                if (input.value.trim() !== '') lastInput = input;
            });
            input.addEventListener('change', () => {
                if (input.value.trim() !== '') lastInput = input;
            });
        });

        form.addEventListener('submit', function (e) {
            if (!lastInput || lastInput.value.trim() === '') {
                e.preventDefault();
                alert('⚠️ Bạn chưa nhập vào thông tin tìm kiếm!');
                return;
            }

            inputs.forEach(input => {
                if (input !== lastInput) input.disabled = true;
            });
        });
    });
</script>

<?php $this->stop() ?>
