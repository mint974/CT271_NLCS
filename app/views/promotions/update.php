<?php $this->layout("layouts/admin", ["title" => APPNAME]) ?>
<?php $this->start("page") ?>
<?php
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);
?>

<?php
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<style>
    .profile-card {
        position: relative;
        border-radius: 1rem;
        overflow: hidden;
        z-index: 1;
    }

    canvas#background-canvas {
        position: absolute;
        top: 0;
        left: 0;
        z-index: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
    }

    .profile-card .card-body {
        position: relative;
        z-index: 2;
    }

    .btn-submit {
        background-color: #2e9e5b;
        color: white;
    }

    .btn-submit:hover {
        background-color: #278a4d;
    }
</style>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card profile-card shadow">
                <canvas id="background-canvas"></canvas>
                <div class="card-body">
                    <h2 class="text-center fw-bold text-success mb-4">Sửa khuyến mãi</h2>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <?php if (is_array($errors)): ?>
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <?= htmlspecialchars($errors) ?>
                            <?php endif; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
                        </div>

                    <?php endif; ?>

                    <?php if (isset($success) && !empty($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($success) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
                        </div>
                    <?php endif; ?>
                    <?php
                    // Lấy giá trị datetime-local đúng chuẩn
                    $startDate = $old['start_date'] ?? $promotion->start_day;
                    $endDate = $old['end_date'] ?? $promotion->end_day;
                    ?>

                    <form action="/promotion/update" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                        <div class="mb-3">
                            <label for="id_promotion" class="form-label"><strong>Mã khuyến mãi</strong></label>
                            <input type="text" class="form-control" id="id_promotion" name="id_promotion"
                                value="<?= htmlspecialchars($old['id_promotion'] ?? $promotion->id_promotion) ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label"><strong>Tên khuyến mãi</strong></label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="<?= htmlspecialchars($old['name'] ?? $promotion->name) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label"><strong>Mô tả</strong></label>
                            <textarea class="form-control" id="description" name="description"
                                required><?= htmlspecialchars($old['description'] ?? $promotion->description) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="discount_percent" class="form-label"><strong>Phần trăm giảm (%)</strong></label>
                            <input type="number" class="form-control" id="discount_percent" name="discount_percent"
                                min="0" max="100"
                                value="<?= htmlspecialchars($old['discount_percent'] ?? $promotion->discount_rate) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="start_date" class="form-label"><strong>Ngày bắt đầu</strong></label>
                            <input type="datetime-local" class="form-control" id="start_date" name="start_date"
                                value="<?= htmlspecialchars(date('Y-m-d\TH:i', strtotime($startDate))) ?>" required>
                        </div>

                        <div class="mb-4">
                            <label for="end_date" class="form-label"><strong>Ngày kết thúc</strong></label>
                            <input type="datetime-local" class="form-control" id="end_date" name="end_date"
                                value="<?= htmlspecialchars(date('Y-m-d\TH:i', strtotime($endDate))) ?>" required>
                        </div>

                        <button type="submit" class="btn btn-submit w-100 fw-bold mb-3">Cập nhật khuyến mãi</button>
                        <a href="/promotion/admin" class="btn btn-outline-secondary w-100 fw-bold mb-3">
                            <i class="bi bi-arrow-left-circle me-2"></i>Quay lại
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const canvas = document.getElementById("background-canvas");
    const ctx = canvas.getContext("2d");

    function resizeCanvas() {
        canvas.width = canvas.offsetWidth;
        canvas.height = canvas.offsetHeight;
        drawWaveBackground();
    }

    function drawWaveBackground() {
        const width = canvas.width;
        const height = canvas.height;

        ctx.clearRect(0, 0, width, height);

        ctx.beginPath();
        ctx.moveTo(0, height * 0.7);
        ctx.bezierCurveTo(width * 0.3, height, width * 0.7, height * 0.4, width, height * 0.7);
        ctx.lineTo(width, height);
        ctx.lineTo(0, height);
        ctx.closePath();
        ctx.fillStyle = "#a8e6cf";
        ctx.fill();

        ctx.beginPath();
        ctx.moveTo(0, height * 0.85);
        ctx.bezierCurveTo(width * 0.4, height * 0.6, width * 0.6, height, width, height * 0.8);
        ctx.lineTo(width, height);
        ctx.lineTo(0, height);
        ctx.closePath();
        ctx.fillStyle = "#dcedc1";
        ctx.fill();
    }

    window.addEventListener("resize", resizeCanvas);
    resizeCanvas();
</script>

<?php $this->stop() ?>