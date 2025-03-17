<?php

namespace App\Models;

use PDO;

class Order
{
    private PDO $db;
    public string $id_order;
    public string $created_at;
    public int $id_account;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    private function generateOrderId(): string
    {
        $stmt = $this->db->query("SELECT MAX(id_order) FROM Orders");
        $lastId = $stmt->fetchColumn();

        if (!$lastId) {
            return "ORD001";
        }

        $lastNumber = (int) substr($lastId, 3);
        $newNumber = $lastNumber + 1;
        
        return sprintf("ORD%03d", $newNumber);
    }

    public function where(string $column, string $value): ?Order
    {
        $allowedColumns = ['id_order', 'id_account'];
        if (!in_array($column, $allowedColumns)) {
            throw new \Exception("Invalid column: " . htmlspecialchars($column));
        }

        $statement = $this->db->prepare("SELECT * FROM Orders WHERE $column = :value LIMIT 1");
        $statement->execute(['value' => $value]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->fillFromDbRow($row) : null;
    }

    public function save(): bool
    {
        $this->created_at = date('Y-m-d H:i:s');

        if (!empty($this->id_order)) {
            $statement = $this->db->prepare(
                'UPDATE Orders 
                 SET id_account = :id_account, created_at = :created_at
                 WHERE id_order = :id_order'
            );
        } else {
            $this->id_order = $this->generateOrderId();
            $statement = $this->db->prepare(
                'INSERT INTO Orders (id_order, id_account, created_at) 
                 VALUES (:id_order, :id_account, :created_at)'
            );
        }

        return $statement->execute([
            'id_order' => $this->id_order,
            'id_account' => $this->id_account,
            'created_at' => $this->created_at
        ]);
    }
    public function save_def(): bool
    {
        $this->created_at = date('Y-m-d H:i:s');

            $statement = $this->db->prepare(
                'INSERT INTO Orders (id_order, id_account, created_at) 
                 VALUES (:id_order, :id_account, :created_at)'
            );
        return $statement->execute([
            'id_order' => $this->id_order,
            'id_account' => $this->id_account,
            'created_at' => $this->created_at
        ]);
    }


    public function fill(array $data): Order
    {
        $this->id_account = $data['id_account'];
        if (!empty($data['id_order'])) {
            $this->id_order = $data['id_order'];
        }
        return $this;
    }

    private function fillFromDbRow(array $row): Order
    {
        $this->id_order = $row['id_order'];
        $this->created_at = $row['created_at'];
        $this->id_account = $row['id_account'];
        return $this;
    }

    public function validate(array $data): array
    {
        $errors = [];
        if (empty($data['id_account']) || !is_numeric($data['id_account'])) {
            $errors['id_account'] = 'ID tài khoản không hợp lệ.';
        }
        return $errors;
    }
}