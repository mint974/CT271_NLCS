<?php $this->layout("layouts/admin", ["title" => APPNAME]); ?>
<?php $this->start("page") ?>

<?php
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!-- CSS cho nền và form -->
<style>
    .edit-product-container {
        position: relative;
        z-index: 1;
        padding-top: 3rem;
        padding-bottom: 3rem;
    }

    .product-edit-card {
        position: relative;
        border-radius: 1rem;
        overflow: hidden;
        z-index: 1;
    }

    #background-canvas {
        position: absolute;
        top: 0;
        left: 0;
        z-index: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
    }

    .product-edit-card .card-body {
        position: relative;
        z-index: 2;
    }
</style>




<div class="container-fluid edit-product-container">

    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            
            <div class="card shadow-lg border-0 rounded-4 position-relative product-edit-card">
                <!-- Canvas hiệu ứng nền -->
            <canvas id="background-canvas"></canvas>
                <div class="card-body px-4 py-5">
                    <h2 class="text-center text-success fw-bold mb-4">Chỉnh sửa thông tin sản phẩm</h2>

                    <!-- Thông báo lỗi -->
                    <?php if (isset($errors) && !empty($errors)): ?>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <?php if (is_array($errors)): ?>
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= $this->e((string) $error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <?= $this->e($errors) ?>
                            <?php endif; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Thông báo thành công -->
                    <?php if (isset($success) && !empty($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= is_array($success) ? implode("<br>", array_map([$this, 'e'], $success)) : $this->e($success) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="/products/update_infor" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                        <div class="row">
                            <!-- ID sản phẩm -->
                            <div class="col-md-6 mb-3">
                                <label for="id_product" class="form-label fw-semibold">ID sản phẩm</label>
                                <input type="text"
                                    class="form-control<?= isset($errors['id_product']) ? ' is-invalid' : '' ?>"
                                    id="id_product" name="id_product" value="<?= $this->e($product->id_product) ?>"
                                    readonly>
                                <?php if (isset($errors['id_product'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $this->e($errors['id_product']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Tên sản phẩm -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-semibold">Tên sản phẩm</label>
                                <input type="text"
                                    class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>" id="name"
                                    name="name" value="<?= $this->e($product->name) ?>">
                                <?php if (isset($errors['name'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $this->e($errors['name']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Mô tả -->
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Mô tả sản phẩm</label>
                            <textarea class="form-control<?= isset($errors['description']) ? ' is-invalid' : '' ?>"
                                id="description" name="description"
                                rows="4"><?= $this->e($product->description) ?></textarea>
                            <?php if (isset($errors['description'])): ?>
                                <div class="invalid-feedback">
                                    <?= $this->e($errors['description']) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- ID khuyến mãi -->
                        <div class="mb-3">
                            <label for="promotion" class="form-label fw-semibold">ID khuyến mãi</label>
                            <input type="text"
                                class="form-control<?= isset($errors['promotion']) ? ' is-invalid' : '' ?>"
                                id="promotion" name="promotion" value="<?= $this->e($product->id_promotion) ?>">
                            <?php if (isset($errors['promotion'])): ?>
                                <div class="invalid-feedback">
                                    <?= $this->e($errors['promotion']) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-success w-100 fw-bold mt-3">Cập nhật sản phẩm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Script canvas hiệu ứng sóng -->
<script>
    const canvas = document.getElementById("background-canvas");
    const ctx = canvas.getContext("2d");

    function resizeCanvas() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        drawWaveBackground();
    }

    function drawWaveBackground() {
        const width = canvas.width;
        const height = canvas.height;

        ctx.clearRect(0, 0, width, height);

        // Wave 1
        ctx.beginPath();
        ctx.moveTo(0, height * 0.7);
        ctx.bezierCurveTo(width * 0.3, height, width * 0.7, height * 0.4, width, height * 0.7);
        ctx.lineTo(width, height);
        ctx.lineTo(0, height);
        ctx.closePath();
        ctx.fillStyle = "#a8e6cf";
        ctx.fill();

        // Wave 2
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