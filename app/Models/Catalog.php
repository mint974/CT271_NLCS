<?php

namespace App\Models;

use PDO;
use App\Models\Product;

class Catalog
{
    private PDO $db;

    public string $id_catalog;
    public string $name;
    public array $product_list = [];

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    // Tìm danh mục theo điều kiện
    public function where(string $column, string $value, $operator = "="): ?Catalog
    {
        $allowedColumns = ['id_catalog', 'name'];
        if (!in_array($column, $allowedColumns)) {
            throw new \Exception("Invalid column: " . htmlspecialchars($column));
        }

        $statement = $this->db->prepare("SELECT * FROM Product_Catalog WHERE $column $operator :value LIMIT 1");
        $statement->execute(['value' => $value]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $this->fill($row);
        }

        return null;
    }

    // Lưu danh mục vào database
    public function save(): bool
    {
        if (!empty($this->id_catalog)) {
            $statement = $this->db->prepare(
                'UPDATE Product_Catalog SET name = :name WHERE id_catalog = :id_catalog'
            );
            return $statement->execute([
                'id_catalog' => $this->id_catalog,
                'name' => $this->name
            ]);
        } else {
            $statement = $this->db->prepare(
                'INSERT INTO Product_Catalog (id_catalog, name) VALUES (:id_catalog, :name)'
            );
            $this->id_catalog = uniqid("CAT_");
            return $statement->execute([
                'id_catalog' => $this->id_catalog,
                'name' => $this->name
            ]);
        }
    }

    public function fill(array $data): Catalog
    {
        $this->id_catalog = $data['id_catalog'] ?? uniqid("CAT_");
        $this->name = $data['name'] ?? '';

        $product = new Product(pdo());
        $this->product_list = $product->getByCatalogId($this->id_catalog);

        return $this;
    }

  
    private function fillFromDbRow(array $row): Catalog
    {
        $this->id_catalog = $row['id_catalog'];
        $this->name = $row['name'] ?? '';

        $product = new Product(pdo());
        $this->product_list = $product->getByCatalogId($this->id_catalog);

        return $this;
    }

    public function getAllCatalog(): array
    {
        $productModel = new Product(pdo());
        $statement = $this->db->query('SELECT id_catalog, name FROM Product_Catalog');
        $catalogs = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($catalogs as &$catalog) {
            $catalog['product_list'] = $productModel->getByCatalogId($catalog['id_catalog']);
        }

        return $catalogs;
    }

    public function getCatalogByIdProduct(string $id_product): array
    {
        $sql = "SELECT pc.* 
                FROM product_catalog pc 
                JOIN product_catalog_details pcd ON pc.id_catalog = pcd.id_catalog 
                WHERE pcd.id_product = :id_product";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_product' => $id_product]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $results = [];

        foreach ($rows as $row) {
            $results[] = (new Catalog(pdo()))->fillFromDbRow($row);
        }

        return $results;
    }
}
