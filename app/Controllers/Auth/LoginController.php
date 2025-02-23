<?php

namespace App\Controllers\Auth;

use App\Models\User;
use App\Controllers\Controller;

class LoginController extends Controller
{
    public function create()
    {
        if (AUTHGUARD()->isUserLoggedIn()) {
            redirect('/home');
        }

        $data = [
            'success' => session_get_once('success'),
            'old' => $this->getSavedFormValues(),
            'errors' => session_get_once('errors')
        ];

        $this->sendPage('auth/login', $data);
    }

    public function store()
    {
        $user_credentials = $this->filterUserCredentials($_POST);
        $errors = [];

        $user = (new User(PDO()))->where('email', $user_credentials['email']); // Sử dụng kết nối có sẵn

        if ($user->id_account == -1) { 
            // Người dùng không tồn tại
            $errors['email'] = 'Invalid email or password.';
        } elseif (!password_verify($user_credentials['password'], $user->password)) { 
            // Sai mật khẩu
            $errors['password'] = 'Invalid email or password.';
        } else {
            // Đăng nhập thành công
            AUTHGUARD()->login($user, $user_credentials);
            //dd($user);
            redirect('/home');
            return;
        }

        // Đăng nhập không thành công: lưu giá trị form, trừ password
        $this->saveFormValues($_POST, ['password']);
        redirect('/login', ['errors' => $errors]);
    }

    public function destroy()
    {
        AUTHGUARD()->logout();
        redirect('/login');
    }

    protected function filterUserCredentials(array $data)
    {
        return [
            'email' => filter_var($data['email'], FILTER_VALIDATE_EMAIL),
            'password' => $data['password'] ?? null
        ];
    }
}
