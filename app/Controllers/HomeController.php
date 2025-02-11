<?php

namespace App\Controllers;

class HomeController extends Controller
{
  public function __construct()
  {
    parent::__construct();
  }

  public function index()
  {
    $this->sendPage('index');
  }

  private function checkLogin()
  {
    if (!AUTHGUARD()->isUserLoggedIn()) {
      redirect('/login');
    }
  }

  public function someOtherMethod()
  {
    $this->checkLogin();
  }
}
