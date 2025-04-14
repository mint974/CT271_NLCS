<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>

<?php $this->start("page_specific_css") ?>
<link href="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.0.8/r-3.0.2/sp-2.3.1/datatables.min.css" rel="stylesheet">
<?php $this->stop() ?>


<?php $this->start("page") ?>

<div class="container-fuild homepage products p-1">

    <div class="container">
    <div class="container-fluid row m-3 position-relative d-flex justify-content-center align-items-center">
        <img src="/assets/image/products/banner1.png" class="img-fluid w-100" alt="">
        <h1 class="position-absolute text-center banner-heading">DANH MỤC SẢN PHẨM</h1>
    </div>
    <!-- <h1 class="dd">sds</h1> -->
    <div class="container-fuild">
        <div class=" row row-cols-2">
            <!-- catalog -->
            <div class=" container col col-2 catalog mt-5">
                <div class="mt-3 mb-2">
                    <h3>DOANH MỤC</h3>
                    <div class="catalog_detail">
                        <form action="/products/load_prod_cata" method="POST">
                            <input type="hidden" name="discountproduct" value="discountproduct">
                            <button type="submit" class="catalog-btn">Trái cây giảm giá</button>
                        </form>
                        <hr>
                        <?php foreach ($catalogs as $catalog): ?>
                            <form action="/products/load_prod_cata" method="POST">
                                <input type="hidden" name="id_catalog"
                                    value="<?php echo htmlspecialchars($catalog['id_catalog']); ?>">
                                <button type="submit"
                                    class="catalog-btn"><?php echo htmlspecialchars($catalog['name']); ?></button>
                            </form>
                            <hr>
                        <?php endforeach; ?>

                    </div>

                </div>
                <div class="mb-3">
                    <h3 >SẢN PHẨM BÁN CHẠY NHẤT</h3>

                </div>
                <img src="/assets/image/products/banner_prod1.png" width="100%" class="mb-3" alt="">
                <img src="/assets/image/products/banner_prod2.png" width="100%" class="mb-3" alt="">
            </div>

            <!-- product -->
            <div class="col col-10">
                <!-- TRÁI CÂY VIỆT NAM -->
                <?php if (isset($productbycata) && !empty($productbycata)): ?>
                    <div
                        class="container-fluid product-box other-box d-flex flex-column align-items-center justify-content-center">
                        <h1><b><?php echo htmlspecialchars($productbycata->name); ?></b></h1>

                        <div class="row row-cols-3 row-cols-md-4 g-1 product-list">
                            <?php
                            $product_list = $productbycata->product_list;
                            ?>
                            <?php foreach ($product_list as $product): ?>
                                <div class="col product-card p-3 product-item">
                                    <a href="<?= '/products/proddetail/' . $this->e($product['id_product']) ?>">
                                        <div class="card    h-100 position-relative">
                                            <?php
                                            // Lấy danh sách ảnh, chọn ảnh đầu tiên nếu có, nếu không dùng ảnh mặc định
                                            $images3 = explode(',', $product['images'] ?? '');
                                            $firstImage3 = !empty($images3[0]) ? $images3[0] : '/assets/image/default.jpg';
                                            ?>
                                            <img src="<?php echo htmlspecialchars($firstImage3); ?>" class="card-img-top"
                                                alt="<?php echo htmlspecialchars($product['name']); ?>">
                                            <div class="card-body">
                                                <h5 class="card-title fs-4" style="height: 40px;">
                                                    <?php echo htmlspecialchars($product['name']); ?>
                                                </h5>
                                                <p class="card-text fs-5 pt-2">
                                                    <?php echo number_format($product['price'], 0, ',', '.'); ?> đ
                                                </p>
                                                <form action="<?= '/products/addprod/' . $this->e($product['id_product']) ?>"
                                                    method="post">
                                                    <button type="submit" class="btn my-btn btn-outline-success">
                                                        <i class="fa fa-plus orange-color"></i> Thêm vào giỏ
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    </div>
                <?php elseif (isset($discounted_Products) && !empty($discounted_Products)): ?>
                    <div
                        class="container-fluid product-box sale-box d-flex flex-column align-items-center justify-content-center">
                        <h1><b>TRÁI CÂY KHUYẾN MÃI</b></h1>

                        <div class="row row-cols-4 row-cols-md-5 g-4 product-list" id="discounted-product-list">
                            <?php foreach ($discounted_Products as $product1): ?>
                                <?php
                                $images1 = explode(',', $product1['images'] ?? '');
                                $firstImage1 = !empty($images1[0]) ? $images1[0] : '/assets/image/default.jpg';
                                $discountedPrice = $product1['price'] * (1 - $product1['discount_rate'] / 100);
                                ?>
                                <div class="col p-3 " id="discounted-product-list">
                                    <a href="<?= '/products/proddetail/' . $this->e($product1['id_product']) ?>">
                                        <div class="card h-100 position-relative">
                                            <img src="<?php echo htmlspecialchars($firstImage1); ?>" class="card-img-top"
                                                alt="<?php echo htmlspecialchars($product1['name']); ?>">

                                            <div class="discount_rate">
                                                <p class="fs-5 m-1" class="color: black;">
                                                    <?php echo htmlspecialchars($product1['discount_rate']); ?>%
                                                </p>
                                            </div>

                                            <div class="card-body">
                                                <h5 class="card-title fs-4" style="height: 40px;">
                                                    <?php echo htmlspecialchars($product1['name']); ?>
                                                </h5>
                                                <div class="d-flex align-items-center">
                                                    <p class="mx-2 new-price">
                                                        <?php echo number_format($discountedPrice, 0, ',', '.'); ?> đ
                                                    </p>
                                                    <p class="mx-2 old-price">
                                                        <?php echo number_format($product1['price'], 0, ',', '.'); ?> đ
                                                    </p>
                                                </div>
                                                <form action="<?= '/products/addprod/' . $this->e($product1['id_product']) ?>"
                                                    method="post">
                                                    <button type="submit" class="btn my-btn btn-outline-success">
                                                        <i class="fa fa-plus orange-color"></i> Thêm vào giỏ
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>



                    <?php else: ?>
                        <div
                            class=" container-fluid product-box VN-box d-flex flex-column align-items-center justify-content-center pb-4">
                            <h1><b>TRÁI CÂY VIỆT NAM</b></h1>

                            <div class="row row-cols-4 row-cols-md-5 g-4 product-list">
                                <?php
                                // Lấy danh sách sản phẩm từ danh mục đầu tiên (catalogs[0])
                                $products1 = $catalogs[0]['product_list'] ?? [];
                                ?>

                                <?php foreach ($products1 as $index => $product2): ?>
                                    <div class="col product-card p-3 product-item"
                                        style="display: <?php echo $index < 10 ? 'block' : 'none'; ?>;">
                                        <a href="<?= '/products/proddetail/' . $this->e($product2['id_product']) ?>">
                                            <div class="card h-100 position-relative">
                                                <?php
                                                // Lấy danh sách ảnh, chọn ảnh đầu tiên nếu có, nếu không dùng ảnh mặc định
                                                $images2 = explode(',', $product2['images'] ?? '');
                                                $firstImage2 = !empty($images2[0]) ? $images2[0] : '/assets/image/default.jpg';
                                                ?>
                                                <img src="<?php echo htmlspecialchars($firstImage2); ?>" class="card-img-top"
                                                    alt="<?php echo htmlspecialchars($product2['name']); ?>">
                                                <div class="card-body">
                                                    <h5 class="card-title fs-4" style="height: 40px;">
                                                        <?php echo htmlspecialchars($product2['name']); ?>
                                                    </h5>
                                                    <p class="card-text fs-5">
                                                        <?php echo number_format($product2['price'], 0, ',', '.'); ?> đ
                                                    </p>
                                                    <form
                                                        action="<?= '/products/addprod/' . $this->e($product2['id_product']) ?>"
                                                        method="post">
                                                        <button type="submit" class="btn my-btn btn-outline-success">
                                                            <i class="fa fa-plus orange-color"></i> Thêm vào giỏ
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>

                            </div>

                            <button type="button" class="btn my-btn btn-outline-success mt-3 load-more">
                                <i class="fas fa-angle-down"></i> XEM THÊM
                            </button>
                        </div>


                        <!-- TRÁI CÂY NƯỚC NGOÀI -->
                        <div
                            class="container-fluid product-box other-box d-flex flex-column align-items-center justify-content-center">
                            <h1><b>TRÁI CÂY NHẬP KHẨU</b></h1>

                            <div class="row row-cols-4 row-cols-md-5 g-4 product-list">
                                <?php

                                $products2 = $catalogs[1]['product_list'] ?? [];
                                ?>
                                <?php foreach ($products2 as $index => $product3): ?>
                                    <div class="col product-card p-3 product-item"
                                        style="display: <?php echo $index < 10 ? 'block' : 'none'; ?>;">
                                        <a href="<?= '/products/proddetail/' . $this->e($product3['id_product']) ?>">
                                            <div class="card h-100 position-relative">
                                                <?php
                                                // Lấy danh sách ảnh, chọn ảnh đầu tiên nếu có, nếu không dùng ảnh mặc định
                                                $images3 = explode(',', $product3['images'] ?? '');
                                                $firstImage3 = !empty($images3[0]) ? $images3[0] : '/assets/image/default.jpg';
                                                ?>
                                                <img src="<?php echo htmlspecialchars($firstImage3); ?>" class="card-img-top"
                                                    alt="<?php echo htmlspecialchars($product3['name']); ?>">
                                                <div class="card-body">
                                                    <h5 class="card-title fs-4" style="height: 40px;">
                                                        <?php echo htmlspecialchars($product3['name']); ?>
                                                    </h5>
                                                    <p class="card-text fs-5">
                                                        <?php echo number_format($product3['price'], 0, ',', '.'); ?> đ
                                                    </p>
                                                    <form
                                                        action="<?= '/products/addprod/' . $this->e($product3['id_product']) ?>"
                                                        method="post">
                                                        <button type="submit" class="btn my-btn btn-outline-success">
                                                            <i class="fa fa-plus orange-color"></i> Thêm vào giỏ
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <button type="button" class="btn my-btn btn-outline-success mt-3 load-more">
                                <i class="fas fa-angle-down"></i> XEM THÊM
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
<script>

    //load thêm sản phẩm
    document.addEventListener("DOMContentLoaded", function () {
        // Xử lý cho các sản phẩm thường
        document.querySelectorAll(".product-box").forEach(section => {
            let products = section.querySelectorAll(".product-item");
            let loadMoreBtn = section.querySelector(".load-more");
            let itemsPerPage = 10;
            let currentIndex = 10;

            if (loadMoreBtn) {
                loadMoreBtn.addEventListener("click", function () {
                    let totalProducts = products.length;

                    for (let i = currentIndex; i < currentIndex + itemsPerPage && i < totalProducts; i++) {
                        products[i].style.display = "block";
                    }

                    currentIndex += itemsPerPage;

                    if (currentIndex >= totalProducts) {
                        loadMoreBtn.style.display = "none";
                    }
                });
            }
        });

        // Xử lý cho sản phẩm khuyến mãi
        let discountedItems = document.querySelectorAll("#discounted-product-list .product-item1");
        let loadMoreBtn1 = document.getElementById("load-more-discounted");
        let itemsPerPage1 = 4;
        let currentIndex1 = 4;

        if (loadMoreBtn1) {
            loadMoreBtn1.addEventListener("click", function () {
                let totalItems = discountedItems.length;
                let nextIndex = currentIndex1 + itemsPerPage1;

                for (let i = currentIndex1; i < nextIndex && i < totalItems; i++) {
                    discountedItems[i].style.display = "block";
                }

                currentIndex1 = nextIndex;

                if (currentIndex1 >= totalItems) {
                    loadMoreBtn1.style.display = "none";
                }
            });
        }
    });
</script>
<?php $this->stop() ?>