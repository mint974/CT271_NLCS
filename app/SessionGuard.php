<?php

namespace App;

use App\Models\User;
use App\Models\OrderDetail;

class SessionGuard
{
  protected $user;

  public function login(User $user, array $credentials)
  {
    $verified = password_verify($credentials['password'], $user->password);
    if ($verified) {
      $_SESSION['user_id'] = $user->id_account;
    }
    return $verified;
  }

  public function user()
  {
    if (!$this->user && $this->isUserLoggedIn()) {
      $this->user = (new User(PDO()))->where('id_account', $_SESSION['user_id']);
    }
    return $this->user;
  }

  public function logout()
  {
    $this->user = null;
    session_unset();
    session_destroy();
  }

  public function isUserLoggedIn()
  {
    return isset($_SESSION['user_id']);
  }

  public function showQuantity(): int
    {

        $id_account = $this->user()->id_account;
        $id_order = sprintf('REORD%d', $id_account);
        $orderDetailModel = new OrderDetail(PDO());
        return $orderDetailModel->getTotalQuantity($id_order);
        // DD($orderDetailModel->getTotalQuantity($id_order));
        // exit();

    }
}
