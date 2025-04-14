<?php $this->layout("layouts/admin", ["title" => APPNAME]) ?>
<?php $this->start("page") ?>

<style>
    .card {
        border-radius: 16px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    }

    .card:hover {
        transform: translateY(-4px);
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

    .product-img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #ddd;
    }

    .legend-dot {
        display: inline-block;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        margin-right: 8px;
        vertical-align: middle;
    }

    .chart-container {
        width: 300px;
        height: auto;

    }
</style>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0"><i class="bi bi-box-seam me-2"></i>Quản lý sản phẩm</h3>
    </div>

    <!-- Thống kê -->
    <div class="card mb-3 <?php if (empty($promotionCounts) || !empty($_POST)): ?>d-none<?php endif; ?>">
        <div class="card-header bg-primary text-white text-center">
            <i class="bi bi-pie-chart-fill me-2 fs-5"></i>
            <strong>Thống kê tình trạng khuyến mãi</strong>
        </div>
        <div class="card-body d-flex justify-content-center">
            <div class="chart-container text-center">
                <canvas id="promotionChart"></canvas>
                <p class="fs-6 mt-3 mb-3"><strong>BIỂU ĐỒ KHUYẾN MÃI</strong></p>
            </div>
        </div>
    </div>

    <!-- Form tìm kiếm -->
    <div class="card mb-3 position-sticky" style="top: 72px; z-index: 100;">
        <div class="card-header bg-primary text-white text-center">
            <i class="bi bi-search me-2 fs-5"></i><strong>Tìm kiếm</strong>
        </div>
        <form action="/products/search" method="post" class="row g-3 card-body align-items-center">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="col-md-3">
                <input type="text" class="form-control" name="id_product" placeholder="🔎 Theo ID sản phẩm..."
                    value="<?= htmlspecialchars($_POST['id_product'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" name="name_product" placeholder="🍎 Theo tên sản phẩm..."
                    value="<?= htmlspecialchars($_POST['name_product'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <select class="form-control" name="promotion">
                    <option value="">Lọc theo khuyến mãi...</option>
                    <option value="on" <?= ($_POST['promotion'] ?? '') === 'on' ? 'selected' : '' ?>>Có khuyến mãi</option>
                    <option value="off" <?= ($_POST['promotion'] ?? '') === 'off' ? 'selected' : '' ?>>Không có khuyến mãi
                    </option>
                </select>
            </div>
            <div class="col-md-3 text-md-center">
                <div class="d-inline-flex gap-2">
                    <button class="btn btn-primary btn-sm"><i class="bi bi-search"></i> Tìm kiếm</button>
                    <a href="/products/admin" class="btn btn-outline-primary btn-sm"><i class="bi bi-list"></i> Xem tất
                        cả</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Bảng dữ liệu sản phẩm -->
    <div class="card table-responsive">
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

        <?php else: ?>

            <table class="table table-hover text-center align-middle">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>ID</th>
                        <th>Hình ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Số lượng trong kho</th>
                        <th>Giá / dvt</th>
                        <th>Khuyến mãi</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $index => $product): ?>
                        <?php
                        // Xử lý hình ảnh
                        $images = explode(',', $product['images'] ?? '');
                        $firstImage = !empty($images[0]) ? $images[0] : '';
                        ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($product['id_product']) ?></td>
                            <td><img src="<?= $firstImage ?>" class="product-img" alt="Ảnh"></td>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td><?= htmlspecialchars($product['quantity']) ?></td>
                            <td><?= htmlspecialchars($product['price']) ?> đ / <?= htmlspecialchars($product['unit']) ?></td>

                            <td>
                                <?php if (!empty($product['id_promotion'])): ?>
                                    <span class="badge bg-success">Đang khuyến mãi</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Không</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <a href="<?= '/products/proddetail/' . $this->e($product['id_product']) ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Xem thêm
                                </a>
                                <a href="/products/update/<?= $product['id_product'] ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i> Chỉnh sửa
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if ($totalPages > 1 && empty($_POST)): ?>
                <nav class="mt-4 d-flex justify-content-center">
                    <ul class="pagination">
                        <?php
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($totalPages, $currentPage + 2);

                        if ($currentPage <= 3) {
                            $endPage = min(5, $totalPages);
                        }

                        if ($currentPage >= $totalPages - 2) {
                            $startPage = max(1, $totalPages - 4);
                        }
                        ?>

                        <!-- Nút lùi -->
                        <li class="page-item <?= $currentPage == 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $currentPage - 1 ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>

                        <!-- Các nút trang -->
                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <!-- Nút tiến -->
                        <li class="page-item <?= $currentPage == $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $currentPage + 1 ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</div>

<!-- Chart script -->
<!-- JavaScript xử lý form thông minh -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector("form[action='/products/search']");
        const inputs = form.querySelectorAll("input[type='text'], select");

        let lastModifiedInput = null;

        // Theo dõi input cuối cùng người dùng tương tác
        inputs.forEach(input => {
            input.addEventListener("input", () => {
                lastModifiedInput = input;
            });

            if (input.tagName === "SELECT") {
                input.addEventListener("change", () => {
                    lastModifiedInput = input;
                });
            }
        });

        form.addEventListener("submit", function (e) {
            if (!lastModifiedInput || !lastModifiedInput.value.trim()) {
                e.preventDefault();
                alert("Vui lòng nhập ít nhất một giá trị để tìm kiếm!");
                return;
            }

            // Disable tất cả trừ input cuối cùng
            inputs.forEach(input => {
                if (input !== lastModifiedInput) {
                    input.disabled = true;
                }
            });
        });
    });
</script>

<!-- JavaScript hiển thị biểu đồ Chart.js -->
<script>
    const ctx = document.getElementById('promotionChart');
    if (ctx) {
        const data = {
            labels: ["Có khuyến mãi", "Không có"],
            datasets: [{
                label: 'Tình trạng khuyến mãi',
                data: [<?= $promotionCounts['on'] ?>, <?= $promotionCounts['off'] ?>],
                backgroundColor: ['#28a745', '#dc3545'],
                hoverOffset: 10
            }]
        };

        const chart = new Chart(ctx, {
            type: 'pie',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#333',
                            font: { size: 14 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const label = context.label;
                                const value = context.parsed;
                                const total = <?= $promotionCounts['on'] + $promotionCounts['off'] ?>;
                                const percent = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} sản phẩm (${percent}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
</script>



<?php $this->stop() ?>