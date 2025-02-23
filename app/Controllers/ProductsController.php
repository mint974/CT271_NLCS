<?php

namespace App\Controllers;

use App\Models\Product;

class ProductsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $success = session_get_once('success');
        $products = (new Product(PDO()))->getAll();
        
        $this->sendPage('products/index', [
            'products' => $products,
            'success' => $success
        ]);
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

    public function edit($productId)
    {
        $product = (new Product(PDO()))->find($productId);
        if (!$product) {
            $this->sendNotFound();
        }
        
        $form_values = $this->getSavedFormValues();
        $data = [
            'errors' => session_get_once('errors'),
            'product' => !empty($form_values) ? array_merge($form_values, ['id_product' => $product->id_product]) : (array) $product
        ];
        
        $this->sendPage('products/edit', $data);
    }

    public function update($productId)
    {
        $product = (new Product(PDO()))->find($productId);
        if (!$product) {
            $this->sendNotFound();
        }
        
        $data = $this->filterProductData($_POST);
        $model_errors = $product->validate($data);
        
        if (empty($model_errors)) {
            $product->fill($data)->save();
            redirect('/products', ['success' => 'Product has been updated successfully.']);
        }
        
        $this->saveFormValues($_POST);
        redirect('/products/edit/' . $productId, ['errors' => $model_errors]);
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
            'name' => $data['name'] ?? '',
            'description' => $data['description'] ?? null,
            'quantity' => isset($data['quantity']) ? (int) $data['quantity'] : 0,
            'price' => isset($data['price']) ? (float) $data['price'] : 0.00,
            'delivery_limit' => isset($data['delivery_limit']) ? (int) $data['delivery_limit'] : 0,
            'unit' => $data['unit'] ?? '',
            'id_promotion' => isset($data['id_promotion']) ? (int) $data['id_promotion'] : null
        ];
    }
}