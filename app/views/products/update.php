<?php $this->layout("layouts/admin", ["title" => APPNAME]); ?>
<?php $this->start("page") ?>
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

    .Avatar_file {
        width: 120px;
        height: 120px;
        object-fit: cover;
        object-position: center;
        clip-path: circle(130px at 50% 50%);
        margin-bottom: 1rem;
    }

    .btn-submit {
        background-color: #2e9e5b;
        color: white;
    }

    .btn-submit:hover {
        background-color: #278a4d;
    }

    .avatar-wrapper {
        display: inline-block;
        position: relative;
        width: 140px;
        height: 140px;
    }

    .avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid #2e9e5b;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        transition: 0.3s;
    }

    .upload-icon {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        cursor: pointer;
        border: 2px solid white;
    }

    .avatar-wrapper:hover .avatar-img {
        filter: brightness(85%);
    }

    .avatar-wrapper:hover .upload-icon {
        background-color: #e0fce3;
    }
</style>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card profile-card shadow">
                <canvas id="background-canvas"></canvas>
                <div class="card-body">
                    <h2 class="text-center fw-bold text-success mb-4">Chỉnh sửa thông tin</h2>


                    <?php if (isset($errors) && !empty($errors)): ?>

                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <?php if (is_array($errors)): ?>
                                <ul>
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= $this->e((string) $error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php elseif (is_string($errors)): ?>
                                <?= $this->e($errors) ?>
                            <?php else: ?>
                                <p>Lỗi không xác định.</p>
                            <?php endif; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($success) && !empty($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php if (is_array($success)): ?>
                                <ul>
                                    <?php foreach ($success as $message): ?>
                                        <li><?= $this->e((string) $message) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php elseif (is_string($success)): ?>
                                <?= $this->e($success) ?>
                            <?php else: ?>
                                <p>Hoàn thành thành công.</p>
                            <?php endif; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <form action="/products/update" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                        <div class="mb-3 ">
                            <label for="id_product" class="form-label "><strong>id sản phẩm</strong></label>
                            <input type="text" class="form-control" name="id_product" id="id_product"
                                value="<?= $this->e($product->id_product) ?>">
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label "><strong>Tên Sản phẩm</strong></label>
                            <input type="text" class="form-control" name="username" id="username"
                                value="<?= $this->e($product->username) ?>">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label"><strong>Mô tả</strong></label>
                            <input type="text" class="form-control" name="description" id="description"
                                value="<?= $this->e($product->description) ?>">
                        </div>

                        

                        <button type="submit" class="btn btn-submit w-100 fw-bold">Cập nhật thông tin</button>
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

    // Preview avatar when file is selected
    document.getElementById("avatar").addEventListener("change", function (event) {
        const file = event.target.files[0];
        const reader = new FileReader();
        if (file && file.type.startsWith("image/")) {
            reader.onload = function (e) {
                const avatarImg = document.querySelector(".avatar-img");
                avatarImg.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

</script>

<?php $this->stop() ?>