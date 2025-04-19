<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>

<?php $this->start("page_specific_css") ?>
<link href="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.0.8/r-3.0.2/sp-2.3.1/datatables.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
<?php $this->stop() ?>

<?php $this->start("page") ?>

<?php 
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<style>
    .section-title {
        font-weight: bold;
        font-size: 20px;
        border-bottom: 3px solid #ff6f61;
        display: inline-block;
        padding-bottom: 5px;
        margin-bottom: 20px;
    }

    .order-summary,
    .delivery {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    .btn-primary,
    .btn-warning {
        font-weight: bold;
    }
</style>

<div class="container mt-2">
    <div class="row">
        <div class="col-md-8 delivery mb-3">
            <h3 class="section-title"><i class="fas fa-map-marker-alt"></i> Địa chỉ giao hàng</h3>
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


            <form action="/delivery/update" method="post">

                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                <div class="row">
                    <div class="mb-3 col-12">
                        <label class="mb-2 ">Chọn địa chỉ giao hàng</label>
                        <select id="deliverySelect" class="form-control">
                            <?php foreach ($delivery_list as $index => $delivery): ?>
                                <option value="<?= $index ?>">
                                    <?= $delivery->receiver_name ?> - <?= $delivery->receiver_phone ?> -
                                    <?= $delivery->city ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3 col-6 hidden">
                        <label>id_list</label>
                        <input type="text" name="product_ids" id="product_ids" value="<?= $product_ids; ?>"
                            class="form-control">
                    </div>
                    <div class="mb-3 col-6 hidden">
                        <label>total_price</label>
                        <input type="text" name="total_price" id="total_price" value="<?= $total_price; ?>"
                            class="form-control">
                    </div>
                    <div class="mb-3 col-6 hidden">
                        <label>id</label>
                        <input type="text" name="id_delivery" id="id_delivery" class="form-control">
                    </div>
                    <div class="mb-3 col-6">
                        <label>Tên người nhận</label>
                        <input type="text" name="receiverName" id="receiverName" class="form-control">
                    </div>
                    <div class="mb-3 col-6">
                        <label>Số điện thoại</label>
                        <input type="text" name="receiverPhone" id="receiverPhone" class="form-control">
                    </div>
                    <div class="mb-3 col-6">
                        <label>Địa chỉ nhà</label>
                        <input type="text" name="houseNumber" id="houseNumber" class="form-control">
                    </div>
                    <div class="mb-3 col-6">
                        <label>Xã/Phường</label>
                        <input type="text" name="ward" id="ward" class="form-control">
                    </div>
                    <div class="mb-3 col-6">
                        <label>Quận/Huyện</label>
                        <input type="text" name="district" id="district" class="form-control">
                    </div>
                    <div class="mb-3 col-6">
                        <label>Tỉnh/Thành phố</label>
                        <input type="text" name="city" id="city" class="form-control">
                    </div>
                </div>
                <div class="d-flex justify-content-center gap-3">
                    <button type="submit" class="btn btn-outline-primary ">Lưu chỉnh sửa địa chỉ</button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#deliveryModal">
                        Thêm địa chỉ mới
                    </button>
                </div>
            </form>
        </div>
        <div class="col-md-4">
            <div class="order-summary">
                <form action="/orders/save" method="post">
                    
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                    <!-- danh sách id sản phẩm sẽ mua -->
                    <div class="mb-3 col-6 hidden">
                        <label>id_list</label>
                        <input type="text" name="product_ids" value="<?= $product_ids; ?>" class="form-control">
                    </div>
                    <!-- tổng tiền -->
                    <div class="mb-3 col-6 hidden">
                        <label>total_price</label>
                        <input type="text" name="total_price" value="<?= $total_price; ?>" class="form-control">
                    </div>
                    <!-- id giao hàng -->
                    <div class="mb-3 col-6 hidden">
                        <label>id</label>
                        <input type="text" name="id_delivery" id="id_delivery_order" class="form-control">
                    </div>
                    <h4 class="text-success text-center ">Tóm tắt đơn hàng</h4>
                    <p>Tổng tiền hàng: <strong><?= number_format($total_price, 0, ',', '.') ?> VND</strong></p>
                    <p>Phí vận chuyển: <strong id="shippingFee">0 VND</strong></p>
                    <h5>Tổng cộng: <span class="text-danger" id="totalAmount">0 VND</span></h5>
                    <hr>
                    <div class="mb-3 mt-3">
                        <label for="payment_method" class="form-label"><strong>Chọn phương thức thanh
                                toán</strong></label>
                        <select name="payment_method" id="payment_method" class="form-control">
                            <option value="COD">Thanh toán khi nhận hàng (COD)</option>
                            <option value="Online">Thanh toán trực tuyến (VNPay)</option>
                        </select>
                    </div>
                    <button type="submit" name="redirect" class="btn btn-primary w-100 mt-3">Đặt hàng</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm/Chỉnh sửa Địa chỉ -->
<div class="modal fade" id="deliveryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm địa chỉ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="/delivery/add" method="post">
                    
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                    <div class="mb-3 col-6 hidden">
                        <label>id_list</label>
                        <input type="text" name="product_ids" id="product_ids" value="<?= $product_ids; ?>"
                            class="form-control">
                    </div>
                    <div class="mb-3 col-6 hidden">
                        <label>total_price</label>
                        <input type="text" name="total_price" id="total_price" value="<?= $total_price; ?>"
                            class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Tên người nhận</label>
                        <input type="text" class="form-control" name="receiver_name" id="modalReceiverName" required>
                    </div>
                    <div class="mb-3">
                        <label>Số điện thoại</label>
                        <input type="text" class="form-control" name="receiver_phone" id="modalReceiverPhone" required>
                    </div>
                    <div class="mb-3">
                        <label>Địa chỉ nhà</label>
                        <input type="text" class="form-control" name="house_number" id="modalHouseNumber" required>
                    </div>
                    <div class="mb-3 ">
                        <label>Xã/Phường</label>
                        <input type="text" name="ward" id="modalward" class="form-control" required>
                    </div>
                    <div class="mb-3 ">
                        <label>Quận/Huyện</label>
                        <input type="text" name="district" id="modaldistrict" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Tỉnh/Thành phố</label>
                        <input type="text" class="form-control" name="city" id="modalCity" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-success">Lưu địa chỉ</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
    const deliveryList = <?= json_encode($delivery_list) ?>;
    const deliverySelect = document.getElementById("deliverySelect");
    const shippingFee = document.getElementById("shippingFee");
    const totalAmount = document.getElementById("totalAmount");

    function updateDeliveryInfo(index) {
        const delivery = deliveryList[index];
        document.getElementById("id_delivery_order").value = delivery.id_delivery;
        document.getElementById("id_delivery").value = delivery.id_delivery;
        document.getElementById("receiverName").value = delivery.receiver_name;
        document.getElementById("receiverPhone").value = delivery.receiver_phone;
        document.getElementById("houseNumber").value = delivery.house_number;
        document.getElementById("ward").value = delivery.ward;
        document.getElementById("district").value = delivery.district;
        document.getElementById("city").value = delivery.city;
        shippingFee.innerText = new Intl.NumberFormat('vi-VN').format(delivery.shipping_fee) + " VND";
        totalAmount.innerText = new Intl.NumberFormat('vi-VN').format(<?= $total_price ?> + delivery.shipping_fee) + " VND";
    }

    deliverySelect.addEventListener("change", function () {
        updateDeliveryInfo(this.value);
    });

    window.onload = function () {
        updateDeliveryInfo(0);
    };

    //kiểm tra giao hàng khác tỉnh
    document.addEventListener("DOMContentLoaded", function () {
        const orderButton = document.querySelector(".order-summary button[type='submit']");
        const deliverySelect = document.getElementById("deliverySelect");
        const totalPriceInput = document.querySelector("input[name='total_price']");

        orderButton.addEventListener("click", function (event) {
            const selectedDeliveryIndex = deliverySelect.value;
            const selectedCity = deliveryList[selectedDeliveryIndex].city.toLowerCase().trim();
            const totalPrice = parseFloat(totalPriceInput.value);

            if (selectedCity !== "cần thơ" && totalPrice < 800000) {
                event.preventDefault();
                alert("Chỉ giao hàng khác tỉnh khi đơn hàng từ 800.000 VND. Vui lòng mua thêm hoặc đổi địa chỉ giao hàng.");
            }
        });
    });

</script>
<?php $this->stop() ?>