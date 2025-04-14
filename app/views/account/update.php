<?php
if (AUTHGUARD()->user()->role === 'khách hàng') {
    $this->layout("layouts/default", ["title" => APPNAME]);
} else {
    $this->layout("layouts/admin", ["title" => APPNAME]);
}
?>
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

                    <?php if (!empty($_SESSION['form_error'])): ?>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <?php if (is_array($_SESSION['form_error'])): ?>
                                <ul class="mb-0">
                                    <?php foreach ($_SESSION['form_error'] as $error): ?>
                                        <li><?= htmlspecialchars((string) $error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php elseif (is_string($_SESSION['form_error'])): ?>
                                <?= htmlspecialchars($_SESSION['form_error']) ?>
                            <?php endif; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
                        </div>
                        <?php unset($_SESSION['form_error']); ?>
                    <?php endif; ?>

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

                    <?php if (isset($success) && !empty($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php if (is_array($success)): ?>
                                <ul>
                                    <?php foreach ($success as $message): ?>
                                        <li><?= htmlspecialchars((string) $message) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php elseif (is_string($success)): ?>
                                <?= htmlspecialchars($success) ?>
                            <?php else: ?>
                                <p>Hoàn thành thành công.</p>
                            <?php endif; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <form action="/account/update" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                        <div class="text-center mb-4 position-relative">
                            <label for="avatar" class="avatar-wrapper mb-3">
                                <img src="/<?= htmlspecialchars($user->url) ?>" alt="Avatar"
                                    class="rounded-circle avatar-img">
                                <div class="upload-icon bg-light shadow">
                                    <i class="fa fa-camera text-success"></i>
                                </div>
                            </label>
                            <input type="file" class="form-control d-none" name="avatar" id="avatar">
                            <h4 class="text-primary">Thay ảnh đại diện</h4>
                        </div>

                        <div class="mb-3 d-none">
                            <label for="id_account" class="form-label "><strong>id người dùng</strong></label>
                            <input type="text" class="form-control" name="id_account" id="id_account"
                                value="<?= htmlspecialchars($user->id_account) ?>">
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label "><strong>Tên người dùng</strong></label>
                            <input type="text" class="form-control" name="username" id="username"
                                value="<?= htmlspecialchars($user->username) ?>">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label"><strong>Email</strong></label>
                            <input type="text" class="form-control" name="email" id="email"
                                value="<?= htmlspecialchars($user->email) ?>">
                        </div>

                        <?php if (AUTHGUARD()->user()->role === 'quản lý'): ?>
                            <?php if (AUTHGUARD()->user()->id_account === $user->id_account): ?>
                                <div class="mb-3">
                                    <label for="old_password" class="form-label"><strong>Mật khẩu cũ</strong></label>
                                    <input type="password" class="form-control" name="old_password" id="old_password">
                                </div>

                                <div class="mb-3">
                                    <label for="new_password" class="form-label"><strong>Mật khẩu mới</strong></label>
                                    <input type="password" class="form-control" name="new_password" id="new_password">
                                </div>

                                <div class="mb-4">
                                    <label for="confirm_password" class="form-label"><strong>Xác nhận mật khẩu
                                            mới</strong></label>
                                    <input type="password" class="form-control" name="confirm_password" id="confirm_password">
                                </div>
                            <?php endif; ?>
                            <?php
                            $roles = ['khách hàng', 'nhân viên', 'quản lý'];
                            $currentRole = $user->role;
                            ?>

                            <div class="mb-3">
                                <label for="role" class="form-label"><strong>Quyền</strong></label>
                                <select class="form-control" name="role">

                                    <option value="<?= htmlspecialchars($currentRole) ?>" selected>
                                        <?= htmlspecialchars($currentRole) ?> (hiện tại)
                                    </option>


                                    <?php foreach ($roles as $role): ?>
                                        <?php if ($role !== $currentRole): ?>
                                            <option value="<?= htmlspecialchars($role) ?>">
                                                <?= htmlspecialchars($role) ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>


                        <?php else: ?>
                            <div class="mb-3">
                                <label for="old_password" class="form-label"><strong>Mật khẩu cũ</strong></label>
                                <input type="password" class="form-control" name="old_password" id="old_password">
                            </div>

                            <div class="mb-3">
                                <label for="new_password" class="form-label"><strong>Mật khẩu mới</strong></label>
                                <input type="password" class="form-control" name="new_password" id="new_password">
                            </div>

                            <div class="mb-4">
                                <label for="confirm_password" class="form-label"><strong>Xác nhận mật khẩu
                                        mới</strong></label>
                                <input type="password" class="form-control" name="confirm_password" id="confirm_password">
                            </div>
                        <?php endif; ?>

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