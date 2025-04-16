<?php

namespace App\Models;

use PDO;

class ProductReceiptDetails
{
    private PDO $db;

    public string $id_receipt;
    public string $id_product;
    public int $quantity;
    public float $purchase_price;

public float $selling_price;
    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function save(): bool
    {
        $statement = $this->db->prepare(
            'REPLACE INTO Product_receipt_details (id_receipt, id_product, quantity, purchase_price, selling_price)
             VALUES (:id_receipt, :id_product, :quantity, :purchase_price, :selling_price)'
        );

        return $statement->execute([
            'id_receipt' => $this->id_receipt,
            'id_product' => $this->id_product,
            'quantity' => $this->quantity,
            'purchase_price' => $this->purchase_price,
            'selling_price' => $this->selling_price
        ]);
    }

    public function find(string $id_receipt, string $id_product): ?ProductReceiptDetails
    {
        $statement = $this->db->prepare(
            'SELECT * FROM Product_receipt_details WHERE id_receipt = :id_receipt AND id_product = :id_product'
        );

        $statement->execute([
            'id_receipt' => $id_receipt,
            'id_product' => $id_product
        ]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->fillFromDbRow($row) : null;
    }

    public function where(string $column, string $value): array
    {
        $allowedColumns = ['id_receipt', 'id_product'];
        if (!in_array($column, $allowedColumns)) {
            throw new \Exception("Invalid column: " . htmlspecialchars($column));
        }

        $statement = $this->db->prepare("SELECT * FROM Product_receipt_details WHERE $column = :value");
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
            'DELETE FROM Product_receipt_details WHERE id_receipt = :id_receipt AND id_product = :id_product'
        );

        return $statement->execute([
            'id_receipt' => $this->id_receipt,
            'id_product' => $this->id_product
        ]);
    }

    public function fill(array $data): ProductReceiptDetails
    {
        $this->id_receipt = $data['id_receipt'] ?? '';
        $this->id_product = $data['id_product'] ?? '';
        $this->quantity = (int) ($data['quantity'] ?? 0);
        $this->purchase_price = (float) ($data['purchase_price'] ?? 0);
        $this->selling_price = (float) ($data['selling_price'] ?? 0);
        return $this;
    }

    private function fillFromDbRow(array $row): ProductReceiptDetails
    {
        $this->id_receipt = $row['id_receipt'];
        $this->id_product = $row['id_product'];
        $this->quantity = (int) $row['quantity'];
        $this->purchase_price = (float) $row['purchase_price'];
        $this->selling_price = (float) $row['selling_price'];
        return $this;
    }

    //tìm tất cả đơn hàng chứa sản phẩm
    public function FindAllReceiptDetailsByIdProduct($id_product)
    {

        $query = ' SELECT prd.* 
            FROM Product_receipt_details prd
            LEFT JOIN Product_receipt pr ON prd.id_receipt = pr.id_receipt
            WHERE prd.id_product = :id_product
            ORDER BY pr.created_at DESC';

        $statement = $this->db->prepare($query);
        $statement->execute([
            'id_product' => $id_product
        ]);

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $results = [];
        foreach ($rows as $row) {
            $results[] = (new self($this->db))->fillFromDbRow($row);
        }

        return $results;
    }
}
