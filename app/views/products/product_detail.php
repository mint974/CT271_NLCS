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

    /* Lịch sử nhập hàng */
    .table {
        background-color: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .table thead th {
        background-color: var(--bg-dark-green-color);
        color: #fff;
        font-weight: 600;
        vertical-align: middle;
        font-size: 15px;
    }

    .table tbody td {
        vertical-align: middle;
        font-size: 14px;
        color: #333;
        background-color: #fefefe;
    }

    .table-bordered th,
    .table-bordered td {
        border-color: #dee2e6;
    }

    .table tbody tr:hover {
        background-color: #f0fff4;
        /* nhẹ nhàng khi hover */
    }

    /* Icon trong tiêu đề bảng */
    .table thead th i,
    .table thead th.bi {
        margin-right: 5px;
    }

    /* Nút quay về */
    .btn-outline-primary {
        margin-top: 15px;
        border-radius: 20px;
        padding: 5px 20px;
        transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
        background-color: var(--blue-color);
        color: white;
        border-color: var(--blue-color);
    }

    .catalog_detail {
        background-color: var(--bg-1-color);
        border-left: 5px solid var(--bg-green-color);
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        font-size: 16px;
        color: #333;
    }

    .catalog_detail h4 {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--bg-dark-green-color);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .catalog_detail p {
        margin-bottom: 5px;
        font-weight: 500;
        padding-left: 5px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 15px;
    }

    .catalog_detail p i {
        color: var(--orange-color);
        font-size: 1.2rem;
    }

    .catalog_detail hr {
        margin: 10px 0;
        border-top: 1px dashed #ccc;
    }
</style>

<div class="product-detail-page container-fluid mb-3">
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
                        <div class="row row-cols-1 row-cols-md-1 g-3 mt-4">
                            <div class="col">
                                <a href="/products/updateInfor/<?= $this->e($products->id_product) ?>"
                                    class="btn btn-warning w-100 shadow-sm rounded-3 d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-pencil-square"></i>
                                    <span>Sửa thông tin sản phẩm</span>
                                </a>
                            </div>
                            <div class="col">
                                <a href="/products/update/<?= $this->e($products->id_product) ?>"
                                    class="btn btn-info w-100 shadow-sm rounded-3 d-flex align-items-center justify-content-center gap-2 text-white">
                                    <i class="bi bi-image"></i>
                                    <span>Sửa ảnh sản phẩm</span>
                                </a>
                            </div>
                            <div class="col">
                                <a href="/products/update/<?= $this->e($products->id_product) ?>"
                                    class="btn btn-success w-100 shadow-sm rounded-3 d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-tags"></i>
                                    <span>Sửa danh mục sản phẩm</span>
                                </a>
                            </div>
                        </div>




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

                <div class="mt-3 mb-2">
                    <div class="catalog_detail">
                        <h4><i class="bi bi-tags-fill text-success"></i> DANH MỤC</h4>
                        <?php foreach ($catalogs as $catalog): ?>
                            <p><i class="bi bi-box-seam-fill"></i><?= htmlspecialchars($catalog->name); ?></p>
                            <hr>
                        <?php endforeach; ?>
                    </div>

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
                <h2 class="text-center mb-3" style="color: #08a045;">Lịch sử nhập hàng</h2>

                <table class="table table-bordered align-middle text-center">
                    <thead class="table-success">
                        <tr>
                            <th>STT</th>
                            <th class="bi bi-calendar-plus">Ngày nhập</th>
                            <th class="bi bi-hash">Mã đơn</th>
                            <th class="bi bi-person">Người lập</th>
                            <th class="bi bi-geo-alt">Nhà cung cấp</th>
                            <th><i class="bi bi-stack"></i> Số lượng</th>
                            <th><i class="bi bi-cash-coin"></i> Giá mua</th>
                            <th><i class="bi bi-currency-dollar"></i> Giá bán</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($receipt_details_full as $index => $item): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= $this->e($item['receipt']->created_at) ?></td>
                                <td><?= $this->e($item['receipt']->id_receipt) ?></td>
                                <td><?= $this->e($item['createdBy']->username) ?></td>
                                <td><?= $this->e($item['supplier']->name) ?></td>
                                <td><?= $this->e($item['detail']->quantity) ?></td>
                                <td><?= number_format($item['detail']->purchase_price) ?> đ</td>
                                <td><?= number_format($item['detail']->selling_price) ?> đ</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

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