<?php
if (AUTHGUARD()->user()->role === 'khách hàng') {
    $this->layout("layouts/default", ["title" => APPNAME]);
} else {
    $this->layout("layouts/admin", ["title" => APPNAME]);
}
?>
<?php $this->start("page") ?>

<style>
    body {
        background-color: #e8fccf;
    }

    .profile-card {
        position: relative;
        overflow: hidden;
        border-radius: 1rem;
        background-color: white;
        padding: 2rem;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
    }

    #background-canvas {
        position: absolute;
        top: 0;
        right: 0;
        height: 100%;
        width: 100%;
        z-index: 0;
    }

    .profile-info {
        position: relative;
        z-index: 1;
    }

    .avatar {
        border-radius: 50%;
        width: 135px;
        height: 135px;
        object-fit: cover;
        object-position: center;
        clip-path: circle(130px at 50% 50%);
    }

    .address-card {
        background-color: #f5fff0;
        border: 1px solid #cdecc1;
        border-radius: 0.75rem;
        padding: 1rem;
        transition: all 0.3s ease;
        height: 100%;
    }

    .address-card:hover {
        border-color: #7ed957;
        box-shadow: 0 0 10px rgba(46, 204, 113, 0.3);
        transform: translateY(-5px);
    }

    .timeline {
        position: relative;
        padding-left: 2rem;
        list-style: none;
        border-left: 3px solid #7ed957;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .timeline-item::before {
        content: "";
        position: absolute;
        left: -11px;
        top: 0;
        width: 18px;
        height: 18px;
        background-color: #7ed957;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 0 0 2px #7ed957;
    }

    .timeline-item .time {
        font-size: 0.85rem;
        color: #888;
        margin-bottom: 0.25rem;
        display: block;
    }

    .timeline-item .timeline-content {
        background: #f5fff0;
        border: 1px solid #cdecc1;
        padding: 0.75rem;
        border-radius: 0.5rem;
        transition: 0.3s;
    }

    .timeline-item .timeline-content:hover {
        box-shadow: 0 0 10px rgba(46, 204, 113, 0.3);
    }

    a.btn:hover {
        color: white !important;
    }
