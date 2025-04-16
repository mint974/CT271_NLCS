<?php

namespace App\Models;

use PDO;

class ImageProduct
{
    private PDO $db;

    public string $id_image;
    public string $URL_image;
    public string $id_product;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function save(): bool
    {
        $statement = $this->db->prepare(
            'INSERT INTO image_product (URL_image, id_product)
             VALUES (:URL_image, :id_product)'
        );
    
        $success = $statement->execute([
            'URL_image' => $this->URL_image,
            'id_product' => $this->id_product
        ]);
    
        if ($success) {
            // Lấy lại id_image được trigger tạo tự động
            $statement = $this->db->prepare(
                'SELECT id_image FROM image_product
                 WHERE URL_image = :URL_image AND id_product = :id_product
                 ORDER BY id_image DESC LIMIT 1'
            );
            $statement->execute([
                'URL_image' => $this->URL_image,
                'id_product' => $this->id_product
            ]);
    
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $this->id_image = $row['id_image'];
            }
        }
    
        return $success;
    }
    

    public function find(string $id_image): ?ImageProduct
    {
        $statement = $this->db->prepare(
            'SELECT * FROM image_product WHERE id_image = :id_image'
        );

        $statement->execute(['id_image' => $id_image]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->fillFromDbRow($row) : null;
    }

    public function where(string $column, string $value): array
    {
        $allowedColumns = ['id_image', 'id_product'];
        if (!in_array($column, $allowedColumns)) {
            throw new \Exception("Invalid column: " . htmlspecialchars($column));
        }

        $statement = $this->db->prepare("SELECT * FROM image_product WHERE $column = :value");
        $statement->execute(['value' => $value]);
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $results = [];
        foreach ($rows as $row) {
            $results[] = (new self($this->db))->fillFromDbRow($row);
        }

        return $results;
    }

    public function delete(): bool
    {
        $statement = $this->db->prepare(
            'DELETE FROM image_product WHERE id_image = :id_image'
        );

        return $statement->execute([
            'id_image' => $this->id_image
        ]);
    }

    public function fill(array $data): ImageProduct
    {
        $this->id_image = $data['id_image'] ?? '';
        $this->URL_image = $data['URL_image'] ?? '';
        $this->id_product = $data['id_product'] ?? '';
        return $this;
    }

    private function fillFromDbRow(array $row): ImageProduct
    {
        $this->id_image = $row['id_image'];
        $this->URL_image = $row['URL_image'];
        $this->id_product = $row['id_product'];
        return $this;
    }

    // Lấy tất cả ảnh của một sản phẩm
    public function getAllByProductId(string $id_product): array
    {
        $statement = $this->db->prepare(
            'SELECT * FROM image_product WHERE id_product = :id_product'
        );
        $statement->execute(['id_product' => $id_product]);

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $results = [];
        foreach ($rows as $row) {
            $results[] = (new ImageProduct(pdo()))->fillFromDbRow($row);
        }

        return $results;
    }
}
