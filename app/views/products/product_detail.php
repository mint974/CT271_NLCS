<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>

<?php $this->start("page_specific_css") ?>
<link href="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.0.8/r-3.0.2/sp-2.3.1/datatables.min.css" rel="stylesheet">
<?php $this->stop() ?>



<?php $this->start("page") ?>
<div class="product-detail-page container-fluid">
    <div class="row">
        <!-- Carousel Box -->
        <div class="col-md-4  mb-3">
            <div id="productCarousel" class="carousel border slide" data-bs-ride="carousel">
                <div class="carousel-inner p-4">
                    <?php foreach ($products->images as $index => $image): ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                            <img src="<?= htmlspecialchars(trim($image)) ?>" style="width: 180px; height: 300px;"
                                class="d-block w-100" alt="Hình ảnh sản phẩm">
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#productCarousel"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>

            <!-- Thumbnail Images -->
            <div class="thumbnail-images row row-cols-auto mt-2 mx-1 text-center">
                <?php foreach ($products->images as $index => $image): ?>
                    <div class="col p-1 ">
                        <img src="<?= htmlspecialchars(trim($image)) ?>"
                            class="img-thumbnail thumb-img <?= $index === 0 ? 'active' : '' ?>"
                            style="width: 100px; height: 100px;" onclick="setCarousel(<?= $index ?>)"
                            data-index="<?= $index ?>">
                    </div>
                <?php endforeach; ?>
            </div>

        </div>

        <!-- Thông tin sản phẩm -->
        <div class="col-md-5">
            <div class="product-info">
                <h2 class="text-center " style="color: #08a045;">
                    <strong><?= htmlspecialchars($products->name) ?></strong></h2>
                <p class="fs-5"><strong>Mô tả: </strong> <span class="text-muted"
                        style="letter-spacing: 1px;"><?= htmlspecialchars($products->description) ?></span></p>
                <p class="fs-5"><strong>Kho hàng:</strong> <?= htmlspecialchars($products->quantity) ?></p>
                <p class="fs-5"><strong>Đơn vị tính: </strong> (0.95-1.05) <?= htmlspecialchars($products->unit) ?></p>
                <?php if (!empty($products->promotion)): ?>
                    <?php $discountedPrice = $products->price * (1 - $products->promotion['discount_rate'] / 100); ?>
                    <div class="d-flex align-items-center">
                        <p class="fs-5"><strong>Giá:</strong></p>
                        <p class=" mx-2 new-price fs-5" style="font-size: medium;">
                            <?php echo number_format($discountedPrice, 0, ',', '.'); ?> đ
                        </p>
                        <p class="mx-2 old-price fs-5"><?php echo number_format($products->price, 0, ',', '.'); ?> đ</p>
                    </div>
                <?php else: ?>
                    <p class="fs-5"><strong>Giá:</strong> <span
                            class="price"><?= number_format($products->price, 0, ',', '.') ?>
                            VND</span></p>
                <?php endif; ?>
                <form action="<?= '/products/addprod/' . $this->e($products->id_product) ?>" method="post">
                    <div class="number-input" style="width: 103px;">
                        <button class="minus" onclick="decreaseValue(event)">-</button>
                        <input type="number" id="quantity" name="quantity" value="1" min="1">
                        <button class="plus" onclick="increaseValue(event)">+</button>
                    </div>

                    <button type="submit" class="btn my-btn mt-3 btn-outline-success w-100">
                        <i class="fa fa-plus orange-color"></i> Thêm vào giỏ
                    </button>
                </form>

            </div>
        </div>

        <!-- Hiển thị chương trình khuyến mãi nếu có -->
        <div class="col-md-3">
            <div class="promotion-box">
                <div class="promo-icon">🎁</div>
                <h5><?= htmlspecialchars($products->promotion['name'] ?? 'Không có chương trình') ?></h5>
                <p><?= htmlspecialchars($products->promotion['description'] ?? 'Không có mô tả') ?></p>
                <p><strong>Giảm giá:</strong> <span
                        class="discount"><?= $products->promotion['discount_rate'] ?? 'N/A' ?>%</span></p>
                <p><strong>Thời gian:</strong>
                    <?= date('d/m/Y', strtotime($products->promotion['start_day'] ?? 'now')) ?> -
                    <?= date('d/m/Y', strtotime($products->promotion['end_day'] ?? 'now')) ?>
                </p>
            </div>
        </div>





    </div>

    <hr class="custom-hr">
    <div class="col-md-12 mt-4">
        <div class="delivery-policy container-fluid p-4 text-center">
            <h3 class="text-uppercase">🚚 Chính sách giao hàng</h3>
            <p class="text-muted">Chúng tôi hỗ trợ giao hàng với các điều kiện sau:</p>

            <ul class="list-unstyled delivery-policy-list">
                <li><span class="icon">✅</span> Giao hàng nội thành trong vòng 24h.</li>
                <li><span class="icon">✅</span> Miễn phí giao hàng cho đơn từ <strong>400.000 VND</strong>.</li>
                <li><span class="icon">✅</span> Giao hàng liên tỉnh khi đơn hàng từ <strong> 1.000.000 VND</strong>.
                </li>
                <li><span class="icon">✅</span> Đóng gói kỹ lưỡng, đảm bảo độ tươi ngon.</li>
            </ul>

            <p class="text-danger"><strong>Lưu ý:</strong> Đối với các đơn hàng đặc biệt, vui lòng liên hệ để được
                tư vấn!</p>
        </div>
    </div>


</div>

<script>
    function setCarousel(index) {
        var carousel = new bootstrap.Carousel(document.getElementById('productCarousel'));
        carousel.to(index);

        // Cập nhật trạng thái active cho ảnh thumbnail
        document.querySelectorAll(".thumbnail-images img").forEach((img, idx) => {
            img.classList.toggle("active", idx === index);
        });
    }

    function increaseValue(event) {
        event.preventDefault(); // Ngăn trang bị load lại
        let input = document.getElementById("quantity");
        input.value = parseInt(input.value) + 1;
    }

    function decreaseValue(event) {
        event.preventDefault(); // Ngăn trang bị load lại
        let input = document.getElementById("quantity");
        if (parseInt(input.value) > parseInt(input.min)) {
            input.value = parseInt(input.value) - 1;
        }
    }


</script>

<?php $this->stop() ?>