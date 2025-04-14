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

  public function search()
  {
    if (!$this->checkCsrf()) {
      $_SESSION['error'] = 'Lỗi CSRF, hãy kiểm tra và thử lại!';
      $this->index();
      exit();
    }

    $keyword = $_POST['search'] ?? '';

    $productModel = new Product(PDO());
    $results = $productModel->searchProductsByKeyword($keyword);
    $this->applyDiscountToResults($results);
    // dd($results);
    // exit();

    $this->sendPage('search/index', [
      'keyword' => $keyword,
      'results' => $results
    ]);
  }

  function applyDiscountToResults(array &$results)
  {
    foreach ($results as &$product) {
      if (!empty($product['id_promotion']) && is_numeric($product['discount_rate'])) {
        $price = (float) $product['price'];
        $discountRate = (float) $product['discount_rate'];
        $product['discounted_price'] = round($price * (1 - $discountRate / 100), 0);
      } else {
        $product['discounted_price'] = null; // Không có khuyến mãi
      }
    }
  }

  function introduction()
  {
   
    $this->sendPage('/introduction/index');
  
  }

  function adminindex()
  {
    $user = AUTHGUARD()->user();
    unset($user->password);
    // dd($user);
    // exit();
    $this->sendPage('adminindex', [
      'user' => $user
    ]);
  
  }

}