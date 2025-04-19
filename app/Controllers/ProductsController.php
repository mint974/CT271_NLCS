<?php

namespace App\Controllers;

use App\Models\ImageProduct;
use App\Models\Product;
use App\Models\Catalog;
use App\Models\Promotion;
use App\Models\Supplier;
use App\Models\ProductReceipt;
use App\Models\ProductReceiptDetails;
use App\Models\User;

class ProductsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {

        $productModel = new Product(PDO());
        $catalogModel = new Catalog(PDO());
        $discountedProducts = new Product(PDO());

        $catalogs = $catalogModel->getAllCatalog();

        $discountedProducts = $discountedProducts->getDiscountedProducts();
        
        foreach ($catalogs as &$catalog) {
            $catalog['product_list'] = $productModel->getByCatalogId($catalog['id_catalog']);
        }
        
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



$productModel = new Product(PDO());
        // truy xuất giảm giá 
        if (isset(($_POST['discountproduct']))) {
            
            $discountedProducts = $productModel->getDiscountedProducts();
            $this->sendPage('products/index', [
                'catalogs' => $catalogs,
                'discounted_Products' => $discountedProducts,
            ]);
        }
        // truy xuất sản phẩm thường
        else if (isset($_POST['id_catalog']) || !empty($_POST['id_catalog'])) {

            $catalogModel = new Catalog(PDO());
            $catalogname = $catalogModel->where('id_catalog', $_POST['id_catalog']);
            $productbycata[] = '';
            $productbycata['name'] = $catalogname->name;
            $productbycata['product_list'] = $productModel->getByCatalogId($catalogname->id_catalog);
            
            $this->sendPage('products/index', [
                'productbycata' => $productbycata,
                'catalogs' => $catalogs
            ]);
        } else {

            foreach ($catalogs as &$catalog) {
                $catalog['product_list'] = $productModel->getByCatalogId($catalog['id_catalog']);
            }
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

    //trang chi tiếc sản phẩm
    public function getproductbyid(string $id_product)
    {

        $productmodel = new Product(PDO());
        $product = $productmodel->where('id_product', $id_product);
        if ($product) {

            $catalogmodel = new Catalog(pdo());

            $catalogs = $catalogmodel->getCatalogByIdProduct($product->id_product);

            if (AUTHGUARD()->user()->role === 'khách hàng') {
                $this->sendPage('products/product_detail', [
                    'products' => $product,
                    'catalogs' => $catalogs,
                ]);
            } else {
                //lấy danh sách chi tiết nhập
                $product_receipt_detail_model = new ProductReceiptDetails(pdo());
                $product_receipt_details = $product_receipt_detail_model->FindAllReceiptDetailsByIdProduct($id_product);

                $productreceiptmodel = new ProductReceipt(pdo());
                $suppliermodel = new Supplier(pdo());
                $CreatedBytmodel = new User(pdo());

                $receipt_details_full = [];

                foreach ($product_receipt_details as $prd) {
                    $receipt = $productreceiptmodel->where('id_receipt', $prd->id_receipt);
                    if ($receipt !== null) {
                        $supplier = $suppliermodel->whereSup('id_supplier', $receipt->id_supplier);
                        $createdBy = $CreatedBytmodel->where('id_account', $receipt->id_account);
                        unset($createdBy->password); // Bỏ mật khẩu cho an toàn

                        $receipt_details_full[] = [
                            'detail' => $prd,
                            'receipt' => $receipt,
                            'supplier' => $supplier,
                            'createdBy' => $createdBy
                        ];
                    }
                }

                // dd($receipt_details_full);
                $this->sendPage('products/product_detail', [
                    'products' => $product,
                    'catalogs' => $catalogs,
                    'receipt_details_full' => $receipt_details_full
                ]);

            }



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

    // public function updatepage(string $id_product)
    // {
    //     $product = (new product(pdo()))->where('id_product', $id_product)->toArray();

    //     $product['catalogs'] = (new Catalog(pdo()))->getCatalogByIdProduct($product['id_product']);

    //     $promotions = (new Promotion(pdo()))->getAll();

    //     $catalogs = (new Catalog(pdo()))->getAllCatalog();

    //     // dd($product);
    //     $this->sendPage('products/update', [
    //         'product' => $product,
    //         'promotion' => $promotions,
    //         'catalogs' => $catalogs
            
    //     ]);

    // }

    public function UpdateInforPage(String $id_product){


        $product = (new Product(pdo()))->where('id_product', $id_product);

        $this->sendPage('products/update_infor_product', [
                    'product' => $product             
                ]);
    }
}
