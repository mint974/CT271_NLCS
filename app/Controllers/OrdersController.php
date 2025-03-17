<?php

namespace App\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetail;

class OrdersController extends Controller
{
    public function __construct()
    {
        if (!AUTHGUARD()->isUserLoggedIn()) {
            redirect('/login');
        }

        parent::__construct();
    }

    public function index()
    {
        $success = session_get_once('success');
        $ordermodel = new Order(PDO());
        $order = $ordermodel->where('id_order', sprintf("REORD%05d",AUTHGUARD()->user()->id_account));
       
        $orderdetail = new orderdetail(PDO());

        $orders = $orderdetail->getAllOrderDetails($order->id_order);

        $this->sendPage('orders/index', [
            'orders' => $order
        ]);
    }

    public function shoppingcart(){
    $ordermodel = new Order(PDO());
    $reorder = $ordermodel->where('id_order', sprintf("REORD%05d", AUTHGUARD()->user()->id_account));

    if (!$reorder) {
        redirect('/orders', ['error' => 'Không tìm thấy đơn hàng.']);
    }

    $orderdetail = new OrderDetail(PDO());
    $orders_detail = $orderdetail->getAllOrderDetails($reorder->id_order);

    // Lấy danh sách id_product từ orders_detail
    $productIds = array_map(fn($order) => $order->id_product, $orders_detail);

    if (empty($productIds)) {
        redirect('/orders', ['error' => 'Giỏ hàng trống.']);
    }

    // Truy vấn danh sách sản phẩm theo id_product
    $productModel = new Product(PDO());
    $products = $productModel->getProductsByIds($productIds);

    // dd($products);
    // exit();
    $this->sendPage('orders/shopping_cart', [
        'orders_detail' => $orders_detail,
        'products' => $products
    ]);
}



    public function create()
    {
        $this->sendPage('orders/create', [
            'errors' => session_get_once('errors'),
            'old' => $this->getSavedFormValues()
        ]);
    }

    public function store()
    {
        $newOrder = new Order(PDO());
        $newOrder->id_order = uniqid('ORD_'); // Tạo ID đơn hàng tự động
        $newOrder->created_at = date('Y-m-d H:i:s'); // Thời gian tạo đơn hàng

        if ($newOrder->save()) {
            redirect('/', ['success' => 'Order has been created successfully.']);
        } else {
            redirect('/orders/create', ['errors' => ['Failed to create order.']]);
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

    public function destroy($orderId)
    {
        $order = AUTHGUARD()->user()->findOrder($orderId);
        if (!$order) {
            $this->sendNotFound();
        }

        if ($order->delete()) {
            redirect('/', ['success' => 'Order has been deleted successfully.']);
        } else {
            redirect('/', ['errors' => ['Failed to delete order.']]);
        }
    }


    public function updateprod($id_product){
        $quantity = $_POST['quantity'] ?? 1;
        $order = new OrderDetail(PDO());

        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '/products';
        if($order->updateProduct($id_product, $quantity)){
            redirect($redirectUrl, ['success' => 'Đã sửa số lượng sản phẩm, kiểm tra ngay!']);
        } else {
            redirect($redirectUrl, ['error' => 'Lỗi sửa số lượng sản phẩm, Thử lại sao!']);
        }
        

    }


    public function addprod($id_product){
        $quantity = $_POST['quantity'] ?? 1;
        $order = new OrderDetail(PDO());
        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '/products';
        if($redirectUrl == 'http://ct271-mintfreshfruit.localhost/products/load_prod_cata'){
            $redirectUrl = 'http://ct271-mintfreshfruit.localhost/products';
        }
        if($order->addproduct($id_product, $quantity)){
            redirect($redirectUrl, ['success' => 'Đã thêm sản phẩm vào giỏ hàng, kiểm tra ngay!']);
        } else {
            redirect($redirectUrl, ['error' => 'Lỗi thêm sản phẩm vào giỏ hàng, Thử lại sao!']);
        }

    }

    public function deletebyIDProd(String $id_Product){
       
        $order = new OrderDetail(PDO());

        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '/products';
        if($order->delete($id_Product)){
            redirect($redirectUrl, ['success' => 'Đã xóa sản phẩm!']);
        } else {
            redirect($redirectUrl, ['error' => 'Lỗi xóa sản phẩm, Thử lại sao!']);
        }
    }
}
