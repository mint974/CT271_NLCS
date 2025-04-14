<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>
<?php $this->start("page") ?>
<?php 
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<style>
    .contact-section {
        background: #f4fdf1;
        padding: 60px 0;
        border-radius: 20px;
    }

    .contact-form,
    .contact-info-card {
        background: #fff;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .form-control,
    .form-select {
        border-radius: 30px;
        padding: 12px 20px;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #08bdbd;
        box-shadow: 0 0 10px rgba(8, 189, 189, 0.2);
    }

    .map-embed {
        border-radius: 20px;
        height: 300px;
        width: 100%;
        border: 0;
    }

    .info-icon {
        color: #29bf12;
    }

    .social {
        background-image: url('/assets/image/contacts/social.jpg');
        background-size: cover;
        background-position: center;
        border-radius: 20px;
        padding: 100px 80px;
        width: 60%;
        position: relative;
        overflow: hidden;
    }

    /* Overlay mờ để dễ đọc chữ */
    .social::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        /* nền đen mờ */
        z-index: 1;
    }

    /* Nội dung bên trong overlay */
    .social-info {
        position: relative;
        z-index: 2;
        color: #fff;
    }

    /* Chữ và icon */
    .social-info h2 {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 20px;
        color: #ffffff;
    }

    .social-icons a {
        color: #ffffff;
        margin: 0 15px;
        transition: 0.3s;
    }

    .social-icons a:hover {
        color: #fbd46d;
        transform: scale(1.2);
    }

    .contact-image {
        width: 100%;
        border-radius: 20px;
        object-fit: cover;
        height: 300px;
        margin-top: 20px;
    }
</style>

<div class="container contact-section ">
    <h1 class="text-center fw-bold mb-5">LIÊN HỆ VỚI CHÚNG TÔI</h1>
    <div class="row row-cols-1 row-cols-md-2 g-4">
        <!-- Contact Form -->
        <div class="col-md-6 ">
            <div class="contact-form h-100">
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
                <?php if (!AUTHGUARD()->user()): ?>
                    <div class="alert alert-warning text-center mt-3" role="alert">
                        <strong>Chú ý:</strong> Bạn cần <a href="/login" class="alert-link">đăng nhập</a> để gửi liên hệ!
                    </div>
                <?php endif; ?>

                <form action="/contacts/save" method="post">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

                    <div class="mb-3">
                        <input type="text" name="username" class="form-control" placeholder="Tên đăng nhập"
                            value="<?= isset($user->username) ? htmlspecialchars($user->username) : ''; ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email (*)"
                            value="<?= isset($user->email) ? htmlspecialchars($user->email) : ''; ?>" disabled>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <input type="text" name="phone" class="form-control" placeholder="Số điện thoại (*)"
                                required>
                        </div>
                        <div class="col-6 mb-3">
                            <select name="subject" class="form-select">
                                <option value="Góp ý chung">Góp ý chung</option>
                                <option value="Báo lỗi">Báo lỗi</option>
                                <option value="Đề xuất cải thiện">Đề xuất cải thiện</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <textarea name="content" class="form-control" rows="4" placeholder="Nội dung liên hệ (*)"
                            required></textarea>
                    </div>

                    <button type="submit" class="btn btn-success w-100 rounded-pill" <?= !AUTHGUARD()->user() ? 'disabled' : ''; ?>>
                        Gửi liên hệ
                    </button>
                </form>
            </div>
        </div>

        <!-- Map -->
        <div class="col-md-6">
            <div class="contact-info-card h-100">
                <iframe class="map-embed mb-3"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3916.445899577631!2d105.768426114287!3d10.02993369283137!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31a089c1b6a1e8a3%3A0x4e6d9a0a2b4e6a0a!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBD4bqnbiBUaMO0!5e0!3m2!1svi!2s!4v1610000000000!5m2!1svi!2s"
                    allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>

                <div class="text-center">
                    <p><i class="fas fa-map-marker-alt fa-lg info-icon me-2"></i>91 Đ. 3/2, P. Xuân Khánh, Ninh Kiều,
                        Cần Thơ</p>
                    <p><i class="fas fa-phone fa-lg info-icon me-2"></i>0775097409</p>
                    <p><i class="fas fa-envelope fa-lg info-icon me-2"></i>mint1224@gmail.com</p>
                </div>
            </div>
        </div>

        <!-- Image -->

    </div>


    <div class="social text-center mt-3 mx-auto">
        <div class="social-info">
            <h2><strong><b>THEO DỖI CHÚNG TÔI</b></strong>
            </h2>
            <div class="social-icons mt-2">
                <a href="https://facebook.com"><i class="fab fa-facebook fa-2x"></i></a>
                <a href="https://instagram.com"><i class="fab fa-instagram fa-2x"></i></a>
                <a href="https://youtube.com"><i class="fab fa-youtube fa-2x"></i></a>
                <a href="https://twitter.com"><i class="fab fa-twitter fa-2x"></i></a>
            </div>
        </div>
    </div>

</div>

<?php $this->stop() ?>