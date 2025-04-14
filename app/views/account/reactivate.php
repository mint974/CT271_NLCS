<?php $this->layout("layouts/default", ["title" => "Khôi phục tài khoản"]) ?>
<?php $this->start("page") ?>

<style>
    .reactivate-card {
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

    .reactivate-card .card-body {
        position: relative;
        z-index: 2;
    }


    .btn-success-custom:hover {
        background-color: #21867a;
    }

    .alert ul {
        padding-left: 1.2rem;
    }
</style>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card reactivate-card shadow-lg mt-4">
                <canvas id="background-canvas"></canvas>
                <div class="card-body">
                    <h2 class="text-center fw-bold text-success mb-4">Khôi phục tài khoản</h2>
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
                    <div class="alert alert-info border-info shadow-sm" role="alert">

                        <p><strong class="d-block mb-2">Vui lòng nhập địa chỉ email và mật khẩu</strong>
                            để xác nhận việc khôi phục tài khoản của bạn.</p>
                        <p>Tài khoản của bạn sẽ được quản lý của chúng tôi cấp lại sớm nhất.</p>
                    </div>

                    <form action="/account/reactivate" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label"><strong>Email</strong></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label"><strong>Mật khẩu</strong></label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <button type="submit" class="btn btn-success w-100 fw-bold">
                            Xác nhận khôi phục tài khoản
                        </button>
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
    }

    window.addEventListener("resize", resizeCanvas);
    resizeCanvas();
</script>

<?php $this->stop() ?>