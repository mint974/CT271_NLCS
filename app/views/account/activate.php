<?php $this->layout("layouts/admin", ["title" => APPNAME]) ?>
<?php $this->start("page") ?>

<style>
    .activate-card {
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

    .activate-card .card-body {
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

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card activate-card shadow-lg mt-4">
                <canvas id="background-canvas"></canvas>
                <div class="card-body">
                    <h2 class="text-center fw-bold text-primary mb-4">Kích hoạt tài khoản</h2>

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

                    <form action="/account/activate" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                        <input type="hidden" name="id_account" value="<?= $this->e($user['id_account']) ?>">

                        <div class="mb-3">
                            <label class="form-label"><strong>ID tài khoản</strong></label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($user['id_account']) ?>"
                                disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><strong>Tên người dùng</strong></label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>"
                                disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><strong>Email</strong></label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>"
                                disabled>
                        </div>

                        <div class="mb-4">
                            <label class="form-label"><strong>Trạng thái tài khoản</strong></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="activateYes" value="1"
                                    >
                                <label class="form-check-label text-success" for="activateYes">
                                    Kích hoạt lại tài khoản
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="activateNo" value="0"
                                    >
                                <label class="form-check-label text-danger" for="activateNo">
                                    Không kích hoạt lại tài khoản
                                </label>
                            </div>
                        </div>


                        <button type="submit" class="btn btn-success w-100 fw-bold mb-2">
                            Cập nhật trạng thái tài khoản
                        </button>

                        <a href="/account/admin" class="btn btn-outline-secondary w-100 fw-bold">
                            <i class="fas fa-users"></i> Quay về
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
    }

    window.addEventListener("resize", resizeCanvas);
    resizeCanvas();
</script>

<?php $this->stop() ?>