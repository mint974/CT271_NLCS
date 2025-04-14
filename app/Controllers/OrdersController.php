<?php

namespace App\Controllers;

use App\Models\DeliveryInformation;
use App\Models\Order;
use App\Models\OrderCancellation;
use App\Models\Product;
use App\Models\OrderDetail;
use GrahamCampbell\ResultType\Success;

class OrdersController extends Controller
{
    public function __construct()
    {
        if (!AUTHGUARD()->isUserLoggedIn()) {
            redirect('/login');
        }

        parent::__construct();
    }

    public function index(string $error = null, string $Success = null)
    {
        $id_account = AUTHGUARD()->user()->id_account;
        $orderModel = new Order(PDO());
        $orderDetailModel = new OrderDetail(PDO());

        $orders = $orderModel->getUserOrders($id_account); // Lấy danh sách đơn hàng của user
        foreach ($orders as &$order) {
            $order['total_price'] = $orderDetailModel->getTotalPrice($order['id_order']); // Tính tổng tiền từng đơn hàng
        }

        $orderIds = array_column($orders, 'id_order');
        $orderDetails = $orderModel->getOrderDetails($orderIds); // Lấy thông tin chi tiết từng đơn hàng

        $this->sendPage('orders/index', [
            'orders' => $orders,
            'order_details' => $orderDetails,
            'errors' => $error,
            'success' => $Success
        ]);
    }

    public function shoppingcart($errors = null, $success = null)
    {
        $ordermodel = new Order(PDO());
        $reorder = $ordermodel->where('id_order', sprintf("REORD%d", AUTHGUARD()->user()->id_account));

        $orderdetail = new OrderDetail(PDO());
        $orders_detail = $orderdetail->getAllOrderDetails($reorder->id_order);

        // Lấy danh sách id_product từ orders_detail
        $productIds = array_map(fn($order) => $order->id_product, $orders_detail);

        if (empty($productIds)) {
            $null = 'Giỏ hàng của bạn đang trống.';
            $this->sendPage('orders/shopping_cart', [
                'null' => $null
            ]);
        } else {
            // Truy vấn danh sách sản phẩm theo id_product
            $productModel = new Product(PDO());
            $products = $productModel->getProductsByIds($productIds);

            $this->sendPage('orders/shopping_cart', [
                'orders_detail' => $orders_detail,
                'products' => $products,
                'errors' => $errors,
                'success' => $success
            ]);
        }


    }

    public function store()
    {
        $data = explode(",", $_POST['product_ids']);

        $modelproduct = new Product(PDO());
        $product_list = $modelproduct->getProductsByIds($data);

        //kiểm tra số lượng sản phẩm trong kho
        $id_order = sprintf("REORD%d", AUTHGUARD()->user()->id_account);
        $modelorderdetail = new OrderDetail(PDO());
        $error = [];
        foreach ($product_list as $product) {
            $orderedQuantity = $modelorderdetail->getTotalQuantity($id_order, $product['id_product']); // Chỉ truyền ID sản phẩm

            if ($product['quantity'] <= $orderedQuantity) {
                $error[] = "Số lượng sản phẩm " . htmlspecialchars($product['name']) . " không đủ để bán. vui lòng chọn lại!";
            }
        }
        if (!empty($error)) {
            $this->shoppingcart($error);
        }

        $newOrder = new Order(PDO());
        $newOrder->id_account = AUTHGUARD()->user()->id_account;
        $newOrder->id_delivery = $_POST['id_delivery'];

        //tạo đơn hàng
        if ($newOrder->save()) {

            if ($modelorderdetail->Orders($newOrder->id_order, $data)) {
                $orders = $this->getUserOrdersWithTotal(AUTHGUARD()->user()->id_account);
                redirect('/orders/index', ['orders' => $orders]);

            } else {
                $this->shoppingcart('Lỗi đặt hàng!', null);
            }

        } else {
            $this->shoppingcart('Lỗi tạo đơn đặt hàng!', null);

        }
    }

    public function edit($orderId)
    {
        $order = AUTHGUARD()->user()->findOrder($orderId);
        if (!$order) {
            $this->sendNotFound();
        }

        $this->sendPage('orders/edit', [
            'errors' => session_get_once('errors'),
            'order' => (array) $order
        ]);
    }

    public function update($orderId)
    {
        $order = AUTHGUARD()->user()->findOrder($orderId);
        if (!$order) {
            $this->sendNotFound();
        }

        $order->created_at = date('Y-m-d H:i:s'); // Cập nhật thời gian khi sửa đơn hàng
        if ($order->save()) {
            redirect('/', ['success' => 'Order has been updated successfully.']);
        } else {
            redirect('/orders/edit/' . $orderId, ['errors' => ['Failed to update order.']]);
        }
    }

