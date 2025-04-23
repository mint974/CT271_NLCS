<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\ProductReceipt;
use App\Models\ProductReceiptDetails;
use App\Models\Supplier;
use App\Models\User;


class ReceiptController extends Controller
{
    public function __construct()
    {

        parent::__construct();
        $this->checkroleadmin();
    }

    public function index($error = null, $success = null)
    {
        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['errors'] ?? null;
        unset($_SESSION['success'], $_SESSION['errors']);
        $receiptModel = new ProductReceipt(pdo());
        $supplierModel = new Supplier(pdo());

        $receiptList = $receiptModel->getAll();

        $receipts = [];

        foreach ($receiptList as $receipt) {
            $supplier = $supplierModel->find($receipt->id_supplier);
            $receipts[] = [
                'receipt' => $receipt,
                'supplier' => $supplier
            ];
        }
        // dd($receipts);
        $this->sendPage('receipts/index', [
            'receipts' => $receipts,
            'errors' => $error,
            'success' => $success
        ]);
    }



    public function storepage()
    {
        $this->sendPage('receipts/add');
    }

    public function store()
    {
        $errors = [];

        if (!$this->checkCsrf()) {
            $errors['csrf'] = 'Lỗi CSRF, vui lòng thử lại.';
        }

        $data = $this->filterReceiptData($_POST);

        $receipt = new ProductReceipt(pdo());
        if (method_exists($receipt, 'validate')) {
            $errors = $receipt->validate($data);
        }

        if (empty($errors)) {
            if (method_exists($receipt, 'fill') && method_exists($receipt, 'save')) {
                if (!$receipt->fill($data)->save()) {
                    $errors['save'] = 'Không thể lưu phiếu nhập.';
                } else {
                    $success = 'Thêm phiếu nhập thành công.';
                    $this->sendPage('receipts/index', [
                        'receipts' => [], // Hiện tại model chưa có getAll
                        'success' => $success
                    ]);
                    return;
                }
            }
        }

        $this->saveFormValues($_POST);
        $this->sendPage('receipts/add', ['errors' => $errors]);
    }

    public function search()
    {

        if (!$this->checkCsrf()) {
            $this->index('Lỗi CSRF, vui lòng thử lại.');
            return;
        }

        $searchData = $this->fillformsearch($_POST);
        $receiptModel = new ProductReceipt(pdo());
        $supplierModel = new Supplier(pdo());
        $receipts = [];
        //   dd($searchData);
        if (!empty($searchData['id_receipt'])) {
            $receipt = $receiptModel->where('id_receipt', $searchData['id_receipt']);

            if ($receipt && $receipt instanceof ProductReceipt) {
                $supplier = $supplierModel->find($receipt->id_supplier);
                $receipts[] = [
                    'receipt' => $receipt,
                    'supplier' => $supplier
                ];
            }
        } elseif (!empty($searchData['supplier_name'])) {
            $suppliers = $supplierModel->where('name', $searchData['supplier_name']);

            // dd($suppliers);
            foreach ($suppliers as $supplier) {

                $receiptlist = $receiptModel->getByIdSuplider($supplier->id_supplier);
                foreach ($receiptlist as $receipt) {
                    $receipts[] = [
                        'receipt' => $receipt,
                        'supplier' => $supplier
                    ];
                }
            }

        } elseif (!empty($searchData['receipt_date'])) {
            $receiptList = $receiptModel->getByTime($searchData['receipt_date']);

            foreach ($receiptList as $receipt) {
                $supplier = $supplierModel->find($receipt->id_supplier);
                $receipts[] = [
                    'receipt' => $receipt,
                    'supplier' => $supplier
                ];
            }
        } else {
            $this->index('Bạn chưa nhập thông tin tìm kiếm.');
            return;
        }


        $this->sendPage('receipts/index', [
            'receipts' => $receipts,
            'errors' => null,
            'success' => null
        ]);
    }


    protected function filterReceiptData(array $data): array
    {
        return [
            'id_receipt' => $data['id_receipt'] ?? '',
            'id_supplier' => $data['id_supplier'] ?? '',
            'id_account' => AUTHGUARD()->user()->id_account ?? null
        ];
    }

