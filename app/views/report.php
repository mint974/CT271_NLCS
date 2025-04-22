<?php $this->layout("layouts/admin", ["title" => APPNAME]) ?>
<?php $this->start("page") ?>

<style>
    .card-stat {
        text-align: center;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .card-stat h4 {
        color: #08a045;
    }

    .chart-card {
        border-radius: 16px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .chart-card:hover {
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .chart-container canvas {
        max-width: 100%;
        height: 320px !important;
    }
</style>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0"><i class="bi bi-bar-chart-line-fill me-2"></i>Báo Cáo</h3>
    </div>

    <!-- Tổng quan -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card-stat">
                <h6>Tổng Tiền Chi</h6>
                <h4 class="text-danger">45,000,000 VND</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-stat">
                <h6>Tổng Tiền Thu</h6>
                <h4 class="text-success">68,000,000 VND</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-stat">
                <h6>Lợi Nhuận</h6>
                <h4 class="text-primary">23,000,000 VND</h4>
            </div>
        </div>
    </div>

    <!-- Biểu đồ lợi nhuận -->
    <div class="card mb-4 chart-card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Biểu Đồ Lợi Nhuận 12 Tháng</h5>
            <select class="form-select w-auto" id="yearSelect"></select>
        </div>
        <div class="card-body">
            <canvas id="profitChart" height="100"></canvas>

            <!-- Custom Legend Below Chart -->
            <div class="mt-3 text-center">
                <span class="me-3">
                    <i class="bi bi-circle-fill me-1" style="color: #dc3545;"></i>
                    <small class="text-muted">Tổng tiền chi</small>
                </span>
                <span class="me-3">
                    <i class="bi bi-circle-fill me-1" style="color: #198754;"></i>
                    <small class="text-muted">Tổng tiền thu</small>
                </span>
                <span>
                    <i class="bi bi-circle-fill me-1" style="color: #0d6efd;"></i>
                    <small class="text-muted">Lợi nhuận</small>
                </span>
            </div>

        </div>
    </div>

    <!-- Top sản phẩm / khách hàng -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card chart-card">
                <div class="card-header bg-info text-white text-center">
                    <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Sản Phẩm Bán Chạy</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item">Anh đào - 350 lượt</li>
                        <li class="list-group-item">Me thái - 290 lượt</li>
                        <li class="list-group-item">Táo - 250 lượt</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card chart-card">
                <div class="card-header bg-success text-white text-center">
                    <h5 class="mb-0"><i class="bi bi-people-fill me-2"></i>Khách Hàng Mua Nhiều</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item">Nattat - 12 đơn</li>
                        <li class="list-group-item">nguyetmai - 10 đơn</li>
                        <li class="list-group-item">AnhThu - 8 đơn</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script>
  const ctx = document.getElementById('profitChart');
  const yearSelect = document.getElementById('yearSelect');

  // Bước 1: Danh sách năm (giả lập từ 2022 đến năm hiện tại)
  const currentYear = new Date().getFullYear();
  for (let y = currentYear; y >= 2022; y--) {
    const opt = document.createElement('option');
    opt.value = y;
    opt.textContent = y;
    yearSelect.appendChild(opt);
  }

  // Bước 2: Dữ liệu mẫu theo năm
  const profitDataByYear = {
    2025: {
      labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6'],
      datasets: [
        {
          label: 'Tổng tiền chi (triệu đồng)',
          data: [10, 12, 8, 14, 9, 11],
          borderColor: '#dc3545',
          tension: 0.3,
          fill: false
        },
        {
          label: 'Tổng tiền thu (triệu đồng)',
          data: [20, 22, 18, 24, 19, 21],
          borderColor: '#198754',
          tension: 0.3,
          fill: false
        },
        {
          label: 'Lợi nhuận (triệu đồng)',
          data: [10, 10, 10, 10, 10, 10],
          borderColor: '#0d6efd',
          tension: 0.3,
          fill: false
        }
      ]
    },
    2024: {
      labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6'],
      datasets: [
        {
          label: 'Tổng tiền chi (triệu đồng)',
          data: [9, 11, 7, 13, 8, 10],
          borderColor: '#dc3545',
          tension: 0.3,
          fill: false
        },
        {
          label: 'Tổng tiền thu (triệu đồng)',
          data: [18, 20, 17, 22, 18, 19],
          borderColor: '#198754',
          tension: 0.3,
          fill: false
        },
        {
          label: 'Lợi nhuận (triệu đồng)',
          data: [9, 9, 10, 9, 10, 9],
          borderColor: '#0d6efd',
          tension: 0.3,
          fill: false
        }
      ]
    }
  };

  // Bước 3: Khởi tạo biểu đồ với năm mặc định
  let chartInstance = new Chart(ctx, {
    type: 'line',
    data: profitDataByYear[currentYear] || profitDataByYear[2025],
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'top' },
        title: { display: false }
      },
      scales: {
        y: {
          ticks: {
            callback: function (value) {
              return value + ' triệu';
            }
          }
        }
      }
    }
  });

  // Bước 4: Cập nhật dữ liệu khi chọn năm
  yearSelect.addEventListener('change', function () {
    const selectedYear = this.value;
    const newData = profitDataByYear[selectedYear];

    if (newData) {
      chartInstance.data.labels = newData.labels;
      chartInstance.data.datasets = newData.datasets;
      chartInstance.update();
    }
  });
</script>


<?php $this->stop() ?>