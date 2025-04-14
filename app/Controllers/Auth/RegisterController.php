<?php

namespace App\Controllers\Auth;

use App\Models\User;
use App\Models\Order;
use App\Models\DeliveryInformation;
use App\Models\ActivityHistory;

use App\Controllers\Controller;

class RegisterController extends Controller
{
    public function __construct()
    {
        if (AUTHGUARD()->isUserLoggedIn()) {
            redirect('/home');
        }

        parent::__construct();
    }

    public function create()
    {
        $data = [
            'old' => $this->getSavedFormValues(),
            'errors' => session_get_once('errors')
        ];

        $this->sendPage('auth/register', $data);
    }

    public function store()
    {
        $this->saveFormValues($_POST, ['password', 'password_confirm']);

        $user_fields = ["username", "email", "password", "password_confirm"];

        $data = [];
        $data_delivery = [];

        foreach ($_POST as $key => $value) {
            if (in_array($key, $user_fields)) {
                $data[$key] = $value;
            } else {
                $data_delivery[$key] = $value;
            }
        }

        $datauser = $this->filterDataUser($data);
        $newUser = new User(PDO());

        $model_errorsUser = $newUser->validate($datauser);

        if (empty($model_errorsUser)) {
            $newUser->fill($datauser)->save();

            // Tạo thông tin giao hàng
            $data_delivery['id_account'] = $newUser->id_account;
            $data_delivery['receiver_name'] = $newUser->username;

            $newDelivery = new DeliveryInformation(PDO());
            $model_errorsDe = $newDelivery->validate($data_delivery);

            if (!empty($model_errorsDe)) {
                redirect('/register', ['errors' => $model_errorsDe]);
            }

            $newDelivery->fill($data_delivery)->save();

            // Tạo giỏ hàng
            $order = new Order(PDO());
            //$order->id_order = sprintf("REORD%d", $newUser->id_account);
            $order->id_account = $newUser->id_account;
            $order->status = 'Giỏ Hàng';
            $order->save_def();

            //tạo lịch sử hoạt động

            $modelActivity = new ActivityHistory(pdo());
            $modelActivity->id_account = $newUser->id_account;
            $modelActivity->action = "Tạo tài khoản";
            $modelActivity->status = "Hoạt động";
            $modelActivity->created_by = $newUser->id_account;
            $modelActivity->save();


            redirect('/login', ['success' => 'User created successfully.']);
        }
        redirect('/register', ['errors' => $model_errorsUser]);
    }

    protected function filterDataUser(array $data)
    {
        return [
            'username' => $data['username'] ?? null,
            'email' => filter_var($data['email'], FILTER_VALIDATE_EMAIL),
            'password' => $data['password'] ?? null,
            'password_confirm' => $data['password_confirm'] ?? null
        ];
    }
}
