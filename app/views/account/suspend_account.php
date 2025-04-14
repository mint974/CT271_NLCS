<?php
if (AUTHGUARD()->user()->role === 'khách hàng') {
    $this->layout("layouts/default", ["title" => APPNAME]);
} else {
    $this->layout("layouts/admin", ["title" => APPNAME]);
}
?>
<?php $this->start("page") ?>

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

    .btn-danger-custom {
        background-color: #e63946;
        color: white;
    }

    .btn-danger-custom:hover {
        background-color: #d62828;
    }

    .alert ul {
        padding-left: 1.2rem;
    }
</style>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card profile-card shadow-lg mt-4">
                <canvas id="background-canvas"></canvas>
                <div class="card-body">
                    <h2 class="text-center fw-bold text-danger mb-4">Tạm dừng tài khoản</h2>
                    <?php if (AUTHGUARD()->user()->role !== "quản lý"): ?>
                        <div class="alert alert-warning border-warning shadow-sm" role="alert">
                            <strong class="d-block mb-2">Bạn có chắc chắn muốn tạm dừng tài khoản?</strong>
                            Khi tạm dừng:
                            <ul class="mb-0">
                                <li>Bạn sẽ không thể đăng nhập hoặc sử dụng dịch vụ.</li>
                                <li>Thông tin tài khoản vẫn được giữ lại.</li>
                                <li>Bạn có thể yêu cầu kích hoạt lại bất kỳ lúc nào.</li>
                            </ul>
                        </div>
                        <?php else: ?>
                            <p class="fs-5 fw-bold">Tạm dừng tài khoản: <?= htmlspecialchars($user->username) ?> - <?= htmlspecialchars($user->email) ?></p>
                    <?php endif; ?>
                    <form action="/account/suspend" method="POST">
                        <div class="mb-3">
                            <label for="action" class="form-label"><strong>Lý do tạm dừng tài khoản</strong></label>
                            <select name="action" id="action" class="form-select shadow-sm">
                                <?php if (AUTHGUARD()->user()->role == "quản lý"): ?>
                                    <option value="Hủy đơn quá nhiều lần">Hủy đơn quá nhiều lần</option>
                                    <option value="Vi phạm điều khoản sử dụng">Vi phạm điều khoản sử dụng</option>
                                    <option value="Bảo mật và an toàn">Bảo mật và an toàn</option>
                                <?php else: ?>
                                    <option value="Tôi tìm thấy nơi khác rẻ hơn">Tôi tìm thấy nơi khác rẻ hơn</option>
                                    <option value="Tôi không tìm thấy sản phẩm tôi cần">Tôi không tìm thấy sản phẩm tôi cần
                                    </option>
                                    <option value="Không hài lòng với chính sách đổi trả">Không hài lòng với chính sách đổi
                                        trả</option>
                                <?php endif; ?>

                                <option value="others">Khác</option>
                            </select>
                        </div>

                        <div class="mb-3 d-none" id="custom-reason-wrapper">
                            <label for="custom_reason" class="form-label"><strong>Nhập lý do khác</strong></label>
                            <input type="text" class="form-control" id="custom_reason" name="custom_reason">
                        </div>

                        <div class="mb-3 d-none">
                            <input type="text" class="form-control" id="id_account" name="id_account"
                                value="<?= htmlspecialchars($user->id_account) ?>">
                        </div>
                        <div class="mb-3 d-none">
                            <input type="text" class="form-control" id="status" name="status"
                                value="Vô hiệu hóa tài khoản">
                        </div>
                        <button type="submit" class="btn btn-danger-custom w-100 fw-bold">Xác nhận tạm dừng tài
                            khoản</button>
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

    const selectAction = document.getElementById("action");
    const customReasonWrapper = document.getElementById("custom-reason-wrapper");
    const customReasonInput = document.getElementById("custom_reason");

    selectAction.addEventListener("change", function () {
        if (this.value === "others") {
            customReasonWrapper.classList.remove("d-none");
            customReasonInput.setAttribute("name", "action");
        } else {
            customReasonWrapper.classList.add("d-none");
            customReasonInput.removeAttribute("name");
        }
    });
</script>

<?php $this->stop() ?>