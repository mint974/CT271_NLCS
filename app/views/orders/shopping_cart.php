<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>

<?php $this->start("page_specific_css") ?>
<link href="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.0.8/r-3.0.2/sp-2.3.1/datatables.min.css" rel="stylesheet">
<?php $this->stop() ?>

<?php $this->start("page") ?>

<style>
    .cart-summary {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    .action-btns .btn {
        margin: 2px;
    }

    .delivery-policy {
        background: #f8f9fa;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .delivery-policy h3 {
        color: #08a045;
    }

    .delivery-policy-list li {
        font-size: 18px;
        margin-bottom: 10px;
    }

    .delivery-policy .icon {
        font-size: 20px;
        margin-right: 8px;
        color: #08a045;
    }

    .delivery-policy p.text-danger {
        font-size: 16px;
        font-weight: bold;
    }

    .modal-lg {
        max-width: 700px;
    }

    .delivery-info {
        font-size: 16px;
    }

    .delivery-info p {
        margin-bottom: 5px;
    }
</style>

<div class="container shopping-card cart-summary">
    <div class="card p-2">
        <h2 class="text-center mb-4 mt-2 text-success"><strong>GIỎ HÀNG CỦA BẠN</strong></h2>
        <div class="table-responsive">
            <!-- thông báo thành công -->
            <?php if (isset($success) && !empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <!-- thông báo lỗi -->
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
            <?php if (isset($null) && !empty($null)): ?>

                <div class="cart-empty text-center p-3 pb-5">
                    <h2 class="text-danger"><?= htmlspecialchars($null); ?></h2>
                    <p class="text-muted">Hãy tiếp tục mua sắm để tìm thấy những sản phẩm yêu thích!</p>
                    <a href="/products" class="btn btn-success mt-3">Mua hàng ngay</a>
                </div>
            <?php else: ?>

                <table class="table table-bordered text-center">
                    <thead class="table-success">
                        <tr>
                            <th><input type="checkbox" id="checkbox_header"></th>
                            <th>Hình ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Đơn giá</th>
                            <th>Khuyến mãi</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sumPrice = 0; ?>
                        <?php foreach ($orders_detail as $order): ?>
                            <?php
                            // Tìm sản phẩm tương ứng
                            $product = array_filter($products, fn($p) => $p['id_product'] === $order->id_product);
                            $product = reset($product);

                            if (!$product)
                                continue;

                            // Xử lý hình ảnh sản phẩm
                            $images = explode(',', $product['images']);
                            $imageSrc = !empty($images[0]) ? $images[0] : 'default.jpg';

                            // Tính giá sau khuyến mãi
                            $discount = $product['discount_rate'] !== NULL ? ($product['price'] * $product['discount_rate'] / 100) : 0;
                            $finalPrice = $product['price'] - $discount;
                            $totalPrice = $finalPrice * $order->quantity;
                            $sumPrice += $totalPrice;
                            ?>
                            <tr>
                                <td><input type="checkbox" class="checkbox_item"
                                        data-product-id="<?= $product['id_product'] ?>"></td>
                                <td>
                                    <img src="<?= htmlspecialchars($imageSrc) ?>"
                                        alt="<?= htmlspecialchars($product['name']) ?>" width="70">
                                </td>
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td><?= number_format($product['price'], 0, ',', '.') ?> VND</td>
                                <td><?= $product['discount_rate'] !== NULL ? $product['discount_rate'] . '%' : 'Không có' ?>
                                </td>
                                <td><?= htmlspecialchars($order->quantity) ?>         <?= htmlspecialchars($product['unit']) ?></td>
                                <td><?= number_format($totalPrice, 0, ',', '.') ?> VND</td>
                                <td class="action-btns">
                                    <!-- Nút Sửa -->
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editModal_<?= $product['id_product'] ?>" title="Chỉnh sửa">
                                        <i class="fa-solid fa-pen-to-square"></i> Sửa
                                    </button>

                                    <!-- Nút Xóa -->
                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#deleteConfirmModal_<?= $product['id_product'] ?>">
                                        <i class="fa-solid fa-trash"></i> Xóa
                                    </button>




                                </td>
                            </tr>
                            <!-- Modal xác nhận xóa (mỗi sản phẩm có 1 modal riêng) -->
                            <div class="modal fade" id="deleteConfirmModal_<?= $product['id_product'] ?>" tabindex="-1"
                                aria-labelledby="deleteConfirmLabel_<?= $product['id_product'] ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteConfirmLabel_<?= $product['id_product'] ?>">Xác
                                                nhận xóa</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Bạn có chắc chắn muốn xóa sản phẩm
                                                <strong><?= htmlspecialchars($product['name']) ?></strong> này không?
                                            </p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                            <a href="/orders/delete/<?= $product['id_product'] ?>"
                                                class="btn btn-danger">Xóa</a>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- Modal sửa số lượng (Modal riêng cho từng sản phẩm) -->
                            <div class="modal fade" id="editModal_<?= $product['id_product'] ?>" tabindex="-1"
                                aria-labelledby="editModalLabel_<?= $product['id_product'] ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="editModalLabel_<?= $product['id_product'] ?>">Chỉnh sửa
                                                số lượng</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="<?= '/orders/update/' . $this->e($product['id_product']) ?>"
                                                method="POST">

                                                <!-- Tên sản phẩm -->
                                                <div class="mb-3">
                                                    <label for="name_product">Tên sản phẩm:</label>
                                                    <input type="text" id="name_product_<?= $product['id_product'] ?>"
                                                        name="name_product" class="form-control" value="<?= $product['name'] ?>"
                                                        disabled>
                                                </div>

                                                <!-- Số lượng -->
                                                <div class="mb-3">
                                                    <label for="quantity">Số lượng:</label>
                                                    <input type="number" id="quantity_<?= $product['id_product'] ?>"
                                                        name="quantity" value="<?= htmlspecialchars($order->quantity) ?>"
                                                        class="form-control" min="1">
                                                </div>



                                                <div class="mb-3 d-flex justify-content-center">
                                                    <button type="submit" class="btn btn-success mx-2">
                                                        <i class="fa-solid fa-check"></i> Lưu
                                                    </button>
                                                    <button type="button" class="btn btn-secondary mx-2"
                                                        data-bs-dismiss="modal">
                                                        <i class="fa-solid fa-xmark"></i> Hủy
                                                    </button>
                                                </div>

                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                    </tbody>

                </table>
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <?php if (isset($null) && !empty('null')): ?>
            <div class="cart-summary col col-md-6 text-center mt-4">
                <h4>Tổng tiền tạm tính: 0 VND</span> </h4>
                <button class="btn btn-primary mt-3" disabled>Tiến hành đặt hàng</button>
            </div>

        <?php else: ?>
            <div class="cart-summary col col-md-6 text-center mt-4">
                <h4>Tổng tiền tạm tính: <span class="text-danger"><?= number_format($sumPrice, 0, ',', '.') ?> VND</span>
                </h4>
                <form id="checkoutForm" action="/orders/start_order" method="POST">
                    <input type="hidden" name="product_list" id="productListInput">
                    <input type="hidden" name="sumPrice" value="<?= $sumPrice; ?>">
                    <button type="submit" class="btn btn-primary mt-3" id="checkoutButton" disabled>
                        Tiến hành đặt hàng
                    </button>
                </form>

            </div>
            
        <?php endif; ?>

        <div class="col col-md-6 mt-4">
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
    </div>

</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const headerCheckbox = document.getElementById("checkbox_header");
        const itemCheckboxes = document.querySelectorAll(".checkbox_item");
        const checkoutButton = document.getElementById("checkoutButton");
        const productListInput = document.getElementById("productListInput");
        const sumPriceInput = document.querySelector('input[name="sumPrice"]'); // Lấy input tổng tiền
        const totalPriceElements = document.querySelectorAll("td:nth-child(7)"); // Cột tổng giá mỗi sản phẩm

        function updateCheckoutInfo() {
            let selectedProducts = [];
            let totalSum = 0;

            itemCheckboxes.forEach((checkbox, index) => {
                if (checkbox.checked) {
                    selectedProducts.push(checkbox.getAttribute("data-product-id"));

                    // Lấy giá trị từ cột "Thành tiền" (cột thứ 7)
                    let priceText = totalPriceElements[index].textContent.trim().replace(/\D/g, ""); // Lọc bỏ ký tự không phải số
                    totalSum += parseInt(priceText, 10) || 0;
                }
            });

            productListInput.value = selectedProducts.join(",");
            sumPriceInput.value = totalSum;

            // Cập nhật tổng tiền trên giao diện
            document.querySelector(".cart-summary h4 span.text-danger").textContent = totalSum.toLocaleString("vi-VN") + " VND";

            // Bật/tắt nút "Tiến hành đặt hàng"
            checkoutButton.disabled = selectedProducts.length === 0;
        }

        // Khi click vào checkbox header, chọn tất cả
        headerCheckbox.addEventListener("change", function () {
            itemCheckboxes.forEach(checkbox => checkbox.checked = headerCheckbox.checked);
            updateCheckoutInfo();
        });

        // Khi chọn hoặc bỏ chọn từng sản phẩm
        itemCheckboxes.forEach(checkbox => {
            checkbox.addEventListener("change", function () {
                headerCheckbox.checked = [...itemCheckboxes].every(cb => cb.checked);
                updateCheckoutInfo();
            });
        });

        // Khi nhấn "Tiến hành đặt hàng", kiểm tra lần cuối
        document.getElementById("checkoutForm").addEventListener("submit", function (e) {
            if (productListInput.value === "" || sumPriceInput.value === "0") {
                e.preventDefault();
                alert("Vui lòng chọn ít nhất một sản phẩm để đặt hàng!");
            }
        });

        window.onload = function () {
            updateCheckoutInfo();
        };
        // Cập nhật thông tin khi tải trang

    });

</script>


<?php $this->stop() ?>