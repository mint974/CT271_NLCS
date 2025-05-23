<?php

namespace App\Controllers;

use League\Plates\Engine;

class Controller
{
    protected $view;

    public function __construct()
    {
        $this->view = new Engine(ROOTDIR . 'app/views');
    }

    public function sendPage($page, array $data = [])
    {
        exit($this->view->render($page, $data));
    }

    // Lưu các giá trị của form được cho trong $data vào $_SESSION 
    protected function saveFormValues(array $data, array $except = [])
    {
        $form = [];
        foreach ($data as $key => $value) {
            if (!in_array($key, $except, true)) {
                $form[$key] = $value;
            }
        }
        $_SESSION['form'] = $form;
    }

    protected function getSavedFormValues()
    {
        return session_get_once('form', []);
    }

    public function sendNotFound()
    {
        http_response_code(404);
        exit($this->view->render('errors/404'));
    }

    public function checkCsrf(): bool
    {
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' &&
            (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) ) {
            return false;
        }
        return true;
    }

    public function checkroleadmin(){
        if(AUTHGUARD()->user()->role === 'khách hàng'){
            $this->sendNotFound();
            exit();
        }
    }
    
  public function checkLogin()
  {
    if (!AUTHGUARD()->isUserLoggedIn()) {
      redirect('/login');
    }
  }

}
