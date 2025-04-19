<?php

namespace App\Controllers;

use App\Controllers\Auth\LoginController;
use App\Models\User;
use App\Models\DeliveryInformation;
use App\Models\ActivityHistory;

use App\Mailer;
class UserController extends Controller
{
    public function __construct()
    {
        $publicPaths = ['/account/reactivate', '/account/reactivatepage'];

        $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if (!in_array($currentPath, $publicPaths) && !AUTHGUARD()->isUserLoggedIn()) {
            redirect('/login');
        }

        parent::__construct();
    }



    public function index($id_account, string $error = null, string $Success = null)
    {
        $usermodel = new User(pdo());
        $user = $usermodel->where('id_account', $id_account);
        unset($user->password);

        $deliverymodel = new DeliveryInformation(pdo());
        $deliveries = $deliverymodel->getAllDeliveryInfo($user->id_account);

        $activitymodel = new ActivityHistory(pdo());
        $activities = $activitymodel->getByAccountId($user->id_account);

        $this->sendPage('account/index', [
            'activities' => $activities,
            'deliveries' => $deliveries,
            'user' => $user,
            'errors' => $error,
            'success' => $Success
        ]);
    }

    public function adminindex($error = null, $users = null)
    {
        if (empty($users)) {
            $usermodel = new User(pdo());
            $users = $usermodel->getAllUser();
        }
        // $subject = 'Kích Hoạt tài khoản thành công!';
        // $body = "<p>Xin chào <strong></strong>,</p>
        //          <p>Chúng tôi đã nhận được liên hệ của bạn với nội dung:</p>
        //          <blockquote></blockquote>
        //          <p>Chúng tôi sẽ phản hồi sớm nhất có thể!</p>
        //          <p>Trân trọng,<br>Mint Fresh Fruit</p>";

        // Mailer::send('tanb2205957@student.ctu.edu.vn', 'Minh tân', $subject, $body);
        // // dd($users);
        // exit();
        $activitymodel = new ActivityHistory(pdo());
        $activities = $activitymodel->countAccountStatuses();

        // dd($users);
        // exit();
        $this->sendPage('account/adminindex', [

            // 'deliveries' => $deliveries,
            'activities' => $activities,
            'users' => $users,
            'errors' => $error

        ]);
        return;
    }

    public function edit()
    {
        $user = AUTHGUARD()->user();
        $form_values = $this->getSavedFormValues();

        $data = [
            'errors' => session_get_once('errors'),
            'user' => (!empty($form_values)) ?
                array_merge($form_values, ['id_account' => $user->id_account]) :
                (array) $user
        ];

        $this->sendPage('user/edit', $data);
    }

    public function update()
    {
        $data = $this->filterUserData($_POST);
    
        $user = (new user(pdo()))->where('id_account', $data['id_account']);
        $model_errors = $user->validate($data);
    
        // Xử lý ảnh đại diện
        $avatarPath = $this->handleAvatarUpload();
    
        if ($avatarPath === null) {
            // Không upload ảnh mới -> giữ nguyên
            $data['avatar'] = $user->url;
        } elseif ($avatarPath === 'Chỉ hỗ trợ định dạng các tệp: jpg, jpeg, png, gif') {
            // Sai định dạng ảnh
            $model_errors['avatar'] = $avatarPath;
        } elseif ($avatarPath === $user->url) {
            // Trùng ảnh cũ
            $model_errors['avatar'] = 'Trùng ảnh, nếu bạn không thay đổi avatar thì giữ nguyên.';
        } else {
            // Có ảnh mới, hợp lệ
            $data['avatar'] = $avatarPath;
            $user->url = $avatarPath;
        }
    
        // Kiểm tra nếu không thay đổi gì và không nhập mật khẩu
        if (empty($data['password']) && $user->isDuplicateAccountInfo($data)) {
            $model_errors['form'] = "Bạn chưa thay đổi gì cả.";
        }
    
        // Xử lý mật khẩu
        if (!empty($data['password']) && !password_verify($data['password'], $user->password)) {
            $model_errors['password_old'] = "Mật khẩu cũ không đúng.";
        } elseif (!empty($data['new_password']) && password_verify($data['new_password'], $user->password)) {
            $model_errors['password_new'] = "Mật khẩu mới trùng mật khẩu cũ.";
        }
    
        // Nếu không có lỗi -> cập nhật
        if (empty($model_errors)) {
            $user->fill($data)->save();
    
            $newuser = (new user(pdo()))->where('id_account', $user->id_account);
            $messages = ['success' => 'Cập nhật thông tin tài khoản thành công.'];
    
            $this->sendPage('/account/update', [
                'user' => $newuser,
                'success' => $messages
            ]);
            return;
        }
    
        // Gửi lại trang với dữ liệu và lỗi
        $this->saveFormValues($_POST);
    
        $this->sendPage('/account/update', [
            'user' => $user,
            'errors' => $model_errors
        ]);
    }
    


