<?php

namespace App\Controllers\Auth;

use App\Models\User;
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
      //dd($_POST);
        $this->saveFormValues($_POST, ['password', 'password_confirm']);

        $data = $this->filterUserData($_POST);
        $newUser = new User(PDO()); // Sử dụng kết nối có sẵn
        
        $model_errors = $newUser->validate($data);
        //dd($model_errors);
        //dd($_POST);
        if (empty($model_errors)) {
            // Đảm bảo role luôn là 0 (khách hàng) khi đăng ký
            $data['role'] = 0;
            $newUser->fill($data)->save();
            //dd($_POST);
            $message = ['success' => 'User has been created successfully.'];
            redirect('/login', $message);
        }
        
        // Dữ liệu không hợp lệ...
        redirect('/register', ['errors' => $model_errors]);
        
    }

    protected function filterUserData(array $data)
    {
        return [
            'username' => $data['username'] ?? null,
            'email' => filter_var($data['email'], FILTER_VALIDATE_EMAIL),
            'phone_number' => $data['phone_number'] ?? null,
            'password' => $data['password'] ?? null,
            'password_confirm' => $data['password_confirm'] ?? null
            
        ];
    }
}
