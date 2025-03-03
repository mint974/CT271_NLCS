<?php

namespace App\Controllers;
use App\Models\Product;
class HomeController extends Controller
{
  public function __construct()
  {
    parent::__construct();
  }

  public function index()
  {

    $id_catalog1 = 'prodcata001'; 
    $id_catalog2 = 'prodcata002'; 

    $productModel = new Product(PDO());
    $products1 = $productModel->getByCatalogId($id_catalog1);
    $products2 = $productModel->getByCatalogId($id_catalog2);
    $discountedProducts = $productModel->getDiscountedProducts();
    $this->sendPage('index', [
         'products1' => $products1,
         'products2' => $products2,
         'discountedProducts' => $discountedProducts
    ]);
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