</style>
<div class="container mt-4">
    <div class="profile-card">

        <canvas id="background-canvas"></canvas>


        <h2 class="fw-bold text-center text-success">Thông tin cá nhân</h2>
        <?php if (!empty($_SESSION['form_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php if (is_array($_SESSION['form_error'])): ?>
                    <ul>
                        <?php foreach ($_SESSION['form_error'] as $error): ?>
                            <li><?= htmlspecialchars((string) $error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php elseif (is_string($_SESSION['form_error'])): ?>
                    <?= htmlspecialchars($_SESSION['form_error']) ?>
                <?php else: ?>
                    <p>Lỗi không xác định.</p>
                <?php endif; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['form_error']); ?>
        <?php endif; ?>

        <div class="row align-items-center profile-info">
            <div class="col col-md-3 text-center">
                <img src="/<?= htmlspecialchars($user->url) ?>" alt="Avatar" class="avatar mb-2">
            </div>
            <div class="col col-md-9">
                <h4 class="fw-bold mb-3">Username: <?= htmlspecialchars($user->username) ?></h4>
                <p class="mb-3 fs-5"> Email đăng kí: <?= htmlspecialchars($user->email) ?></p>
                <?php if (AUTHGUARD()->user()->role !== 'nhân viên'): ?>
                    <div class="d-flex gap-2">
                        <a class="btn btn-outline-primary" href="/account/update/<?= htmlspecialchars($user->id_account) ?>">
                            Chỉnh sửa Thông tin
                        </a>
                        <button class="btn btn-outline-success" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#activityTimeline" aria-controls="activityTimeline">
                            Hiển thị dòng thời gian
                        </button>
                        <?php if (AUTHGUARD()->user()->role === 'khách hàng'): ?>
                            <a href="/account/suspend/<?= htmlspecialchars($user->id_account) ?>" class="btn btn-danger">Tạm
                                dừng tài khoản</a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-success" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#activityTimeline" aria-controls="activityTimeline">
                            Hiển thị dòng thời gian
                        </button>
                    </div>

                <?php endif; ?>

            </div>
        </div>

        <!-- lịch sử hoạt động -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="activityTimeline"
            aria-labelledby="activityTimelineLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-success fw-bold" id="activityTimelineLabel">Lịch sử hoạt động</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                    aria-label="Đóng"></button>
            </div>
            <div class="offcanvas-body">
                <?php if (empty($activities)): ?>
                    <p class="text-muted">Không có hoạt động nào được ghi nhận.</p>
                <?php else: ?>
                    <ul class="timeline">
                        <?php foreach ($activities as $activity): ?>
                            <li class="timeline-item">
                                <span class="time"><?= date("d/m/Y H:i", strtotime($activity->action_time)) ?></span>
                                <div class="timeline-content">
                                    <h6 class="mb-1"><?= htmlspecialchars($activity->action) ?></h6>
                                    <p class="mb-0">Trạng thái: <strong><?= htmlspecialchars($activity->status) ?></strong></p>
                                    <small class="text-muted">Thực hiện bởi:
                                        <?= htmlspecialchars($activity->actor) ?></small>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

    </div>


    <div class="container card p-2">
        <h2 class="fw-bold text-center text-success mb-3 mt-3">Địa chỉ giao hàng</h2>
        <?php if (!empty($errors)): ?>
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

        <?php if (!empty($success) && is_string($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($deliveries as $delivery): ?>
                <div class="col">
                    <div class="address-card h-100 p-3 border">
                        <p><strong>Người nhận:</strong> <?= htmlspecialchars($delivery->receiver_name) ?></p>
                        <p><strong>SĐT:</strong> <?= htmlspecialchars($delivery->receiver_phone) ?></p>
                        <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($delivery->house_number) ?>,
                            <?= htmlspecialchars($delivery->ward) ?>,
                            <?= htmlspecialchars($delivery->district) ?>,
                            <?= htmlspecialchars($delivery->city) ?>
                        </p>
                        <p><strong>Phí giao hàng:</strong> <?= number_format($delivery->shipping_fee) ?> đ</p>
                        <button class="btn mb-2 btn-sm btn-outline-primary" type="button" data-bs-toggle="modal" <?php if (AUTHGUARD()->user()->id_account !== $user->id_account): ?> disabled <?php endif; ?>
                            data-bs-target="#editDeliveryModal<?= $delivery->id_delivery ?>">Chỉnh sửa địa chỉ</button>
                    </div>
                </div>

                <!-- Modal chỉnh sửa -->
                <div class="modal fade" id="editDeliveryModal<?= $delivery->id_delivery ?>" tabindex="-1"
                    aria-labelledby="editDeliveryModalLabel<?= $delivery->id_delivery ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editDeliveryModalLabel<?= $delivery->id_delivery ?>">Chỉnh sửa
                                    địa chỉ
                                    giao hàng</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Form chỉnh sửa địa chỉ -->
                                <form action="/delivery/edit" method="POST" class="row">
                                    <input type="hidden" name="id_delivery" value="<?= $delivery->id_delivery ?>">

                                    <div class="mb-3 col-6">
                                        <label for="receiverName<?= $delivery->id_delivery ?>" class="form-label">Người
                                            nhận</label>
                                        <input type="text" class="form-control"
                                            id="receiverName<?= $delivery->id_delivery ?>" name="receiverName"
                                            value="<?= htmlspecialchars($delivery->receiver_name) ?>" required>
                                    </div>

                                    <div class="mb-3 col-6">
                                        <label for="receiverPhone<?= $delivery->id_delivery ?>"
                                            class="form-label">SĐT</label>
                                        <input type="text" class="form-control"
                                            id="receiverPhone<?= $delivery->id_delivery ?>" name="receiverPhone"
                                            value="<?= htmlspecialchars($delivery->receiver_phone) ?>" required>
                                    </div>

                                    <div class="mb-3 col-6">
                                        <label for="houseNumber<?= $delivery->id_delivery ?>" class="form-label">Địa
                                            chỉ</label>
                                        <input type="text" class="form-control"
                                            id="houseNumber<?= $delivery->id_delivery ?>" name="houseNumber"
                                            value="<?= htmlspecialchars($delivery->house_number) ?>" required>
                                    </div>

                                    <div class="mb-3 col-6">
                                        <label for="ward<?= $delivery->id_delivery ?>" class="form-label">Phường</label>
                                        <input type="text" class="form-control" id="ward<?= $delivery->id_delivery ?>"
                                            name="ward" value="<?= htmlspecialchars($delivery->ward) ?>" required>
                                    </div>

                                    <div class="mb-3 col-6">
                                        <label for="district<?= $delivery->id_delivery ?>" class="form-label">Quận</label>
                                        <input type="text" class="form-control" id="district<?= $delivery->id_delivery ?>"
                                            name="district" value="<?= htmlspecialchars($delivery->district) ?>" required>
                                    </div>

                                    <div class="mb-3 col-6">
                                        <label for="city<?= $delivery->id_delivery ?>" class="form-label">Thành phố</label>
                                        <input type="text" class="form-control" id="city<?= $delivery->id_delivery ?>"
                                            name="city" value="<?= htmlspecialchars($delivery->city) ?>" required>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
    const canvas = document.getElementById("background-canvas");
    const ctx = canvas.getContext("2d");

    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();
</script>

<?php $this->stop() ?>