<?php $this->layout("layouts/admin", ["title" => APPNAME]) ?>
<?php $this->start("page") ?>

<style>
    .card {
        border-radius: 1rem;
        transition: 0.3s ease-in-out;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }

    .form-floating>label {
        padding: 0.5rem 0.75rem;
    }

    .back-btn {
        margin-bottom: 1.5rem;
    }

    .form-check-input:checked~label {
        color: #0d6efd;
    }

    .product-card {
        border-left: 4px solid #0d6efd;
        background-color: #f8f9fa;
    }

    .card-title {
        font-size: 1.1rem;
    }

    .product-image {
        display: block;
        margin: 0 auto;
        width: 100px;
        height: 100px;
        object-fit: contain;
        border-radius: 0.5rem;
        background-color: #f8f9fa;
    }



    .btn-submit-center {
        display: flex;
        justify-content: center;
    }

    .product-card.selected {
        background-color: #d1e7dd !important;
        border-left: 4px solid #198754 !important;
    }
</style>

<div class="container pt-3 card my-5">
    <h3 class="text-center mb-4 fw-bold text-primary">
        <i class="bi bi-box-arrow-in-down me-2"></i>Phiếu nhập hàng mới
    </h3>

    <!-- Nút quay lại -->
    <div class="back-btn">
        <a href="/receipt/index" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>

    <form action="/receipts/update" method="POST" class="mb-5">
        <!-- Nhà cung cấp -->
        <div class="form-floating mb-4">
            <select name="id_supplier" id="id_supplier" class="form-select" required>
                <option value="" disabled selected>-- Chọn nhà cung cấp --</option>
                <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?= htmlspecialchars($supplier->id_supplier) ?>">
                        <?= htmlspecialchars($supplier->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label for="id_supplier"><i class="bi bi-shop-window me-1"></i>Nhà cung cấp</label>
        </div>

        <!-- Danh sách sản phẩm -->
        <h5 class="fw-semibold text-secondary mb-3">
            <i class="bi bi-list-check me-1"></i>Danh sách sản phẩm cần nhập
        </h5>

        <?php if (!empty($products)): ?>
            <?php foreach ($products as $index => $product): ?>
                <div class="card mb-4 p-3 product-card">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-3">
                            <img src="<?= htmlspecialchars($product->images[0] ?? '/images/no-image.png') ?>"
                                alt="<?= htmlspecialchars($product->name) ?>" class="product-image w-100">
                        </div>
                        <div class="col-md-9">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="products[<?= $index ?>][selected]"
                                    id="product_<?= $index ?>" value="1">
                                <label class="form-check-label fw-bold card-title" for="product_<?= $index ?>">
                                    <?= htmlspecialchars($product->name) ?>
                                </label>
                            </div>

                            <input type="hidden" name="products[<?= $index ?>][id_product]"
                                value="<?= htmlspecialchars($product->id_product) ?>">

                            <div class="row g-3 mt-1">
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="number" min="20" max="200" class="form-control"
                                            name="products[<?= $index ?>][quantity]" placeholder="Số lượng">
                                        <label>Số lượng</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="number" min="10000" step="1000" class="form-control"
                                            name="products[<?= $index ?>][purchase_price]" placeholder="Giá nhập">
                                        <label>Giá nhập (VNĐ)</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="number" min="11000" step="1000" class="form-control"
                                            name="products[<?= $index ?>][selling_price]" placeholder="Giá bán">
                                        <label>Giá bán (VNĐ)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">Không có sản phẩm nào hết hàng!</p>
        <?php endif; ?>

        <!-- Nút lưu căn giữa -->
        <div class="btn-submit-center">
            <button type="submit" class="btn btn-primary btn-lg mt-3 shadow-sm rounded-pill px-5">
                <i class="bi bi-save2 me-1"></i> Lưu phiếu nhập
            </button>
        </div>
    </form>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const checkboxes = document.querySelectorAll(".form-check-input");

        checkboxes.forEach(checkbox => {
            const card = checkbox.closest(".product-card");

            function updateCardHighlight() {
                if (checkbox.checked) {
                    card.classList.add("selected");
                } else {
                    card.classList.remove("selected");
                }
            }


            updateCardHighlight();


            checkbox.addEventListener("change", updateCardHighlight);
        });
    });
</script>


<?php $this->stop() ?>