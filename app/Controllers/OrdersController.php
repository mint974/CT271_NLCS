<?php

namespace App\Controllers;

use App\Models\Contact;
use App\Models\DeliveryInformation;
use App\Models\Order;
use App\Models\OrderCancellation;
use App\Models\Product;
use App\Models\OrderDetail;

use App\Models\Payment;

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
        
        $orderIds = array_column($orders, 'id_order');
        $orderDetails = $orderModel->getOrderDetails($orderIds); // Lấy thông tin chi tiết từng đơn hàng

        $delivery_model = new DeliveryInformation(pdo());
        $paymentModel = new Payment(pdo());
        foreach ($orders as &$order) {
            $delivery = $delivery_model->where('id_delivery', $order['id_delivery']);
            $order['total_price'] = $orderDetailModel->getTotalPrice($order['id_order']) + $delivery->shipping_fee;
            $order['payment_status'] = $paymentModel->find($order['id_order'])->payment_status;
            $order['payment_method'] = $paymentModel->find($order['id_order'])->payment_method;
        }

        
        $this->sendPage('orders/index', [
            'orders' => $orders,
            'order_details' => $orderDetails,
            'errors' => $error,
            'success' => $Success
        ]);
    }

    public function indexadmin(string $error = null, string $Success = null)
    {
        
        $orderModel = new Order(PDO());
        $orders = $orderModel->getAll(); 

        $this->sendPage('orders/indexadmin', [
            'orders' => $orders         
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

            //update price
           $order_detail_list = $orderdetail->updatePrice($products, $orders_detail);
           

            $this->sendPage('orders/shopping_cart', [
                'orders_detail' =>  $order_detail_list,
                'products' => $products,
                'errors' => $errors,
                'success' => $success
            ]);
        }


    }

    public function store()
    {
        // dd($_POST);

        $error = [];
        //kiểm tra lỗi 
        if (!$this->checkCsrf()) {
            $error['Crsf'] = 'Lỗi CSRF, hãy kiểm tra và thử lại!';
        }

        //lấy danh sách id_product
        $data = explode(",", $_POST['product_ids']);

        $modelproduct = new Product(PDO());
        $product_list = $modelproduct->getProductsByIds($data);

        //kiểm tra số lượng sản phẩm trong kho
        $id_order = sprintf("REORD%d", AUTHGUARD()->user()->id_account);
        $modelorderdetail = new OrderDetail(PDO());
        
        foreach ($product_list as $product) {
            $orderedQuantity = $modelorderdetail->getTotalQuantity($id_order, $product['id_product']); // Chỉ truyền ID sản phẩm

            if ($product['quantity'] <= $orderedQuantity) {
                $error['product'] = "Số lượng sản phẩm " . htmlspecialchars($product['name']) . " không đủ để bán. vui lòng chọn lại!";
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
            $delivery = (new DeliveryInformation(pdo()))->where('id_delivery', $newOrder->id_delivery);

            if ($modelorderdetail->Orders($newOrder->id_order, $data)) {
                $orders = $this->getUserOrdersWithTotal(AUTHGUARD()->user()->id_account);
                
                // dd($orders);

                foreach($orders as &$order){
                    $order['total_price'] += $delivery->shipping_fee;
                }

                // dd($newOrder);
                $payment_method = $_POST['payment_method'];
                //thanh toán
                if($payment_method === 'Online'){
                    $this->Payments($newOrder);
                }


                $success = 'Đặt hàng thành công!';

                redirect('/orders/index', [
                    'orders' => $orders,
                    'success' => $success
                ]);

            } else {
                $this->shoppingcart('Lỗi đặt hàng!', null);
            }

        } else {
            $this->shoppingcart('Lỗi tạo đơn đặt hàng!', null);

        }
    }


    //hàm chuẩn bị thanh toán
    public function Payments($data){
        $shipping_cost = (new DeliveryInformation(pdo()))->where('id_delivery', $data->id_delivery)->shipping_fee;

        $total_price_order = (new OrderDetail(pdo()))->getTotalPrice($data->id_order);
        $vnp_Amount = $total_price_order + $shipping_cost;

        $vnp_Returnurl = 'http://ct271-mintfreshfruit.localhost/payments/store';

        $vnp_TxnRef = $data->id_order;
        $vnp_OrderInfo = 'Thanh toán tiền trái cây';

        $vnp_OrderType = 'Đơn hàng trực tuyến';

        // dd("d");
        $payment = (new Payment(pdo()))->pay($vnp_Returnurl, $vnp_TxnRef, $vnp_OrderInfo, $vnp_OrderType, $vnp_Amount);
        dd($payment);

    }


    public function updateprod($id_product)
    {
        $quantity = $_POST['quantity'] ?? 1;
        $order = new OrderDetail(PDO());
        $product = (new Product(pdo()))->where('id_product', $id_product);
        $price = $product->price;
        $discountRate = $product->promotion['discount_rate'] ? $product->promotion['discount_rate'] : 0;

        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '/products';
        if ($order->updateProduct($id_product, $quantity, $price, $discountRate)) {
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
        $productmodel = new Product(pdo());
        $product = $productmodel->where('id_product', $id_product);
        $price = $product->price;
        // dd($product);
        // exit();
        $discount_rate = isset($product->promotion['discount_rate']) ? (float) $product->promotion['discount_rate'] : 0;

       
        
        if ($order->addproduct($id_product, $quantity, $price, $discount_rate)) {
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

        $orders = $orderModel->getUserOrders($id_account); // Lấy danh sách đơn hàng của user

        $order_detais_model = new OrderDetail(pdo());
        foreach ($orders as &$order) {

            $order['total_price'] = $order_detais_model->getTotalPrice($order['id_order']) ;
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

        if ($order->status === "Đã gửi đơn đặt hàng") {
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

    protected function fillformsearch(array $data) 
    {
        return [
            'id_order' => isset($data['id_order']) ? htmlspecialchars($data['id_order']) : '',
            'id_account' => isset($data['id_account']) ? htmlspecialchars($data['id_account']) : '',
            'subject' => isset($data['subject']) ? htmlspecialchars($data['subject']) : ''
        ];
    }
  
  
    public function searchadmin(){
    //   dd($_POST);
  
      if (!$this->checkCsrf()) {
          $error = 'Lỗi CSRF, hãy kiểm tra và thử lại!';
          $this->index($error);
          exit();
      }
  
      $searchData = $this->fillformsearch($_POST);
      $orderModel = new Order(pdo());
  
      if (!empty($searchData['id_order'])) {
          $order = $orderModel->where('id_order',$searchData['id_order']);
          $orders = $order ? [$order] : [];
      } elseif (!empty($searchData['id_account'])) {
          $orders = $orderModel->searchadmin('id_account', $searchData['id_account']);
      } elseif (!empty($searchData['subject'])) {
          $orders = $orderModel->searchadmin('subject', $searchData['subject']);
      } else {
          $error = "Bạn chưa nhập thông tin tìm kiếm.";
      }
  
      dd($orders);
      $this->sendPage('suppliers/index', [
          'orders' => $orders ?? [],
          'errors' => $error ?? null
      ]);
  }
}
