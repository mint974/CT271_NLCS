<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>

<?php $this->start("page") ?>
<style>
    body{
        background-color: #e8fccf;
    }
    .search-results {
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
    }

    .product-card .card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s;
    }

    .product-card .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }
</style>


<div class="container mt-4 search-results">
    <h2 class="text-center mb-4">
        Kết quả tìm kiếm cho: <strong><?= htmlspecialchars($keyword) ?></strong>
    </h2>

    <?php if (empty($results)): ?>
        <div class="alert alert-warning text-center fs-4" role="alert">
            Không tìm thấy sản phẩm nào phù hợp với từ khóa "<strong><?= htmlspecialchars($keyword) ?></strong>".
        </div>
        <a href="/" class="btn btn-primary btn-lg w-100">
            <i class="fa fa-home me-2"></i> Quay về trang chủ
        </a>
    <?php else: ?>
        <div class="row row-cols-2 row-cols-md-5 g-4 product-list">
            <?php foreach ($results as $product): ?>
                <?php
                $images = explode(',', $product['images'] ?? '');
                $firstImage = !empty($images[0]) ? $images[0] : '/assets/image/default.jpg';
                ?>
                <div class="col product-card">
                    <div class="card h-100 position-relative">
                        <a href="<?= '/products/proddetail/' . htmlspecialchars($product['id_product']) ?>">
                            <img src="<?= htmlspecialchars($firstImage) ?>" loading="lazy" class="card-img-top"
                                 alt="<?= htmlspecialchars($product['name']) ?>">
                        </a>
                        <?php if ($product['id_promotion']): ?>
                            <div class="discount_rate">
                                <p class="fs-5 m-1" style="color: white !important;">
                                    <?= htmlspecialchars($product['discount_rate']) ?>%
                                </p>
                            </div>
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column justify-content-between">
                            <h5 class="card-title fs-5" style="min-height: 38px;">
                                <a href="<?= '/products/proddetail/' . htmlspecialchars($product['id_product']) ?>"
                                   class="text-decoration-none text-dark">
                                    <?= htmlspecialchars($product['name']) ?>
                                </a>
                            </h5>
                            <?php if ($product['id_promotion']): ?>
                                <div class="d-flex align-items-center">
                                    <p class="mx-2 new-price" style="font-size: 22px; color: #f21b3f;">
                                        <?= number_format($product['discounted_price'], 0, ',', '.') ?> đ
                                    </p>
                                    <p class="mx-2 old-price"
                                       style="font-size: 14px; text-decoration: line-through; color: rgba(0, 0, 0, 0.5);">
                                        <?= number_format($product['price'], 0, ',', '.') ?> đ
                                    </p>
                                </div>
                            <?php else: ?>
                                <p class="card-text fs-6">
                                    <?= number_format($product['price'], 0, ',', '.') ?> đ /
                                    <?= htmlspecialchars($product['unit']) ?>
                                </p>
                            <?php endif; ?>
                            <form action="<?= '/products/addprod/' . htmlspecialchars($product['id_product']) ?>" method="post">
                                <button type="submit" class="btn my-btn btn-outline-success w-100 mt-2">
                                    <i class="fa fa-plus orange-color"></i> Thêm vào giỏ
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php htmlspecialcharsnd() ?>
