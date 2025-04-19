<?php

namespace App\Models;

use PDO;

class Supplier
{
    private PDO $db;

    public string $id_supplier;
    public string $name;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function find(string $id): ?Supplier
    {
        $statement = $this->db->prepare('SELECT * FROM Suppliers WHERE id_supplier = :id');
        $statement->execute(['id' => $id]);

        if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            return $this->fillFromDbRow($row);
        }

        return null;
    }

    public function save(): bool
    {
        $statement = $this->db->prepare(
            'INSERT INTO Suppliers (name) VALUES (:name)'
        );
        $success = $statement->execute([
            'name' => $this->name
        ]);

        // Lấy lại ID mới được sinh nếu cần sử dụng tiếp
        if ($success) {
            $result = $this->db->query('SELECT id_supplier FROM Suppliers ORDER BY CAST(SUBSTRING(id_supplier, 4) AS UNSIGNED) DESC LIMIT 1');
            $this->id_supplier = $result->fetchColumn();
        }

        return $success;
    }

    public function update(): bool
    {
        $statement = $this->db->prepare(
            'UPDATE Suppliers SET name = :name WHERE id_supplier = :id_supplier'
        );
        return $statement->execute([
            'id_supplier' => $this->id_supplier,
            'name' => $this->name
        ]);
    }

    public function delete(): bool
    {
        $statement = $this->db->prepare(
            'DELETE FROM Suppliers WHERE id_supplier = :id_supplier'
        );
        return $statement->execute(['id_supplier' => $this->id_supplier]);
    }

    public function fill(array $data): Supplier
    {
        $this->id_supplier = $data['id_supplier'] ?? $this->id_supplier;
        $this->name = $data['name'] ?? $this->name;
        return $this;
    }

    private function fillFromDbRow(array $row): Supplier
    {
        [
            'id_supplier' => $this->id_supplier,
            'name' => $this->name
        ] = $row;
        return $this;
    }

    public function getAll(): array
    {
        $statement = $this->db->prepare('SELECT * FROM Suppliers');
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($rows as $row) {
            $supplier = new Supplier(pdo());
            $result[] = $supplier->fillFromDbRow($row);
        }

        return $result;
    }

    public function where(string $column, string $value): array
    {
        $allowedColumns = ['id_supplier', 'name'];
        if (!in_array($column, $allowedColumns)) {
            throw new \Exception("Invalid column: " . htmlspecialchars($column));
        }

        $value = trim($value);
        if ($value === '') {
            return [];
        }

        $query = "SELECT * FROM Suppliers WHERE $column LIKE :value";
        $statement = $this->db->prepare($query);
        $statement->execute(['value' => '%' . $value . '%']);

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $suppliers = [];

        // dd($rows);
        foreach ($rows as $row) {
            $supplier = new Supplier($this->db);
            $suppliers[] = $supplier->fillFromDbRow($row);
        }

        // dd($supplier);
        return $suppliers;
    }

    public function whereSup(string $column, string $value): ?Supplier
{
    $allowedColumns = ['id_supplier', 'name'];
    if (!in_array($column, $allowedColumns)) {
        throw new \Exception("Invalid column: " . htmlspecialchars($column));
    }

    $value = trim($value);
    if ($value === '') {
        return null;
    }

    $query = "SELECT * FROM Suppliers WHERE $column = :value LIMIT 1";
    $statement = $this->db->prepare($query);
    $statement->execute(['value' => $value]);

    $row = $statement->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $supplier = new Supplier($this->db);
        return $supplier->fillFromDbRow($row);
    }

    return null;
}

    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['name']) || strlen(trim($data['name'])) < 3) {
            $errors['name'] = 'Tên nhà cung cấp phải có ít nhất 3 ký tự.';
        }

        return $errors;
    }
}
