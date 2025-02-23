<?php

namespace App\Models;

use PDO;

class Product
{
    private PDO $db;

    public int $id_product = -1;
    public string $name;
    public ?string $description = null;
    public int $quantity = 0;
    public float $price;
    public int $delivery_limit;
    public ?string $unit = null;
    public ?int $id_promotion = null;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function where(string $column, string $value): Product
    {
        $allowedColumns = ['id_product', 'name', 'id_promotion'];
        if (!in_array($column, $allowedColumns)) {
            throw new \Exception("Invalid column: " . htmlspecialchars($column));
        }

        $statement = $this->db->prepare("SELECT * FROM Products WHERE $column = :value LIMIT 1");
        $statement->execute(['value' => $value]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->fillFromDbRow($row);
        }
        return $this;
    }

    public function save(): bool
    {
        if ($this->id_product >= 0) {
            $statement = $this->db->prepare(
                'UPDATE Products 
                 SET name = :name, description = :description, quantity = :quantity, 
                     price = :price, delivery_limit = :delivery_limit, unit = :unit, id_promotion = :id_promotion 
                 WHERE id_product = :id_product'
            );
            return $statement->execute([
                'id_product' => $this->id_product,
                'name' => $this->name,
                'description' => $this->description,
                'quantity' => $this->quantity,
                'price' => $this->price,
                'delivery_limit' => $this->delivery_limit,
                'unit' => $this->unit,
                'id_promotion' => $this->id_promotion
            ]);
        } else {
            $statement = $this->db->prepare(
                'INSERT INTO Products (name, description, quantity, price, delivery_limit, unit, id_promotion) 
                 VALUES (:name, :description, :quantity, :price, :delivery_limit, :unit, :id_promotion)'
            );
            $result = $statement->execute([
                'name' => $this->name,
                'description' => $this->description,
                'quantity' => $this->quantity,
                'price' => $this->price,
                'delivery_limit' => $this->delivery_limit,
                'unit' => $this->unit,
                'id_promotion' => $this->id_promotion
            ]);
            if ($result) {
                $this->id_product = $this->db->lastInsertId();
            }
            return $result;
        }
    }

    public function fill(array $data): Product
    {
        $this->name = $data['name'];
        $this->description = $data['description'] ?? null;
        $this->quantity = $data['quantity'];
        $this->price = $data['price'];
        $this->delivery_limit = $data['delivery_limit'];
        $this->unit = $data['unit'] ?? null;
        $this->id_promotion = $data['id_promotion'] ?? null;

        return $this;
    }

    private function fillFromDbRow(array $row)
    {
        $this->id_product = $row['id_product'];
        $this->name = $row['name'];
        $this->description = $row['description'];
        $this->quantity = $row['quantity'];
        $this->price = $row['price'];
        $this->delivery_limit = $row['delivery_limit'];
        $this->unit = $row['unit'];
        $this->id_promotion = $row['id_promotion'];
    }
}