    public function updatepage($id)
    {
        $user = (new User(pdo()))->where('id_account', $id);

        if ($user) {
            $this->sendPage('account/update', [
                'user' => $user,
            ]);
        } else {
            $_SESSION['form_error'] = "Không tìm thấy tài khoản cần sửa.";
            redirect($_SERVER['HTTP_REFERER'] ?? '/account/index');
        }
    }


    // tải trang dừng tài khoản
    public function suspendpage($id)
    {
        $user = (new User(pdo()))->where('id_account', $id);

        if ($user) {
            $this->sendPage('account/suspend_account', [
                'user' => $user,
            ]);
        } else {
            $_SESSION['form_error'] = "Không tìm thấy tài khoản.";
            redirect($_SERVER['HTTP_REFERER'] ?? '/account/index');
        }
    }

    //cập nhật avatar
    protected function handleAvatarUpload(): ?string
    {
        if (isset($_FILES['avatar']) && !empty($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            // dd("d");
            // exit;
            $targetDir = 'assets/image/avatar/';

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $imageFileType = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
            $allowedFormats = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($imageFileType, $allowedFormats) && getimagesize($_FILES['avatar']['tmp_name'])) {

                $uploadHash = md5_file($_FILES['avatar']['tmp_name']);
                $existingFile = null;

                // Tìm file trùng nội dung nếu có
                foreach (glob($targetDir . '*.' . $imageFileType) as $file) {
                    if (md5_file($file) === $uploadHash) {
                        $existingFile = $file;
                        break;
                    }
                }

                if ($existingFile) {
                    return $existingFile; // File trùng, trả về đường dẫn
                } else {
                    $newFileName = uniqid('avatar_', true) . '.' . $imageFileType;
                    $targetFile = $targetDir . $newFileName;

                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFile)) {
                        return $targetFile;
                    }
                }
            } else {
                return 'Chỉ hỗ trợ định dạng các tệp: jpg, jpeg, png, gif';
            }
        }

        return null; // Không có ảnh hoặc upload thất bại
    }


    protected function filterUserData(array $data)
    {
        $user = (new user(pdo()))->where('id_account', $data['id_account']);

        return [
            'id_account' => $data['id_account'] ?? $user->id_account,
            'avatar' => '',
            'email' => $data['email'] ?? $user->email,
            'username' => $data['username'] ?? $user->username,
            'password' => $data['old_password'] ?? '',
            'new_password' => $data['new_password'] ?? '',
            'password_confirm' => $data['confirm_password'] ?? '',
            'role' => $data['role']
        ];
    }

    // dừng tài khoản
    public function suspend()
    {
        $activitymodel = new ActivityHistory(pdo());
        $data = $activitymodel->fill($_POST);

        if ($data->save()) {
            if ($data->id_account == $data->created_by) {
                $_SESSION['success'] = "Vô Hiệu hóa tài khoản thành công.";

                if ($data->id_account == $data->created_by) {
                    $destroy = (new LoginController(pdo()))->destroy();
                    exit();
                }
            } else {
                $success = "Đã vô hiệu hóa tài khoản người dùng" . htmlspecialchars($data->id_account) . " thành công.";

                $usermodel = new User(pdo());
                $users = $usermodel->getAllUser();

                $activitymodel = new ActivityHistory(pdo());
                $activities = $activitymodel->countAccountStatuses();

                $this->sendPage('account/adminindex', [

                    // 'deliveries' => $deliveries,
                    'activities' => $activities,
                    'users' => $users,
                    'success' => $success
                ]);
                return;
            }


        }

        $user = (new User(pdo()))->where('id_account', $data->id_account);

        $this->sendPage('account/suspend_account', [
            'user' => $user,
        ]);
    }



    //user xin cấp lại tài khoản
    public function reactivatepage()
    {
        $this->sendPage('account/reactivate');
    }

    public function reactivate()
    {
        $email = $_POST['email'];
        $pwd = $_POST['password'];
        $errors = [];

        $user = (new User(pdo()))->where('email', $email);

        if (!empty($user) && isset($user->id_account)) {
            if (!isset($user->password) || !password_verify($pwd, $user->password)) {
                $errors['password'] = "Mật khẩu không đúng.";
            }
        } else {
            $errors['email'] = "Không tồn tại tài khoản cho email bạn vừa nhập.";
        }

        if (empty($errors)) {
            $data['id_account'] = $user->id_account;
            $data['action'] = 'Yêu cầu khôi phục tài khoản';
            $data['status'] = 'Khôi phục tài khoản';

            $data['created_by'] = $user->id_account; // hoặc người gửi yêu cầu

            $activity = (new ActivityHistory(pdo()))->fill($data);

            if ($activity->save()) {
                $_SESSION['success'] = "Gửi yêu cầu khôi phục tài khoản thành công, vui lòng kiểm tra email thường xuyên để cập nhật.";
                redirect('/home');
            } else {
                $errors['form'] = "Có lỗi xảy ra, vui lòng thử lại.";
            }
        }

        // Gửi lại trang với lỗi
        $this->sendPage('account/reactivate', [
            'errors' => $errors
        ]);
    }

    public function activatepage($id_account)
    {
        $user = (new User(pdo()))->wherearray('id_account', $id_account);
        $this->sendPage('account/activate', [
            'user' => $user
        ]);
    }


    public function activate()
    {
        $errors = [];
        if (!$this->checkCsrf()) {
            $errors['Crsf'] = 'Lỗi CSRF, hãy kiểm tra và thử lại!';
        }

        $id = $_POST['id_account'];
        $user = (new User(pdo()))->where('id_account', $id);

        if (empty($errors)) {
            $data['id_account'] = $user->id_account;
            $data['created_by'] = AUTHGUARD()->user()->id_account;

            if ($_POST['status']) {

                $data['action'] = 'Khôi phục tài khoản thành công';
                $data['status'] = 'Hoạt động';

                $activity = (new ActivityHistory(pdo()))->fill($data);

                if ($activity->save()) {

                    //gửi mail
                    $subject = 'Kích Hoạt tài khoản thành công!';

                    $body = "<p>Xin chào <strong>" . htmlspecialchars($user->username) . "</strong>,</p>
                            <p>Chúng tôi đã nhận được liên hệ của bạn với nội dung: Yêu cầu khôi phục tài khoản.</p>
                            <blockquote></blockquote>
                            <p>Chúng tôi rất vui thông báo rằng tài khoản của bạn tại Mint Fresh Fruit đã được kích hoạt thành công.  
                            Giờ đây, bạn có thể đăng nhập và sử dụng đầy đủ các chức năng của hệ thống!</p>
                            <p>Trân trọng,<br>Mint Fresh Fruit Team <br> https://mintfreshfruit.com</p>";

                    Mailer::send($user->email, $user->username, $subject, $body);

                    $success = "Khôi phục tài khoản cho " . htmlspecialchars($user->username) . " thành công.";

                    $usermodel = new User(pdo());
                    $users = $usermodel->getAllUser();

                    $activitymodel = new ActivityHistory(pdo());
                    $activities = $activitymodel->countAccountStatuses();

                    $this->sendPage('account/adminindex', [

                        // 'deliveries' => $deliveries,
                        'activities' => $activities,
                        'users' => $users,
                        'success' => $success
                    ]);
                    return;
                } else {
                    $errors['form'] = "Có lỗi xảy ra, vui lòng thử lại.";
                }
            } else {
                $data['action'] = 'Khôi phục tài khoản không thành công';
                $data['status'] = 'Vô hiệu hóa tài khoản';

                $activity = (new ActivityHistory(pdo()))->fill($data);

                if ($activity->save()) {

                    //gửi mail
                    $subject = 'Kích Hoạt tài khoản không thành công!';

                    $body = "<p>Xin chào <strong>" . htmlspecialchars($user->username) . "</strong>,</p>
                            <p>Chúng tôi đã nhận được yêu cầu khôi phục/kích hoạt tài khoản của bạn.</p>
                            <p>Tuy nhiên, chúng tôi rất tiếc phải thông báo rằng yêu cầu kích hoạt tài khoản <strong>không thành công</strong> vào thời điểm này.</p>
                            <p>Vui lòng kiểm tra lại thông tin tài khoản hoặc liên hệ với bộ phận hỗ trợ để được giải đáp và xử lý trong thời gian sớm nhất.</p>
                            <p>Thông tin hỗ trợ:<br>
                            Email: mtan090704@gmail.com<br>
                            Website: <a href='https://mintfreshfruit.com'>mintfreshfruit.com</a></p>
                            <p>Trân trọng,<br>Mint Fresh Fruit Team</p>";

                    Mailer::send($user->email, $user->username, $subject, $body);

                    $errors['activate'] = "Khôi phục tài khoản cho " . htmlspecialchars($user->username) . "không thành công.";

                    $usermodel = new User(pdo());
                    $users = $usermodel->getAllUser();

                    $activitymodel = new ActivityHistory(pdo());
                    $activities = $activitymodel->countAccountStatuses();

                    $this->sendPage('account/adminindex', [

                        // 'deliveries' => $deliveries,
                        'activities' => $activities,
                        'users' => $users,
                        'errors' => $errors
                    ]);
                    return;
                } else {
                    $errors['form'] = "Có lỗi xảy ra, vui lòng thử lại.";
                }
            }
        }

        $usermodel = new User(pdo());
        $users = $usermodel->getAllUser();

        $activitymodel = new ActivityHistory(pdo());
        $activities = $activitymodel->countAccountStatuses();

        // Gửi lại trang với lỗi
        $this->sendPage('account/adminindex', [
            'activities' => $activities,
            'users' => $users,
            'errors' => $errors
        ]);
    }

    public function search()
    {
        $username = $_GET['username'] ?? '';
        $email = $_GET['email'] ?? '';
        $status = $_GET['status'] ?? '';

        $errors = [];

        // Validate email
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ.';
        }

        if (!empty($errors)) {
            $this->adminindex($errors, null);
            return;
        }

        $modeluser = new User(pdo());

        $users = [];

        if (!empty($email)) {
            $user = $modeluser->wherearray('email', $email);
            if (!empty($user)) {
                $users[] = $user;
            }
        } elseif (!empty($username)) {
            $user = $modeluser->wherearray('username', $username);
            if (!empty($user)) {
                $users[] = $user;
            }
        } elseif (!empty($status)) {
            $modelactivity = new ActivityHistory(pdo());
            $users = $modelactivity->find($status); // đã là mảng nhiều dòng
        }

        if (!empty($users)) {
            $this->adminindex(null, $users);
        } else {
            $errors['find'] = 'Không tìm thấy người dùng!';
            $this->adminindex($errors, []);
        }

        return;
    }


}