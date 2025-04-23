<?php

namespace App\Controllers;

use App\Models\Catalog;

class CatalogController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->checkroleadmin();
    }

    public function index($error = null, $success = null)
    {
        $this->sendPage('catalogs/index', [
            'catalogs' => (new Catalog(pdo()))->getAll(),
            'errors' => $error,
            'success' => $success
        ]);
    }

    public function storepage()
    {
        $this->sendPage('catalogs/add');
    }

    public function store()
    {
        $errors = [];
        if (!$this->checkCsrf()) {
            $errors['csrf'] = 'Lỗi CSRF, hãy kiểm tra và thử lại!';
        }

        $data = $this->filterCatalogData($_POST);
        $catalog = new Catalog(pdo());
        $errors = $catalog->validate($data);

        if (empty($errors)) {
            if (!$catalog->fill($data)->save()) {
                $errors['save'] = 'Không thể lưu danh mục.';
            } else {
                $success = 'Đã thêm danh mục thành công.';
                $this->sendPage('catalogs/index', [
                    'catalogs' => (new Catalog(pdo()))->getAll(),
                    'success' => $success
                ]);
                return;
            }
        }

        $this->saveFormValues($_POST);
        $this->sendPage('catalogs/add', ['errors' => $errors]);
    }

    public function updatepage($id)
    {
        $catalog = (new Catalog(pdo()))->find($id);
        $this->sendPage('catalogs/update', [
            'catalog' => $catalog
        ]);
    }

    public function update()
    {
        $errors = [];

        if (!$this->checkCsrf()) {
            $errors['csrf'] = 'Lỗi CSRF, hãy kiểm tra và thử lại!';
        }

        $data = $this->filterCatalogData($_POST);
        $id = $data['id_catalog'] ?? null;

        $catalogModel = new Catalog(pdo());
        $existingCatalog = $catalogModel->find($id);

        if (!$existingCatalog) {
            $errors['not_found'] = 'Không tìm thấy danh mục cần cập nhật.';
        } else {
            $errors = $catalogModel->validate($data);
        }

        if (empty($errors)) {
            $catalog = new Catalog(pdo());
            $catalog->fill($data);
            $catalog->update($data);

            redirect('/catalogs/admin', ['success' => 'Cập nhật thông tin cho ' . $catalog->id_catalog . ' thành công!']);
        }

        $this->saveFormValues($_POST);
        $_SESSION['errors'] = $errors;
        redirect('/catalogs/update/' . $id, ['errors' => $errors]);
    }

    public function search()
    {
        if (!$this->checkCsrf()) {
            $error = 'Lỗi CSRF, hãy kiểm tra và thử lại!';
            $this->index($error);
            exit();
        }

        $catalog = $this->fillformsearch($_POST);

        $catalogModel = new Catalog(pdo());
        if (!empty($catalog['id_catalog'])) {
            $catalogById = $catalogModel->find($catalog['id_catalog']);
            $catalogs = $catalogById ? [$catalogById] : [];
        } elseif (!empty($catalog['name'])) {
            $catalogs = $catalogModel->whereCat('name', $catalog['name']);
        } else {
            $error = "Không có dữ liệu tìm kiếm hợp lệ.";
        }

        $this->sendPage('catalogs/index', [
            'catalogs' => $catalogs ?? [],
            'errors' => $error ?? null
        ]);
    }

    protected function filterCatalogData(array $data): array
    {
        return [
            'id_catalog' => $data['id_catalog'] ?? '',
            'name' => $data['name'] ?? ''
        ];
    }

    public function fillformsearch(array $data): array
    {
        return [
            'id_catalog' => isset($data['id_catalog']) ? htmlspecialchars($data['id_catalog']) : '',
            'name' => isset($data['name']) ? htmlspecialchars($data['name']) : ''
        ];
    }
}
