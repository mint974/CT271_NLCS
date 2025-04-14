<?php
if (AUTHGUARD()->user()->role === 'khách hàng') {
    $this->layout("layouts/default", ["title" => APPNAME]);
} else {
    $this->layout("layouts/admin", ["title" => APPNAME]);
}
?>
<?php $this->start("page") ?>
<style>
    :root {
        --bg-dark-green-color: #08a045;
        --bg-green-color: #29bf12;
        --bg-light-green-color: #abff4f;
        --yellow-color: #f3de2c;
        --blue-color: #08bdbd;
        --light-blue-color: #a8d5e2;
        --red-color: #f21b3f;
        --orange-color: #ff9914;
        --while-color: #FFFFFF;
        --bg-1-color: #e8fccf;
        --bg-2-color: #f5fdc6;
    }

    /* body{
        background-color:;
    } */
    /* product detai */
    .product-detail-page {
        /* background-color: var(--bg-1-color); */
        padding: 20px;
        /* margin-top: -30px; */
        background-color: #fff;
        border-radius: 15px;
    }

    .price {
        color: var(--bg-dark-green-color);
    }

    /* nút tăng giảm */
    .number-input {
        display: flex;
        align-items: center;
        width: 120px;
        border: 2px solid #08a045;
        /* Viền màu xanh lá */
        border-radius: 8px;
        overflow: hidden;
        background: white;
    }

    .number-input button {
        width: 35px;
        height: 40px;
        background-color: #08a045;
        /* Nút màu xanh lá */
        color: white;
        border: none;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }

    .number-input button:hover {
        background-color: #065e2d;
        /* Màu xanh đậm hơn khi hover */
    }

    .number-input input {
        width: 50px;
        height: 40px;
        text-align: center;
        border: none;
        font-size: 16px;
        font-weight: bold;
        color: #333;
    }


    /* delivery */
    .delivery-policy {
        background: var(--bg-2-color);
        border-left: 5px solid var(--bg-green-color);
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    .delivery-policy h3 {
        color: var(--bg-dark-green-color);
        font-weight: bold;
    }

    .delivery-policy ul {
        padding-left: 0;
    }

    .delivery-policy li {
        font-size: 18px;
        margin: 8px 0;
        display: flex;
        align-items: center;
    }

    .delivery-policy .icon {
        font-size: 22px;
        margin-right: 10px;
    }

    .delivery-policy-list li {
        line-height: 1.5;
        letter-spacing: 1px;
        margin-bottom: 10px;
        /* Tạo khoảng cách giữa các dòng */
    }

    .custom-hr {
        border: none;
        height: 2px;
        background: linear-gradient(to right, rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.1));
        margin: 20px auto;
        width: 80%;
        box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.2);
    }

    /* discount box */
    .promotion-box {
        padding: 20px;
        text-align: center;
        background: var(--while-color);
        /* Nền trắng */
        border: 1px solid #ddd;
        /* Viền xám nhạt */
        border-radius: 10px;
        box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
        /* Đổ bóng nhẹ */
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    }

    .promotion-box:hover {
        transform: translateY(-3px);
        box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.15);
    }

    .promo-icon {
        font-size: 2rem;
        margin-bottom: 10px;
        color: #333;
        /* Màu xám đậm */
    }

    .promotion-box h5 {
        font-size: 1.3rem;
        font-weight: bold;
        color: #222;
        /* Màu đen nhạt */
    }

    .promotion-box p {
        color: #555;
        /* Màu xám trung tính */
        font-size: 1rem;
    }

    .discount {
        color: #d9534f;
        /* Màu đỏ nhạt */
        font-weight: bold;
        font-size: 1.2rem;
    }

    
.new-price {
    font-size: 22px;
    color: var(--red-color);
}

.old-price {
    font-size: 14px;
    text-decoration: line-through;
    color: rgba(0, 0, 0, 0.5);
}

</style>

<div class="product-detail-page container-fluid">
    <div class="container">
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
                        <strong><?= htmlspecialchars($products->name) ?></strong>
                    </h2>
                    <p class="fs-5"><strong>Mô tả: </strong> <span class="text-muted"
                            style="letter-spacing: 1px;"><?= htmlspecialchars($products->description) ?></span></p>
                    <p class="fs-5"><strong>Kho hàng:</strong> <?= htmlspecialchars($products->quantity) ?></p>
                    <p class="fs-5"><strong>Đơn vị tính: </strong> (0.95-1.05) <?= htmlspecialchars($products->unit) ?>
                    </p>
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

                    <?php if (AUTHGUARD()->user()->role === 'khách hàng'): ?>
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
                    <?php else: ?>
                        <a href="/products/update/<?= $product['id_product'] ?>" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil-square"></i> Chỉnh sửa
                        </a>
                    <?php endif; ?>
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

        <?php if (AUTHGUARD()->user()->role === 'khách hàng'): ?>
            <div class="col-md-12 mt-4">
                <div class="delivery-policy container-fluid p-4 text-center">
                    <h3 class="text-uppercase">🚚 Chính sách giao hàng</h3>
                    <p class="text-muted">Chúng tôi hỗ trợ giao hàng với các điều kiện sau:</p>

                    <ul class="list-unstyled delivery-policy-list">
                        <li><span class="icon">✅</span> Giao hàng nội thành trong vòng 24h.</li>
                        <li><span class="icon">✅</span> Giao hàng liên tỉnh khi đơn hàng từ <strong> 800.000 VND</strong>.
                        </li>
                        <li><span class="icon">✅</span> Đóng gói kỹ lưỡng, đảm bảo độ tươi ngon.</li>
                    </ul>

                    <p class="text-danger"><strong>Lưu ý:</strong> Đối với các đơn hàng đặc biệt, vui lòng liên hệ để được
                        tư vấn!</p>
                </div>
            </div>

        <?php else: ?>
            <div class="col-md-12 mt-4 d-flex flex-column justify-content-center align-items-center">
                <p>admin</p>


                <a href="/products/admin" class="btn btn-outline-primary btn-sm ">
                    <i class="bi bi-arrow-left-circle"></i> Quay về
                </a>

            </div>

        <?php endif; ?>

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