<?php

namespace App\Controllers\Auth;

use App\Models\User;
use App\Models\ActivityHistory;
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

        $errors = [];
        if (!$this->checkCsrf()) {
            $errors['Crsf'] = 'Lỗi CSRF, hãy kiểm tra và thử lại!';
        } else {
            $user_credentials = $this->filterUserCredentials($_POST);
            $user = (new User(PDO()))->where('email', $user_credentials['email']); // Sử dụng kết nối có sẵn

            if ($user->id_account == null) {
                // Người dùng không tồn tại
                $errors['email'] = 'Email chưa được đăng kí tài khoản, vui lòng đăng kí!';
            } elseif (!password_verify($user_credentials['password'], $user->password)) {
                // Sai mật khẩu
                $errors['password'] = 'Email hoặc mật khẩu không chính xác.';
            } else {
                // Kiểm tra trạng thái tài khoản nếu user tồn tại và mật khẩu đúng
                $activities = (new ActivityHistory(PDO()))->getByAccountId($user->id_account); // Hàm chỉ truy vấn bảng activity_history

                if (!empty($activities)) {
                    $latest = reset($activities);
                    if ($latest->status !== 'Hoạt động') {
                        $errors['account'] = 'Tài khoản của bạn đã bị vô hiệu hóa. Vui lòng liên hệ với chúng tôi để được hỗ trợ.';
                    }
                }
            }
        }
        if (!empty($errors)) {
            // Đăng nhập không thành công: lưu lại giá trị form (trừ password), chuyển hướng với lỗi
            $this->saveFormValues($_POST, ['password']);
            redirect('/login', ['errors' => $errors]);
            return;
        }

        // Đăng nhập thành công
        AUTHGUARD()->login($user, $user_credentials);
        // dd(AUTHGUARD()->user());
        // exit();
        if(AUTHGUARD()->user()->role === 'khách hàng'){
            redirect('/home');
        } else {
            redirect('/adminhome');
        }
        
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
