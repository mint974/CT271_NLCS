<?php $this->layout("layouts/admin", ["title" => APPNAME]) ?>
<?php $this->start("page") ?>

<style>
  .hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    background-color: #f8f9fa;
    transition: all 0.2s ease-in-out;
  }

  .transition {
    transition: all 0.2s ease-in-out;
  }

  .chart-card {
    border-radius: 16px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
  }

  .chart-card:hover {
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
  }

  .card-title i {
    font-size: 1.2rem;
  }
</style>

<div class="container py-4">
  <h3 class="mb-4"><i class="fa fa-home me-2"></i>Tổng Quan</h3>


  <div class="row g-4">
    <!-- Lợi nhuận theo tháng -->
    <div class="col-md-6">
      <div class="card chart-card">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0"><i class="bi bi-graph-up-arrow me-2"></i>Lợi nhuận theo tháng</h5>
        </div>
        <div class="card-body">
          <canvas id="profitChart" height="300"></canvas>
        </div>
      </div>
    </div>

    <!-- Khuyến mãi & người dùng -->
    <div class="col-md-6">
      <a href="/promotion/admin" class="text-decoration-none text-dark">
        <div class="card chart-card mb-4">
          <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="bi bi-tags me-2"></i>Khuyến mãi</h5>
          </div>
          <div class="card-body">
            <p class="mb-0">Hiện có <strong><?= htmlspecialchars($totalpromotion) ?></strong> chương trình khuyến mãi đang diễn
              ra.</p>
          </div>
        </div>
      </a>

      <a href="/account/admin" class="text-decoration-none text-dark">
        <div class="card chart-card">
          <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-people me-2"></i>Tổng số người dùng</h5>
          </div>
          <div class="card-body">
            <p class="mb-0">Hệ thống hiện có <strong><?= htmlspecialchars($totaluser) ?></strong> người dùng đã đăng ký.</p>
          </div>
        </div>
      </a>
    </div>

    <!-- Đơn hàng hôm nay -->
    <div class="col-12">
      <div class="card chart-card">
        <div class="card-header bg-info text-white">
          <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Đơn hàng hôm nay</h5>
        </div>
        <div class="card-body" id="todayOrders">
          <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $order): ?>
              <a href="<?= '/orders/order_detail/' . htmlspecialchars($order->id_order) ?>" class="text-decoration-none text-dark">
                <div class="border rounded-3 p-3 mb-3 bg-light hover-shadow transition">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold text-primary">Mã đơn: <?= htmlspecialchars($order->id_order) ?></span>
                    <span class="badge bg-secondary"><?= htmlspecialchars($order->status) ?></span>
                  </div>
                  <p class="mb-1">
                    <i class="bi bi-calendar-event me-1 text-muted"></i>
                    <small class="text-muted">Ngày tạo:</small> <?= date('d/m/Y H:i', strtotime($order->created_at)) ?>
                  </p>
                  <p class="mb-1">
                    <i class="bi bi-person-circle me-1 text-muted"></i>
                    <small class="text-muted">Tài khoản ID:</small> <?= htmlspecialchars($order->id_account) ?>
                  </p>
                  <?php if (!empty($order->id_delivery)): ?>
                    <p class="mb-0">
                      <i class="bi bi-truck me-1 text-muted"></i>
                      <small class="text-muted">Mã giao hàng:</small> <?= htmlspecialchars($order->id_delivery) ?>
                    </p>
                  <?php endif; ?>
                </div>
              </a>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="fs-5 text-success text-center">Chưa có đơn hàng nào!</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ChartJS Script -->
<script>
  const profitData = {
    labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4'],
    datasets: [{
      label: 'Lợi nhuận (triệu đồng)',
      data: [12, 19, 10, 15],
      fill: false,
      borderColor: 'rgb(75, 192, 192)',
      tension: 0.1
    }]
  };

  const config = {
    type: 'line',
    data: profitData,
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'top' },
        title: { display: false }
      }
    },
  };

  const profitChart = new Chart(document.getElementById('profitChart'), config);
</script>

<?php $this->stop() ?>