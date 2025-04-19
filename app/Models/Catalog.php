<?php 

namespace App\Models;

use PDO;
use App\Models\Product;

class Catalog
{
    private PDO $db;

    public string $id_catalog;
    public string $name;

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

    public function whereCat(string $column, string $value): array
{
    $allowedColumns = ['id_catalog', 'name'];
    if (!in_array($column, $allowedColumns)) {
        throw new \Exception("Invalid column: " . htmlspecialchars($column));
    }

    $statement = $this->db->prepare("SELECT * FROM Product_Catalog WHERE $column LIKE :value");
    $statement->execute(['value' => '%' . $value . '%']);
    $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

    $result = [];
    foreach ($rows as $row) {
        $catalog = new Catalog($this->db);
        $result[] = $catalog->fill($row);
    }

    return $result;
}

    // Lưu danh mục vào database
    public function save(): bool
    {

            $statement = $this->db->prepare(
                'INSERT INTO Product_Catalog (id_catalog, name) VALUES (:id_catalog, :name)'
            );
           
            return $statement->execute([
                'id_catalog' => $this->id_catalog,
                'name' => $this->name
            ]);
        
    }

    public function fill(array $data): Catalog
    {
        $this->id_catalog = $data['id_catalog'] ?? '';
        $this->name = $data['name'] ?? '';
        return $this;
    }

    private function fillFromDbRow(array $row): Catalog
    {
        $this->id_catalog = $row['id_catalog'];
        $this->name = $row['name'] ?? '';
        return $this;
    }

    public function getAllCatalog(): array
    {
        $statement = $this->db->query('SELECT id_catalog, name FROM Product_Catalog');
        $catalogs = $statement->fetchAll(PDO::FETCH_ASSOC);

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

    public function find(string $id): ?Catalog
    {
        $stmt = $this->db->prepare("SELECT * FROM Product_Catalog WHERE id_catalog = :id_catalog LIMIT 1");
        $stmt->execute(['id_catalog' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $this->fill($row);
        }

        return null;
    }

    public function delete(string $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM Product_Catalog WHERE id_catalog = :id_catalog");
        return $stmt->execute(['id_catalog' => $id]);
    }

    public function update(array $data): bool
    {
        $stmt = $this->db->prepare("UPDATE Product_Catalog SET name = :name WHERE id_catalog = :id_catalog");
        return $stmt->execute([
            'name' => $data['name'],
            'id_catalog' => $data['id_catalog']
        ]);
    }

    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors[] = "Tên danh mục không được để trống.";
        }

        return $errors;
    }

    public function getAll(): array
{
    $statement = $this->db->prepare('SELECT * FROM Product_Catalog');
    $statement->execute();
    $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

    $result = [];
    foreach ($rows as $row) {
        $catalog = new Catalog(pdo());
        $result[] = $catalog->fillFromDbRow($row);
    }

    return $result;
}

}