    protected function fillformsearch(array $data): array
    {
        return [
            'id_receipt' => htmlspecialchars($data['id_receipt'] ?? ''),
            'supplier_name' => htmlspecialchars($data['supplier_name'] ?? ''),
            'receipt_date' => htmlspecialchars($data['receipt_date'] ?? '')
        ];
    }

    public function ReceiptDetail($id)
    {

        $receiptModel = new ProductReceipt(pdo());
        $supplierModel = new Supplier(pdo());
        $userModel = new User(pdo());
        $productModel = new Product(pdo());

        $receipt = $receiptModel->where('id_receipt', $id);
        $supplier = $supplierModel->find($receipt->id_supplier);
        $user = $userModel->where('id_account', $receipt->id_account);
        unset($user->password);

        $productList = (new ProductReceiptDetails(pdo()))->where('id_receipt', $receipt->id_receipt);

        $ProductReceiptDetails = [];

        foreach ($productList as $item) {
            $product = $productModel->where('id_product', $item->id_product);
            $ProductReceiptDetails[] = [
                'ProductReceiptDetail' => $item,
                'product' => $product
            ];
        }

        // dd($ProductReceiptDetails);

        $this->sendPage('receipts/receiptDetail', [
            'receipt' => $receipt,
            'supplier' => $supplier,
            'user' => $user,
            'ProductReceiptDetails' => $ProductReceiptDetails
        ]);

    }

    public function updatePage()
    {
        $supplierModel = new Supplier(pdo());
        $productModel = new Product(pdo());

        $suppliers = $supplierModel->getAll();
        $products = $productModel->getOutOfStockProducts();

        // dd($products);
        $this->sendPage('receipts/update', [
            'suppliers' => $suppliers,
            'products' => $products
        ]);
    }

    public function fillReceiptFormUpdate($data)
    {
        $receipt = new ProductReceipt(pdo());
        $receipt->id_supplier = htmlspecialchars($data['id_supplier'] ?? '');
        $receipt->id_account = AUTHGUARD()->user()->id_account;
        return $receipt;
    }

    public function getSelectedProducts(array $postData): array
    {
        if (!isset($postData['products']) || !is_array($postData['products'])) {
            return [];
        }

        $selectedProducts = [];

        foreach ($postData['products'] as $product) {
            if (isset($product['selected']) && $product['selected'] === "1") {
                $selectedProducts[] = $product;
            }
        }

        return $selectedProducts;
    }


    public function update()
    {
        // dd($_POST);
        $receipt = $this->fillReceiptFormUpdate($_POST);
        $productList = $this->getSelectedProducts($_POST);

        $error = [];
        $pdo = pdo(); // PDO connection
        $pdo->beginTransaction(); // Bắt đầu transaction

        try {
            $newreceipt = $receipt->save();

            if (!$newreceipt) {
                throw new Exception("Lỗi khi tạo đơn hàng mới");
            }

            $ProductReceiptDetail = new ProductReceiptDetails($pdo);
            $productModel = new Product($pdo);

            foreach ($productList as $item) {
                $item['id_receipt'] = $newreceipt->id_receipt;
                $details = $ProductReceiptDetail->fill($item);

                if (!$details->save()) {
                    throw new Exception("Lỗi khi lưu chi tiết phiếu nhập cho sản phẩm ID: {$details->id_product}");
                }

                if (!$productModel->updateQuantityAndPrice($details->id_product, $details->quantity, $details->selling_price)) {
                    throw new Exception("Lỗi khi cập nhật số lượng và giá cho sản phẩm ID: {$details->id_product}");
                }
            }

            $pdo->commit(); // Thành công -> lưu luôn
        } catch (Exception $e) {
            $pdo->rollBack(); // Có lỗi -> rollback tất cả
            $error[] = $e->getMessage(); // Ghi nhận lỗi
            $this->index($error);
            exit;
        }
        $success = 'Tạo đơn hàng mới thành công, đơn vừa tạo là: ' . $newreceipt->id_receipt;
        redirect('/receipt/index', [
            'sucess' => $success
        ]);
    }
}
