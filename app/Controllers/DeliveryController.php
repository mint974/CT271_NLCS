<?php

namespace App\Controllers;

use App\Models\DeliveryInformation;
use App\Models\ActivityHistory;

class DeliveryController extends Controller
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
        //$product_ids = explode(',', $_POST['product_list']); 
        $total_price = $_POST['sumPrice'];
        $modelDelivery = new DeliveryInformation(PDO());
        $Deliveries = $modelDelivery->getAllDeliveryInfo(AUTHGUARD()->user()->id_account);
        $this->sendPage('orders/start_order', [
            'product_ids' => $_POST['product_list'],
            'total_price' => $total_price,
            'delivery_list' => $Deliveries
        ]);
    }

    public function store()
    {
        $total_price = $_POST['total_price'];
        $modelDelivery = new DeliveryInformation(PDO());
        $Deliveries = $modelDelivery->getAllDeliveryInfo(AUTHGUARD()->user()->id_account);
        $product_ids = $_POST['product_ids'];

        $data = $this->filterDeliveryData($_POST);

        $newDelivery = new DeliveryInformation(PDO());
        $model_errors = $newDelivery->validate($data);

        if (empty($model_errors)) {
            $delivery = new DeliveryInformation(PDO());
            $newDelivery->fill($data);
            if (
                $delivery->isDuplicateAddress(
                    AUTHGUARD()->user()->id_account,
                    $data['house_number'],
                    $data['ward'],
                    $data['district'],
                    $data['city'],
                    null,
                    $data['receiver_name'],
                    $data['receiver_phone']
                )
            ) {
                $this->sendPage('orders/start_order', [
                    'id_delivery' => $data['id_delivery'],
                    'total_price' => $total_price,
                    'delivery_list' => $Deliveries,
                    'product_ids' => $product_ids,
                    'errors' => 'Địa chỉ giao hàng đã tồn tại!'
                ]);
                return;
            }


            $newDelivery->save();
            $Deliveries = $modelDelivery->getAllDeliveryInfo(AUTHGUARD()->user()->id_account);

            $this->sendPage('orders/start_order', [
                // 'id_delivery' => $data['id_delivery'],
                'total_price' => $total_price,
                'delivery_list' => $Deliveries,
                'product_ids' => $product_ids,
                'success' => 'Thêm địa chỉ mới thành công.'
            ]);
            return;
        }

        $this->saveFormValues($_POST);
        // redirect('/delivery/create', ['errors' => $model_errors]);
        $this->sendPage('orders/start_order', [
            // 'id_delivery' => $data['id_delivery'],
            'total_price' => $total_price,
            'delivery_list' => $Deliveries,
            'product_ids' => $product_ids,
            'errors' => $model_errors
        ]);
        return;
    }

    protected function filterDeliveryData(array $data)
    {
        return [
            'house_number' => $data['house_number'] ?? '',
            'ward' => $data['ward'] ?? '',
            'district' => $data['district'] ?? '',
            'city' => $data['city'] ?? '',
            'receiver_name' => $data['receiver_name'] ?? '',
            'receiver_phone' => $data['receiver_phone'] ?? '',
            'shipping_fee' => $data['shipping_fee'] ?? null
        ];
    }

    public function edit()
    {
        $modelDelivery = new DeliveryInformation(PDO());
        $deliveries = $modelDelivery->getAllDeliveryInfo(AUTHGUARD()->user()->id_account);

        $user = AUTHGUARD()->user();
        unset($user->password);

        $activitymodel = new ActivityHistory(pdo());
        $activities = $activitymodel->getByAccountId($user->id_account);

        $data = [
            'id_delivery' => $_POST['id_delivery'] ?? '',
            'receiver_name' => $_POST['receiverName'] ?? '',
            'receiver_phone' => $_POST['receiverPhone'] ?? '',
            'house_number' => $_POST['houseNumber'] ?? '',
            'ward' => $_POST['ward'] ?? '',
            'district' => $_POST['district'] ?? '',
            'city' => $_POST['city'] ?? '',
            'id_account' => AUTHGUARD()->user()->id_account,
        ];

        //Lấy thông tin cũ từ database
        $oldData = $modelDelivery->getById($data['id_delivery']);

        //Nếu dữ liệu mới giống hệt dữ liệu cũ, không cập nhật
        if ($oldData && $this->isSameData($oldData, $data)) {

            $this->sendPage('account/index', [
                'activities' => $activities,
                'user' => $user,
                'deliveries' => $deliveries,
                'errors' => 'Không có thay đổi nào được thực hiện.'
            ]);
            return;
        }

        // Kiểm tra xem địa chỉ có bị trùng lặp không
        if (
            $modelDelivery->isDuplicateAddress(
                $data['id_account'],
                $data['house_number'],
                $data['ward'],
                $data['district'],
                $data['city'],
                $data['id_delivery'],
                $data['receiver_name'],
                $data['receiver_phone']
            )
        ) {
            $this->sendPage('account/index', [
                'activities' => $activities,
                'user' => $user,
                'deliveries' => $deliveries,
                'errors' => 'Địa chỉ giao hàng đã tồn tại.'
            ]);
            return;
        }

        //Tiến hành cập nhật thông tin giao hàng
        $modelDelivery->fill($data);
        $modelDelivery->save();



        $deliveries = $modelDelivery->getAllDeliveryInfo(AUTHGUARD()->user()->id_account);
        $this->sendPage('account/index', [
            'activities' => $activities,
            'user' => $user,
            'deliveries' => $deliveries,
            'success' => 'Cập nhật thông tin giao hàng thành công!'
        ]);
        return;
    }

    public function update()
    {

        $total_price = $_POST['total_price'];
        $modelDelivery = new DeliveryInformation(PDO());
        $Deliveries = $modelDelivery->getAllDeliveryInfo(AUTHGUARD()->user()->id_account);
        $product_ids = $_POST['product_ids'];


        $data = [
            'id_delivery' => $_POST['id_delivery'] ?? '',
            'receiver_name' => $_POST['receiverName'] ?? '',
            'receiver_phone' => $_POST['receiverPhone'] ?? '',
            'house_number' => $_POST['houseNumber'] ?? '',
            'ward' => $_POST['ward'] ?? '',
            'district' => $_POST['district'] ?? '',
            'city' => $_POST['city'] ?? '',
            'id_account' => AUTHGUARD()->user()->id_account,
        ];

        $delivery = new DeliveryInformation(PDO());

        //Lấy thông tin cũ từ database
        $oldData = $delivery->getById($data['id_delivery']);

        //Nếu dữ liệu mới giống hệt dữ liệu cũ, không cập nhật
        if ($oldData && $this->isSameData($oldData, $data)) {

            $this->sendPage('orders/start_order', [
                'id_delivery' => $data['id_delivery'],
                'total_price' => $total_price,
                'delivery_list' => $Deliveries,
                'product_ids' => $product_ids,
                'errors' => 'Không có thay đổi nào được thực hiện.'
            ]);
            return;
        }

        // Kiểm tra xem địa chỉ có bị trùng lặp không
        if (
            $delivery->isDuplicateAddress(
                $data['id_account'],
                $data['house_number'],
                $data['ward'],
                $data['district'],
                $data['city'],
                $data['id_delivery'],
                $data['receiver_name'],
                $data['receiver_phone']
            )
        ) {
            $this->sendPage('orders/start_order', [
                'id_delivery' => $data['id_delivery'],
                'total_price' => $total_price,
                'delivery_list' => $Deliveries,
                'product_ids' => $product_ids,
                'errors' => 'Địa chỉ giao hàng đã tồn tại.'
            ]);
            return;
        }

        //Tiến hành cập nhật thông tin giao hàng
        $delivery->fill($data);
        $delivery->save();



        $newdelivery = $modelDelivery->getAllDeliveryInfo(AUTHGUARD()->user()->id_account);
        $this->sendPage('orders/start_order', [
            'total_price' => $total_price,
            'delivery_list' => $newdelivery,
            'product_ids' => $product_ids,
            'success' => 'Cập nhật thông tin giao hàng thành công!'
        ]);
        return;
    }

    private function isSameData(array $oldData, array $newData): bool
    {
        return $oldData['receiver_name'] === $newData['receiver_name']
            && $oldData['receiver_phone'] === $newData['receiver_phone']
            && $oldData['house_number'] === $newData['house_number']
            && $oldData['ward'] === $newData['ward']
            && $oldData['district'] === $newData['district']
            && $oldData['city'] === $newData['city'];
    }

}
