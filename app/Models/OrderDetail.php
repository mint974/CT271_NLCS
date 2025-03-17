<?php

namespace App\Models;

use PDO;

class OrderDetail
{
    private PDO $db;

    public string $id_order;
    public string $id_product;
    public int $quantity;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function save(): bool
    {
        $statement = $this->db->prepare(
            'INSERT INTO order_details (id_order, id_product, quantity)
             VALUES (:id_order, :id_product, :quantity)
             ON DUPLICATE KEY UPDATE quantity = :quantity'
        );
        return $statement->execute([
            'id_order' => $this->id_order,
            'id_product' => $this->id_product,
            'quantity' => $this->quantity
        ]);
    }

    public function where(string $column, string $value): ?OrderDetail
    {
        $allowedColumns = ['id_order', 'id_product'];
        if (!in_array($column, $allowedColumns)) {
            throw new \Exception("Invalid column: " . htmlspecialchars($column));
        }

        $statement = $this->db->prepare("SELECT * FROM order_details WHERE $column = :value LIMIT 1");
        $statement->execute(['value' => $value]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row ? (new self($this->db))->fillFromDbRow($row) : null;
    }

    public function delete(String $id_product): bool
    {
        $id_account = AUTHGUARD()->user()->id_account;
        $id_order = sprintf('REORD%05d', $id_account);
        $statement = $this->db->prepare(
            'DELETE FROM order_details WHERE id_order = :id_order AND id_product = :id_product'
        );
        return $statement->execute(['id_order' => $id_order, 'id_product' => $id_product]);
    }

    public function fill(array $data): OrderDetail
    {
        $this->id_order = $data['id_order'] ?? '';
        $this->id_product = $data['id_product'] ?? '';
        $this->quantity = $data['quantity'] ?? 1;
        return $this;
    }

    private function fillFromDbRow(array $row): OrderDetail
    {
        $this->id_order = $row['id_order'] ?? '';
        $this->id_product = $row['id_product'] ?? '';
        $this->quantity = $row['quantity'] ?? 0;
        return $this;
    }

    public function addProduct(string $id_product, int $quantity): bool
    {
        $id_account = AUTHGUARD()->user()->id_account;
        $id_order = sprintf('REORD%05d', $id_account);

        $orderdetail = $this->findByOrderAndProduct($id_order, $id_product);

        if (!empty($orderdetail)) {
            $statement = $this->db->prepare(
                'UPDATE order_details 
                 SET quantity = quantity +  :quantity 
                 WHERE id_order = :id_order AND id_product = :id_product'
            );
        } else {
            $statement = $this->db->prepare(
                'INSERT INTO order_details (id_order, id_product, quantity)
                 VALUES (:id_order, :id_product, :quantity)'
            );
        }

        return $statement->execute([
            'quantity' => $quantity,
            'id_order' => $id_order,
            'id_product' => $id_product
        ]);
    }

    public function updateProduct(string $id_product, int $quantity): bool
    {
        $id_account = AUTHGUARD()->user()->id_account;
        $id_order = sprintf('REORD%05d', $id_account);

        $statement = $this->db->prepare(
            'UPDATE order_details 
                 SET quantity = :quantity 
                 WHERE id_order = :id_order AND id_product = :id_product'
        );
        return $statement->execute([
            'quantity' => $quantity,
            'id_order' => $id_order,
            'id_product' => $id_product
        ]);
    }


    public function getTotalQuantity(string $id_order): int
    {
        $statement = $this->db->prepare(
            "SELECT SUM(quantity) AS total_quantity FROM order_details WHERE id_order = :id_order"
        );
        $statement->execute(['id_order' => $id_order]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result['total_quantity'] ?? 0;
    }

    public function getAllOrderDetails(string $id_order): array
    {
        $statement = $this->db->prepare("SELECT * FROM order_details WHERE id_order = :id_order");
        $statement->execute(['id_order' => $id_order]);
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $orderDetails = [];
        foreach ($rows as $row) {
            $orderDetails[] = (new self($this->db))->fillFromDbRow($row);
        }

        return $orderDetails;
    }

    public function findByOrderAndProduct(string $id_order, string $id_product): ?OrderDetail
    {
        $statement = $this->db->prepare(
            'SELECT * FROM order_details WHERE id_order = :id_order AND id_product = :id_product LIMIT 1'
        );
        $statement->execute([
            'id_order' => $id_order,
            'id_product' => $id_product
        ]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row ? (new self($this->db))->fillFromDbRow($row) : null;
    }

}