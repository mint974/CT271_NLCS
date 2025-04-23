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

    .card .card-header {
        border-bottom: none;
        font-weight: bold;
        font-size: 18px;
    }

    .btn {
        transition: all 0.2s ease;
    }

    .btn:hover {
        transform: scale(1.05);
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #ddd;
    }

    .table th {
        background-color: #0d6efd;
        color: white;
        vertical-align: middle;
    }

    .table td {
        vertical-align: middle;
    }

    .pagination .page-link {
        color: #0d6efd;
        transition: background 0.2s ease;
    }

    .pagination .active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
    }

    .pagination .page-link:hover {
        background-color: #e6f0ff;
    }

    .chart-container {
        width: 300px;
        height: auto;
    }

    .legend-container ul {
        font-size: 15px;
        line-height: 1.8;
        color: #333;
    }

    .legend-dot {
        display: inline-block;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        margin-right: 8px;
        vertical-align: middle;
    }
</style>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0"><i class="bi bi-people-fill me-2"></i>Quản lý tài khoản người dùng</h3>

    </div>
    <div class="card mb-4 chart-card">
        <div class="card-header text-white bg-primary d-flex align-items-center justify-content-center">
            <i class="bi bi-pie-chart-fill me-2 fs-5"></i>
            <strong>Thống kê trạng thái tài khoản</strong>
        </div>
        <div class="card-body row">
            <!-- Biểu đồ bên trái -->
            <div class="col-md-6 d-flex align-items-center justify-content-center">
                <div class="chart-container text-center  w-100">
                    <canvas id="statusChart"></canvas>
                    <p class="fs-6 w-100 text-center mt-3 mb-3"><strong>BIỂU ĐỒ NGƯỜI DÙNG</strong></p>
                </div>

            </div>

            <!-- Chú thích bên phải -->
            <div class="col-md-6 legend-container d-flex align-items-center">
                <ul class="list-unstyled ">
                    <LI>
                        <p class="fs-6 w-100 text-center legend-dot"><strong>CHÚ GIẢI: </strong></p>

                    </LI>
                    <li>
                        <span class="legend-dot" style="background-color: #08a045;"></span> Hoạt động:
                        <strong><?= htmlspecialchars($activities['active']); ?></strong>
                    </li>
                    <li>
                        <span class="legend-dot" style="background-color: #08bdbd;"></span> Chờ khôi phục:
                        <strong><?= htmlspecialchars($activities['pending_activation']); ?></strong>
                    </li>
                    <li>
                        <span class="legend-dot" style="background-color: #f21b3f;"></span> Vô hiệu hóa:
                        <strong><?= htmlspecialchars($activities['suspend']); ?></strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>


    <div class="card mb-3 shadow-sm">
        <!-- Form tìm kiếm -->
        <div class="card-header text-white bg-primary d-flex align-items-center justify-content-center">
            <i class="fas fa-search me-2 fs-5"></i>
            <strong>Tìm kiếm</strong>
        </div>

        <form id="searchForm" action="/account/search" method="get" class="row g-3 card-body align-items-center">
            <div class="col-md-3">
                <input type="text" class="form-control" placeholder="🔎 Tìm theo username..." name="username"
                    value="<?= htmlspecialchars($_GET['username'] ?? '') ?>">

            </div>

            <div class="col-md-3">
                <input type="text" class="form-control" placeholder="📧 Tìm theo email..." name="email"
                    value="<?= htmlspecialchars($_GET['email'] ?? '') ?>">
            </div>

            <div class="col-md-3">
                <select class="form-control" name="status">
                    <option value="">Lọc theo trạng thái...</option>
                    <option value="Hoạt động" <?= ($_GET['status'] ?? '') === 'Hoạt động' ? 'selected' : '' ?>>Hoạt động
                    </option>
                    <option value="Khôi phục tài khoản" <?= ($_GET['status'] ?? '') === 'Khôi phục tài khoản' ? 'selected' : '' ?>>Chờ khôi phục
                    </option>
                    <option value="Vô hiệu hóa tài khoản" <?= ($_GET['status'] ?? '') === 'Vô hiệu hóa tài khoản' ? 'selected' : '' ?>>Vô hiệu hóa</option>
                </select>

            </div>

            <div class="col-md-3 text-md-center">
                <div class="d-inline-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-search"></i> Tìm kiếm
                    </button>
                    <a href="/account/admin" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-list-ul"></i> Xem tất cả
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Danh sách người dùng -->
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
        <?php endif; ?>
        <?php if (!empty($success) && is_string($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <table class="table table-hover align-middle text-center">
            <thead>
                <tr>
                    <th>STT</th>
                    <th><i class="bi bi-person-circle"></i> ID</th>
                    <th><i class="bi bi-person-circle"></i> Ảnh</th>
                    <th><i class="bi bi-person"></i> Username</th>
                    <th><i class="bi bi-envelope-at"></i> Email</th>
                    <th><i class="bi bi-shield-lock"></i> Vai trò</th>
                    <th><i class="bi bi-check-circle"></i> Trạng thái</th>
                    <th><i class="bi bi-tools"></i> Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php

                foreach ($users as $index => $user):
                    ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($user['id_account']) ?></td>

                        <td><img src="/<?= $user['url'] ?>" alt="Avatar" class="user-avatar"></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td><?php if ($user['status'] === 'Hoạt động'): ?>
                                <p class="btn btn-success">Hoạt động</p>
                            <?php elseif ($user['status'] === 'Vô hiệu hóa tài khoản'): ?>
                                <p class="btn btn-danger">Đã vô hiệu hóa</p>
                            <?php else: ?>
                                <p class="btn btn-warning">Chờ khôi phục</p>
                            <?php endif; ?>
                        </td>

                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <?php if (AUTHGUARD()->user()->role === 'quản lý'): ?>
                                    <a href="<?= '/account/detail/' . htmlspecialchars($user['id_account']) ?>"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Xem chi tiết
                                    </a>
                                    <a href="/account/update/<?= htmlspecialchars($user['id_account']) ?>"
                                        class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil-square"></i> Sửa
                                    </a>
                                    <?php if ($user['status'] === 'Hoạt động'): ?>

                                        <a href="/account/suspend/<?= htmlspecialchars($user['id_account']) ?>"
                                            class="btn btn-sm btn-danger">
                                            <i class="bi bi-slash-circle"></i> Đình chỉ
                                        </a>

                                    <?php elseif ($user['status'] === 'Vô hiệu hóa tài khoản'): ?>

                                        <button type="submit" class="btn btn-sm btn-danger " disabled>
                                            <i class="bi bi-slash-circle"></i> Vô hiệu hóa
                                        </button>
                                    <?php else: ?>
                                        <!-- Nút mở modal -->
                                        <a href="<?= '/account/activate/' . htmlspecialchars($user['id_account']) ?>"
                                        class="btn btn-sm btn-primary">
                                        <i class="bi bi-arrow-clockwise"></i> Chờ khôi phục
                                    </a>
                                    <?php endif; ?>

                                <?php else: ?>
                                    <a href="<?= '/account/detail/' . htmlspecialchars($user['id_account']) ?>"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Xem chi tiết
                                    </a>
                                <?php endif; ?>

                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    
    </div>


</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Lọc form
        const form = document.getElementById("searchForm");
        const inputs = form.querySelectorAll("input[type='text'], select");

        let lastModifiedInput = null;

        // Ghi nhận input cuối cùng được người dùng tương tác
        inputs.forEach(input => {
            input.addEventListener("input", () => {
                lastModifiedInput = input;
            });

            // Với select, dùng change thay vì input
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

            // Disable tất cả trừ input được tương tác gần nhất
            inputs.forEach(input => {
                if (input !== lastModifiedInput) {
                    input.disabled = true;
                }
            });
        });


        //Dữ liệu biểu đồ
        const statusCounts = {
            "Hoạt động": <?= htmlspecialchars($activities['active']) ?>,
            "Chờ khôi phục": <?= htmlspecialchars($activities['pending_activation']) ?>,
            "Vô hiệu hóa": <?= htmlspecialchars($activities['suspend']) ?>
        };


        const total = Object.values(statusCounts).reduce((acc, val) => acc + val, 0);

        const ctx = document.getElementById('statusChart');
        if (!ctx) return; // Ngăn lỗi nếu không có canvas

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: Object.keys(statusCounts),
                datasets: [{
                    label: 'Số lượng tài khoản',
                    data: Object.values(statusCounts),
                    backgroundColor: ['#08a045', '#08bdbd', '#f21b3f'],
                    borderRadius: 6,
                    barThickness: 50,
                    barPercentage: 0.8,
                    categoryPercentage: 0.9
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const label = context.label;
                                const value = context.parsed.x ?? context.parsed.y;
                                const percent = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} tài khoản (${percent}%)`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });

        // Lấy tất cả <li> hiện có, giữ lại phần CHÚ GIẢI
        const originalLegendHTML = legendContainer.innerHTML.split("</li>")[0] + "</li>"; // giữ CHÚ GIẢI

        let dynamicLegendHTML = '';
        Object.entries(statusCounts).forEach(([status, count], index) => {
            const percent = ((count / total) * 100).toFixed(1);
            const color = ['#08a045', '#08bdbd', '#f21b3f'][index];
            dynamicLegendHTML += `
        <li>
            <span class="legend-dot" style="background-color: ${color};"></span>
            ${status}: <strong>${count}</strong> (${percent}%)
        </li>
    `;
        });

        legendContainer.innerHTML = originalLegendHTML + dynamicLegendHTML;


    });
</script>



<?php $this->stop() ?>