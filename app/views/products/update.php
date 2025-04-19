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

    .card-img-top {
        width: 100%;
        height: 130px;
        object-fit: cover;
        border-radius: 0.5rem;
    }

    .add_product {
        width: 100px;
        height: 100px;
        background-color: #278a4d;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .add_product:hover {
        background-color: #237b3e;
    }

    .hidden-file-input {
        display: none;
    }

    .image-preview {
        position: relative;
    }

    .delete-icon {
        position: absolute;
        top: 5px;
        right: 5px;
        color: red;
        background: white;
        border-radius: 50%;
        padding: 5px;
        cursor: pointer;
        z-index: 5;
    }

    .image-preview.deleted {
        opacity: 0.4;
        pointer-events: none;
    }

    .image-preview.deleted::after {
        content: "\0111\00e3 chọn x\00f3a";
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: red;
        font-weight: bold;
        z-index: 10;
    }
</style>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-11 col-lg-10">
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

                        <div class="mb-3">
                            <label for="id_product" class="form-label"><strong>ID sản phẩm</strong></label>
                            <input type="text" class="form-control" name="id_product" id="id_product"
                                value="<?= $this->e($product['id_product']) ?>">
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label"><strong>Tên Sản phẩm</strong></label>
                            <input type="text" class="form-control" name="username" id="username"
                                value="<?= $this->e($product['name']) ?>">
                        </div>

                        <!-- ảnh -->
                        <div class="mb-3">
                            <div class="row row-cols-1 row-cols-md-6 g-4">
                                <?php foreach ($product['images'] as $image): ?>
                                    <div class="col image-preview existing position-relative">
                                        <div class="card position-relative">
                                            <img src="<?= $this->e($image) ?>" class="card-img-top" alt="ảnh">
                                            <!-- Nút xóa -->
                                            <div class="delete-existing text-danger"
                                                style="position:absolute; top:5px; right:10px; cursor:pointer;">
                                                <i class="bi bi-trash-fill fs-5"></i>
                                            </div>
                                            <!-- Overlay che mờ khi chọn xóa -->
                                            <div class="delete-overlay d-none" style="
                                                    position:absolute; top:0; left:0; width:100%; height:100%;
                                                    background-color: rgba(0,0,0,0.5); z-index:10; display:flex;
                                                    align-items:center; justify-content:center; color:white; border-radius: 0.5rem;">
                                                <div>
                                                    <p class="mb-2">Đã đánh dấu xoá</p>
                                                    <button type="button"
                                                        class="btn btn-sm btn-light undo-delete">Huỷ</button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Input ẩn để đánh dấu ảnh bị xóa -->
                                        <input type="hidden" name="deleted_images[]" value="<?= $this->e($image) ?>"
                                            class="delete-flag" disabled>
                                    </div>
                                <?php endforeach; ?>


                                <input type="file" id="imageInput" name="images[]" class="hidden-file-input" multiple>
                                <label for="imageInput" class="card add_product">
                                    <p class="text-center m-0"><i class="bi bi-plus-circle fs-3"></i><br>Thêm ảnh</p>
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label"><strong>Mô tả</strong></label>
                            <textarea class="form-control" name="description"
                                id="description"><?= $this->e($product['description']) ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-submit w-100 fw-bold">Cập nhật thông tin</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Khởi tạo canvas background
    const initCanvasBackground = () => {
        const canvas = document.getElementById("background-canvas");
        if (!canvas) return;

        const ctx = canvas.getContext("2d");
        
        const resizeCanvas = () => {
            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;
            drawWaveBackground();
        };

        const drawWaveBackground = () => {
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
        };

        window.addEventListener("resize", resizeCanvas);
        resizeCanvas();
    };

    // Xử lý thêm ảnh mới
    const initImageUpload = () => {
        const imageInput = document.getElementById("imageInput");
        if (!imageInput) return;

        const addLabel = document.querySelector("label[for='imageInput']");
        const imageRow = imageInput.closest(".row");

        addLabel?.addEventListener("click", () => {
            imageInput.click();
        });

        imageInput.addEventListener("change", function(event) {
            const files = event.target.files;
            if (!files || files.length === 0) return;

            for (let file of files) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement("div");
                    col.className = "col image-preview position-relative";

                    const card = document.createElement("div");
                    card.className = "card";

                    const img = document.createElement("img");
                    img.className = "card-img-top";
                    img.src = e.target.result;
                    img.alt = "Ảnh sản phẩm";

                    const deleteIcon = document.createElement("div");
                    deleteIcon.className = "delete-icon text-danger";
                    deleteIcon.innerHTML = '<i class="bi bi-trash-fill fs-5"></i>';

                    deleteIcon.addEventListener("click", () => {
                        col.remove();
                    });

                    card.appendChild(img);
                    card.appendChild(deleteIcon);
                    col.appendChild(card);

                    imageRow.insertBefore(col, addLabel.parentElement);
                };
                reader.readAsDataURL(file);
            }

            event.target.value = '';
        });
    };

    // Xử lý xóa ảnh cũ
    const initExistingImageHandlers = () => {
        document.querySelectorAll(".image-preview.existing").forEach((previewDiv) => {
            const deleteBtn = previewDiv.querySelector(".delete-existing");
            const overlay = previewDiv.querySelector(".delete-overlay");
            const undoBtn = previewDiv.querySelector(".undo-delete");

            if (!deleteBtn || !overlay || !undoBtn) return;

            deleteBtn.addEventListener("click", () => {
                overlay.classList.remove("d-none");
                const deleteFlag = previewDiv.querySelector(".delete-flag");
                if (deleteFlag) deleteFlag.disabled = false;
            });

            undoBtn.addEventListener("click", () => {
                overlay.classList.add("d-none");
                const deleteFlag = previewDiv.querySelector(".delete-flag");
                if (deleteFlag) deleteFlag.disabled = true;
            });
        });
    };

    // Khởi chạy khi DOM tải xong
    document.addEventListener("DOMContentLoaded", () => {
        initCanvasBackground();
        initImageUpload();
        initExistingImageHandlers();
    });
</script>
<?php $this->stop() ?>