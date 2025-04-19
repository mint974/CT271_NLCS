<?php

namespace App\Controllers;

use App\Models\Supplier;
use Error;

class SupplierController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($error = null, $success = null)
    {
        $this->sendPage('suppliers/index', [
            'suppliers' => (new Supplier(pdo()))->getAll(),
            'errors' => $error,
            'success' => $success
        ]);
    }

    public function storepage()
    {
        $this->sendPage('suppliers/add');
    }

    public function store()
    {
        $errors = [];

        if (!$this->checkCsrf()) {
            $errors['csrf'] = 'Lỗi CSRF, hãy kiểm tra và thử lại!';
        }

        $data = $this->filterSupplierData($_POST);

        $supplier = new Supplier(pdo());
        $errors = $supplier->validate($data);

        if (empty($errors)) {
            if (!$supplier->fill($data)->save()) {
                $errors['save'] = 'Không thể lưu nhà cung cấp.';
            } else {
                $success = 'Đã thêm nhà cung cấp thành công.';
                $this->sendPage('suppliers/index', [
                    'suppliers' => (new Supplier(pdo()))->getAll(),
                    'success' => $success
                ]);
                return;
            }
        }

        $this->saveFormValues($_POST);
        $this->sendPage('suppliers/add', ['errors' => $errors]);
    }

    public function updatepage($id)
    {
        $supplier = (new Supplier(pdo()))->find($id);
        $this->sendPage('suppliers/update', [
            'supplier' => $supplier
        ]);
    }

    public function update()
    {
        $errors = [];

        if (!$this->checkCsrf()) {
            $errors['csrf'] = 'Lỗi CSRF, hãy kiểm tra và thử lại!';
        }

        $data = $this->filterSupplierData($_POST);
        $id = $data['id_supplier'] ?? null;

        $supplierModel = new Supplier(pdo());
        $existing = $supplierModel->find($id);

        if (!$existing) {
            $errors['not_found'] = 'Không tìm thấy nhà cung cấp cần cập nhật.';
        } else {
            $errors = $supplierModel->validate($data);
        }

        if (empty($errors)) {
            $supplier = new Supplier(pdo());
            $supplier->fill($data)->update();

            redirect('/suppliers/admin', ['success' => 'Cập nhật thành công cho ' . $supplier->id_supplier]);
        }

        $this->saveFormValues($_POST);
        $_SESSION['errors'] = $errors;
        redirect('/suppliers/update/' . $id, ['errors' => $errors]);
    }

    protected function filterSupplierData(array $data)
    {
        return [
            'id_supplier' => $data['id_supplier'] ?? '',
            'name' => $data['name'] ?? ''
        ];
    }

    protected function fillformsearch(array $data)
    {
        return [
            'id_supplier' => isset($data['id_supplier']) ? htmlspecialchars($data['id_supplier']) : '',
            'name' => isset($data['name']) ? htmlspecialchars($data['name']) : ''
        ];
    }

    public function search()
    {
        if (!$this->checkCsrf()) {
            $error = 'Lỗi CSRF, hãy kiểm tra và thử lại!';
            $this->index($error);
            exit();
        }

        $searchData = $this->fillformsearch($_POST);
        $supplierModel = new Supplier(pdo());

        if (!empty($searchData['id_supplier'])) {
            $supplier = $supplierModel->find($searchData['id_supplier']);
            $suppliers = $supplier ? [$supplier] : [];
        } elseif (!empty($searchData['name'])) {
            $suppliers = $supplierModel->where('name', $searchData['name']);
        } else {
            $error = "Bạn chưa nhập thông tin tìm kiếm.";
        }

        $this->sendPage('suppliers/index', [
            'suppliers' => $suppliers ?? [],
            'errors' => $error ?? null
        ]);
    }
}