    public function updateprod($id_product)
    {
        $quantity = $_POST['quantity'] ?? 1;
        $order = new OrderDetail(PDO());

        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '/products';
        if ($order->updateProduct($id_product, $quantity)) {
            redirect($redirectUrl, ['success' => 'Đã sửa số lượng sản phẩm, kiểm tra ngay!']);
        } else {
            redirect($redirectUrl, ['error' => 'Lỗi sửa số lượng sản phẩm, Thử lại sao!']);
        }


    }


    public function addprod($id_product)
    {
        $quantity = $_POST['quantity'] ?? 1;
        $order = new OrderDetail(PDO());
        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '/products';
        if ($redirectUrl == 'http://ct271-mintfreshfruit.localhost/products/load_prod_cata') {
            $redirectUrl = 'http://ct271-mintfreshfruit.localhost/products';
        }
        if ($order->addproduct($id_product, $quantity)) {
            redirect($redirectUrl, ['success' => 'Đã thêm sản phẩm vào giỏ hàng, kiểm tra ngay!']);
        } else {
            redirect($redirectUrl, ['error' => 'Lỗi thêm sản phẩm vào giỏ hàng, Thử lại sao!']);
        }

    }

    public function deletebyIDProd(string $id_Product)
    {

        $order = new OrderDetail(PDO());

        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '/products';
        if ($order->delete($id_Product)) {
            redirect($redirectUrl, ['success' => 'Đã xóa sản phẩm!']);
        } else {
            redirect($redirectUrl, ['error' => 'Lỗi xóa sản phẩm, Thử lại sao!']);
        }
    }

    public function getUserOrdersWithTotal($id_account)
    {
        $orderModel = new Order(PDO());
        $orderDetailModel = new OrderDetail(PDO());

        $orders = $orderModel->getUserOrders($id_account); // Lấy danh sách đơn hàng của user

        foreach ($orders as &$order) {
            $order['total_price'] = $orderDetailModel->getTotalPrice($order['id_order']); // Tính tổng tiền từng đơn hàng
        }

        return $orders;
    }

    public function search()
    {
        $date = $_POST['search_date'] ?? null;
        $totalRange = $_POST['search_total'] ?? null;
        $status = $_POST['search_status'] ?? null;

        // Lấy danh sách đơn hàng dựa trên bộ lọc
        $modelorder = new Order(PDO());

        $orders = $modelorder->searchOrders(AUTHGUARD()->user()->id_account, $date, $totalRange, $status);

        // Hiển thị danh sách đơn hàng
        $this->sendPage('/orders/index', [
            'orders' => $orders
        ]);

    }

    public function orderdetail($id_order)
    {
        // Khởi tạo model
        $orderModel = new Order(pdo());
        $orderDetailModel = new OrderDetail(pdo());
        $deliveryModel = new DeliveryInformation(PDO());

        // Lấy thông tin đơn hàng
        $order = $orderModel->where('id_order', $id_order);
        if (!$order) {
            return $this->sendPage('/orders/error', ['message' => 'Không tìm thấy đơn hàng!']);
        }

        $delivery = $deliveryModel->where('id_delivery', $order->id_delivery);
        // Lấy danh sách sản phẩm trong đơn hàng

        $orderProducts = $orderDetailModel->getAllProductsOrderDetails($id_order);
        // dd($orderProducts);
        // exit();

        // Tính tổng tiền đơn hàng
        $totalPrice = array_sum(array_column($orderProducts, 'total_price'));

        // Gửi dữ liệu đến view
        return $this->sendPage('/orders/order_details', [
            'order' => $order,
            'orderInfo' => $delivery,
            'orderProducts' => $orderProducts,
            'totalPrice' => $totalPrice + $delivery->shipping_fee
        ]);
    }

    public function cancel()
    {

        $id_order = $_POST['id_order'];
        $reason = $_POST['reason'];

        $order = (new Order(PDO()))->where('id_order', $id_order);

        if ($order->status == "Đã gửi đơn đặt hàng") {
            $modelcancel = new OrderCancellation(PDO());
            $modelcancel->id_order = $id_order;
            $modelcancel->reason = $reason;

            if ($modelcancel->save()) {
                //cập nhật trạng thái đơn hàng
                $order->status = "Đơn hàng đã bị hủy";
                $order->save();

                $order_detail = new OrderDetail(PDO());
                if ($order_detail->cancelorder($order->id_order)) {
                    $success = "Hủy đơn hàng thành công!";
                    $this->index(null, $success);
                    return;
                } else {
                    $error = "Lỗi hủy đơn hàng thử lại sao!";
                    $this->index($error);
                    return;
                }
            } else {
                $error = "Lỗi hủy đơn hàng thử lại sao!";
                $this->index($error);
                return;
            }


        } else {
            $error = "Lỗi hủy đơn hàng, bạn chỉ có thể hủy đơn trước khi shop xác nhận đơn hàng!";
            $this->index($error);
            return;
        }

    }

}
