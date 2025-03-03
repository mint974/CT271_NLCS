<?php

namespace App\Models;

use PDO;
use App\Models\Product;
class Catalog
{
    private PDO $db;

    public string $id_catalog;
    public string $name;
    public array $product_list = []; // Chứa danh sách sản phẩm của danh mục

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    // Tìm danh mục theo điều kiện
    public function where(string $column, string $value): Catalog
    {
        $allowedColumns = ['id_catalog', 'name'];
        if (!in_array($column, $allowedColumns)) {
            throw new \Exception("Invalid column: " . htmlspecialchars($column));
        }

        $statement = $this->db->prepare("SELECT * FROM Product_Catalog WHERE $column = :value LIMIT 1");
        $statement->execute(['value' => $value]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->fill($row);
            return $this;
        }

        return null; 
    }

    // Lưu danh mục vào database
    public function save(): bool
    {
        if (!empty($this->id_catalog)) {
            // Cập nhật danh mục nếu đã tồn tại
            $statement = $this->db->prepare(
                'UPDATE Product_Catalog 
                 SET name = :name 
                 WHERE id_catalog = :id_catalog'
            );
            return $statement->execute([
                'id_catalog' => $this->id_catalog,
                'name' => $this->name
            ]);
        } else {
            // Thêm mới danh mục
            $statement = $this->db->prepare(
                'INSERT INTO Product_Catalog (id_catalog, name) 
                 VALUES (:id_catalog, :name)'
            );
            $this->id_catalog = uniqid("CAT_"); // Tạo ID tự động
            return $statement->execute([
                'id_catalog' => $this->id_catalog,
                'name' => $this->name
            ]);
        }
    }

    // Gán dữ liệu từ mảng vào đối tượng Catalog
    public function fill(array $data): Catalog
    {
        $this->id_catalog = $data['id_catalog'] ?? uniqid("CAT_");
        $this->name = $data['name'];
        $product_list = new Product(PDO());
        // Lấy  danh sách sảnphẩm của danh mục này
        $this->product_list =  $product_list->getByCatalogId($this->id_catalog);

        return $this;
    }

    // Lấy danh sách sản phẩm theo ID danh mục
    public function getAll(): array
    {
        $statement = $this->db->query('SELECT * FROM Product_Catalog');
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllCatalog(): array
    {
        // Tạo đối tượng Product để sử dụng phương thức getByCatalogId()
        $productModel = new Product($this->db);

        // Lấy tất cả danh mục
        $statement = $this->db->query('SELECT id_catalog, name FROM Product_Catalog');
        $catalogs = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Duyệt qua từng danh mục để lấy danh sách sản phẩm
        foreach ($catalogs as &$catalog) {
            $catalog['product_list'] = $productModel->getByCatalogId($catalog['id_catalog']);
        }

        return $catalogs;
    }


}
