<?php

namespace App\Models;

use PDO;

class ProductReceipt
{
    private PDO $db;

    public string $id_receipt;
    public string $created_at;
    public string $id_supplier;
    public int $id_account;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function where(string $column, string $value): ?ProductReceipt
{
    $allowedColumns = ['id_receipt', 'id_supplier', 'id_account'];
    if (!in_array($column, $allowedColumns)) {
        throw new \Exception("Invalid column: " . htmlspecialchars($column));
    }

    $statement = $this->db->prepare("SELECT * FROM Product_receipt WHERE $column = :value LIMIT 1");
    $statement->execute(['value' => $value]);
    $row = $statement->fetch(PDO::FETCH_ASSOC);

    return $row ? $this->fillFromDbRow($row) : null;
}


    // public function save(): bool
    // {
    //     $result = false;

    //     if (!empty($this->id_receipt)) {
    //         $statement = $this->db->prepare(
    //             'UPDATE Product_receipt SET id_supplier = :id_supplier, id_account = :id_account 
    //              WHERE id_receipt = :id_receipt'
    //         );

    //         $result = $statement->execute([
    //             'id_supplier' => $this->id_supplier,
    //             'id_account' => $this->id_account,
    //             'id_receipt' => $this->id_receipt
    //         ]);
    //     } else {
    //         $statement = $this->db->prepare(
    //             'INSERT INTO Product_receipt (id_receipt, id_supplier, id_account, created_at)
    //              VALUES (:id_receipt, :id_supplier, :id_account, NOW())'
    //         );

    //         $result = $statement->execute([
    //             'id_receipt' => $this->id_receipt,
    //             'id_supplier' => $this->id_supplier,
    //             'id_account' => $this->id_account
    //         ]);
    //     }

    //     return $result;
    // }

    // public function find(string $id): ?ProductReceipt
    // {
    //     $statement = $this->db->prepare(
    //         'SELECT * FROM Product_receipt WHERE id_receipt = :id_receipt'
    //     );
    //     $statement->execute(['id_receipt' => $id]);

    //     if ($row = $statement->fetch()) {
    //         return $this->fillFromDbRow($row);
    //     }

    //     return null;
    // }

    // public function delete(): bool
    // {
    //     $statement = $this->db->prepare(
    //         'DELETE FROM Product_receipt WHERE id_receipt = :id_receipt'
    //     );
    //     return $statement->execute(['id_receipt' => $this->id_receipt]);
    // }

    // public function fill(array $data): ProductReceipt
    // {
    //     $this->id_receipt = $data['id_receipt'] ?? '';
    //     $this->id_supplier = $data['id_supplier'] ?? '';
    //     $this->id_account = $data['id_account'] ?? AUTHGUARD()->user()->id_account;
    //     return $this;
    // }

    // public function validate(array $data): array
    // {
    //     $errors = [];

    //     if (empty($data['id_supplier'])) {
    //         $errors['id_supplier'] = 'Vui lòng chọn nhà cung cấp.';
    //     }

    //     return $errors;
    // }

    private function fillFromDbRow(array $row): ProductReceipt
    {
        $this->id_receipt = $row['id_receipt'];
        $this->created_at = $row['created_at'];
        $this->id_supplier = $row['id_supplier'];
        $this->id_account = (int)$row['id_account'];
        return $this;
    }
}
