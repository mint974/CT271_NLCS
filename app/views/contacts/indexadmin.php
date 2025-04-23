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

  .chart-card {
    border-radius: 16px;

    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
  }

  .chart-card:hover {
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
  }

  .legend-dot {
    display: inline-block;
    width: 16px;
    height: 16px;
    margin-right: 8px;
    border-radius: 50%;
  }

  .chart-container canvas {
    max-width: 100%;
    height: 320px !important;
  }
</style>

<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0"><i class="bi bi-envelope-paper-heart me-2"></i>Quản lý liên hệ</h3>
  </div>

  <div class="card mb-4 chart-card">
    <div class="card-header text-white bg-primary d-flex align-items-center justify-content-center">
      <i class="bi bi-pie-chart-fill me-2 fs-5"></i>
      <strong>Thống kê liên hệ theo chủ đề</strong>
    </div>
    <div class="card-body row">
      <!-- Biểu đồ bên trái -->
      <div class="col-md-6 d-flex align-items-center justify-content-center">
        <div class="chart-container text-center w-100">
          <canvas id="subjectChart"></canvas>
          <p class="fs-6 w-100 text-center mt-3 mb-3"><strong>BIỂU ĐỒ LIÊN HỆ</strong></p>
        </div>
      </div>

      <!-- Chú thích bên phải -->
      <div class="col-md-6 legend-container d-flex align-items-center">
        <ul class="list-unstyled">
          <li>
            <p class="fs-6 w-100 text-center legend-dot"><strong>CHÚ GIẢI:</strong></p>
          </li>
          <li>
            <span class="legend-dot" style="background-color: #0d6efd;"></span> Góp ý chung:
            <strong><?= htmlspecialchars($generalFeedback) ?></strong>
          </li>
          <li>
            <span class="legend-dot" style="background-color: #dc3545;"></span> Báo lỗi:
            <strong><?= htmlspecialchars($bugReport) ?></strong>
          </li>
          <li>
            <span class="legend-dot" style="background-color: #ffc107;"></span> Đề xuất cải thiện:
            <strong><?= htmlspecialchars($improvementSuggestions) ?></strong>
          </li>
        </ul>
      </div>
    </div>
  </div>


  <!-- Form tìm kiếm -->
  <div class="card mb-3 position-sticky" style="top: 72px; z-index: 100;">
    <div class="card-header bg-primary text-white text-center">
      <i class="bi bi-search me-2 fs-5"></i><strong>Tìm kiếm liên hệ</strong>
    </div>
    <form action="/contacts/search" id="search_form" method="post" class="row g-3 card-body align-items-center">
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

      <div class="col-md-4">
        <input type="text" class="form-control" name="id_contact" placeholder="🔎 Theo ID liên hệ..."
          value="<?= htmlspecialchars($_POST['id_contact'] ?? '') ?>">
      </div>
      <div class="col-md-4">
        <select name="subject" class="form-select">
          <option value="">📂 Tất cả chủ đề</option>
          <option <?= (($_POST['subject'] ?? '') == 'Góp ý chung') ? 'selected' : '' ?>>Góp ý chung</option>
          <option <?= (($_POST['subject'] ?? '') == 'Báo lỗi') ? 'selected' : '' ?>>Báo lỗi</option>
          <option <?= (($_POST['subject'] ?? '') == 'Đề xuất cải thiện') ? 'selected' : '' ?>>Đề xuất cải thiện</option>
        </select>
      </div>
      <div class="col-md-4 text-md-center">
        <button class="btn btn-primary btn-sm"><i class="bi bi-search"></i> Tìm kiếm</button>
        <a href="/contacts/index" class="btn btn-outline-primary btn-sm"><i class="bi bi-list"></i> Xem tất cả</a>
      </div>
    </form>
  </div>

  <!-- Bảng dữ liệu -->
  <div class="card table-responsive mb-3">
    <?php if (isset($errors) && !empty($errors)): ?>
      <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <?php foreach ((array) $errors as $error): ?>
          <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php elseif (isset($success)): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <table class="table table-hover text-center align-middle">
      <thead>
        <tr>
          <th>STT</th>
          <th>ID</th>
          <th>Chủ đề</th>
          <th>Nội dung</th>
          <th>Người liên hệ</th>
          <th>SĐT</th>
          <th>Ngày gửi</th>
          <th>Trạng thái</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($contacts)): ?>
          <tr>
            <td colspan="8" class="text-muted text-center">Không có liên hệ nào phù hợp.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($contacts as $index => $contact): ?>
            <tr>
              <td><?= $index + 1 ?></td>
              <td><?= htmlspecialchars($contact->id_contact) ?></td>
              <td><?= htmlspecialchars($contact->subject) ?></td>
              <td class="text-start"><?= nl2br(htmlspecialchars($contact->content)) ?></td>
              <td><a href="<?= '/account/detail/' . htmlspecialchars($contact->id_account) ?>"
                  >
                 <?= htmlspecialchars($contact->id_account) ?>
                </a></td>
              <td><?= htmlspecialchars($contact->phone) ?></td>
              <td><?= htmlspecialchars($contact->created_at) ?></td>
              <td>
                <?php if ($contact->status === 'Đã phản hồi'): ?>
                  <span class="badge bg-success">Đã phản hồi</span>
                <?php else: ?>
                  <span class="badge bg-secondary">Chưa phản hồi</span>
                <?php endif; ?>
              </td>
              <td>
                <a href="/contacts/reply/<?= htmlspecialchars($contact->id_contact) ?>" class="btn btn-sm btn-outline-primary">
                  <i class="bi bi-reply"></i> Phản hồi
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const ctx = document.getElementById('subjectChart').getContext('2d');
  const subjectChart = new Chart(ctx, {
    type: 'pie',
    data: {
      labels: ['Góp ý chung', 'Báo lỗi', 'Đề xuất cải thiện'],
      datasets: [{
        data: [<?= $generalFeedback ?>, <?= $bugReport ?>, <?= $improvementSuggestions ?>],
        backgroundColor: ['#0d6efd', '#dc3545', '#ffc107'],
        borderColor: '#fff',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'bottom',
        },
      }
    }
  });
</script>


<?php $this->stop() ?>