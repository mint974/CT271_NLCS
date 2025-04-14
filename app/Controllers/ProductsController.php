<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Catalog;
//use App\Models\Promotion;

class ProductsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {

        // $productModel = new Product(PDO());
        $catalogModel = new Catalog(PDO());
        $discountedProducts = new Product(PDO());

        $catalogs = $catalogModel->getAllCatalog();

        $discountedProducts = $discountedProducts->getDiscountedProducts();

        // dd($catalogs);
        // exit();
        // Gửi dữ liệu đến view
        $this->sendPage('products/index', [
            'catalogs' => $catalogs,
            'discountedProducts' => $discountedProducts
        ]);
    }

    public function indexadmin($error = null, $searchResults = null)
    {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
    
        $productModel = new Product(PDO());
    
        if ($searchResults !== null) {
            
            $products = $searchResults;
            $totalItems = count($products);
            $totalPages = 1; 
            $promotionCounts = null; //
        } else {
            
            $products = $productModel->getall($limit, $offset);
            $totalItems = $productModel->countProduct();
            $totalPages = ceil($totalItems / $limit);
    
            
            $promotionCounts['on'] = count($productModel->getDiscountedProducts());
            $promotionCounts['off'] = $totalItems - $promotionCounts['on'];
        }
    
        $this->sendPage('products/indexadmin', [
            'promotionCounts' => $promotionCounts,
            'totalPages' => $totalPages,
            'products' => $products,
            'currentPage' => $page,
            'errors' => $error
        ]);
    }
    

    public function getprodcatabyid()
    {
        $catalogMode = new Catalog(PDO());
        $catalogs = $catalogMode->getAllCatalog();

        // truy xuất giảm giá 
        if (isset(($_POST['discountproduct']))) {
            $productModel = new Product(PDO());
            $discountedProducts = $productModel->getDiscountedProducts();
            $this->sendPage('products/index', [
                'catalogs' => $catalogs,
                'discounted_Products' => $discountedProducts,
            ]);
        }
        // truy xuất snar phẩm thường
        else if (isset($_POST['id_catalog']) || !empty($_POST['id_catalog'])) {

            $catalogModel = new Catalog(PDO());
            $catalogname = $catalogModel->where('id_catalog', $_POST['id_catalog']);
            $this->sendPage('products/index', [
                'productbycata' => $catalogname,
                'catalogs' => $catalogs
            ]);
        } else {
            $error = 'Không có doanh mục';
            $this->sendPage('products/index', [
                'catalogs' => $catalogs,
                'error' => $error
            ]);
        }

    }



    public function create()
    {
        $this->sendPage('products/create', [
            'errors' => session_get_once('errors'),
            'old' => $this->getSavedFormValues()
        ]);
    }

    public function store()
    {
        $data = $this->filterProductData($_POST);
        $newProduct = new Product(PDO());
        $model_errors = $newProduct->validate($data);

        if (empty($model_errors)) {
            $newProduct->fill($data)->save();
            redirect('/products', ['success' => 'Product has been created successfully.']);
        }

        $this->saveFormValues($_POST);
        redirect('/products/create', ['errors' => $model_errors]);
    }

    public function destroy($productId)
    {
        $product = (new Product(PDO()))->find($productId);
        if (!$product) {
            $this->sendNotFound();
        }

        $product->delete();
        redirect('/products', ['success' => 'Product has been deleted successfully.']);
    }

    protected function filterProductData(array $data)
    {
        return [
            'id_product' => $data['id_product'] ?? uniqid('"PROD_"'),
            'name' => $data['name'] ?? '',
            'description' => $data['description'] ?? null,
            'quantity' => isset($data['quantity']) ? (int) $data['quantity'] : 0,
            'price' => isset($data['price']) ? (float) $data['price'] : 0.00,
            'delivery_limit' => isset($data['delivery_limit']) ? (int) $data['delivery_limit'] : 0,
            'unit' => $data['unit'] ?? '',
            'id_promotion' => isset($data['id_promotion']) ? (string) $data['id_promotion'] : null
        ];
    }

    public function getproductbyid(string $id_product)
    {

        $productmodel = new Product(PDO());
        $product = $productmodel->where('id_product', $id_product);
        if ($product) {

            $this->sendPage('products/product_detail', [
                'products' => $product
            ]);


        } else {
            $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '/products';
            if ($redirectUrl == 'http://ct271-mintfreshfruit.localhost/products/load_prod_cata') {
                $redirectUrl = 'http://ct271-mintfreshfruit.localhost/products';
            }
            redirect($redirectUrl, ['error' => 'Lỗi truy xuất sản phẩm, Thử lại sao!']);
        }

    }

    public function search()
    {
        if (!$this->checkCsrf()) {
            $error[] = '';
            $error['csrf'] = 'Lỗi CSRF, hãy kiểm tra và thử lại!';
            $this->indexadmin($error);
            exit();
        }
        foreach (array_slice($_POST, 1, 1, true) as $key => $value) {
            $$key = $value; // Tạo biến từ key
        }


        $productModel = new Product(PDO());

        if (isset($id_product)) {
            $results = $productModel->searchadmin('id_product', $id_product);
        } elseif (isset($name_product)) {
            $results = $productModel->searchadmin('name', $name_product);
        } else {
            $results = $productModel->searchadmin('id_promotion', $promotion);
        }
        if ($results === []) {
            $error[] = '';
            $error['search'] = 'Không tồn tại sản phẩm như trên!';
            $this->indexadmin($error);
            exit();
        }

        $this->indexadmin(null, $results);
    }


}
