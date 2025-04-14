<?php

namespace App\Models;

use PDO;
use Exception;

class Supplier
{
    private PDO $db;

    public string $id_supplier;
    public string $name;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function where(string $column, string $value): Supplier
    {
        $allowedColumns = ['id_supplier', 'name'];
        if (!in_array($column, $allowedColumns)) {
            throw new Exception("Invalid column: " . htmlspecialchars($column));
        }

        $statement = $this->db->prepare("SELECT * FROM suppliers WHERE $column = :value LIMIT 1");
        $statement->execute(['value' => $value]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->fillFromDbRow($row);
        }

        return $this;
    }

    public function save(): bool
    {
        if (!empty($this->id_supplier)) {
            // Update
            $statement = $this->db->prepare(
                'UPDATE suppliers 
                 SET name = :name
                 WHERE id_supplier = :id_supplier'
            );
            return $statement->execute([
                'id_supplier' => $this->id_supplier,
                'name'        => $this->name
                
            ]);
        } else {
            // Insert
            $statement = $this->db->prepare(
                'INSERT INTO suppliers (name) 
                 VALUES (:name)'
            );
            $result = $statement->execute([
                'name'    => $this->name
               
            ]);

            if ($result) {
                $this->id_supplier = $this->db->lastInsertId();
            }

            return $result;
        }
    }

    public function fill(array $data): Supplier
    {
        if (!empty($data['id_supplier'])) {
            $this->id_supplier = $data['id_supplier'];
        }
        if (!empty($data['name'])) {
            $this->name = $data['name'];
        }

        return $this;
    }

    private function fillFromDbRow(array $row): void
    {
        $this->id_supplier = $row['id_supplier'];
        $this->name        = $row['name'];
    }

    public function getAll(): array
    {
        $statement = $this->db->prepare("SELECT * FROM suppliers");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['name']) || strlen($data['name']) < 2) {
            $errors['name'] = 'Tên nhà cung cấp phải có ít nhất 2 ký tự.';
        }
        return $errors;
    }
}
