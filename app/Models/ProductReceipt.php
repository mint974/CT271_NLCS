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


    public function save(): ?ProductReceipt
{
    $createdAt = date('Y-m-d H:i:s'); 
    $this->created_at = $createdAt;

    $statement = $this->db->prepare(
        'INSERT INTO Product_receipt (id_supplier, id_account, created_at)
         VALUES (:id_supplier, :id_account, :created_at)'
    );

    $success = $statement->execute([
        'id_supplier' => $this->id_supplier,
        'id_account'  => $this->id_account,
        'created_at'  => $createdAt
    ]);

    if ($success) {
        // Truy vấn lại bản ghi vừa chèn để lấy id_receipt (dựa vào 3 giá trị độc nhất)
        $stmt = $this->db->prepare(
            'SELECT * FROM Product_receipt
             WHERE id_supplier = :id_supplier
             AND id_account = :id_account
             AND created_at = :created_at
             ORDER BY id_receipt DESC LIMIT 1'
        );

        $stmt->execute([
            'id_supplier' => $this->id_supplier,
            'id_account'  => $this->id_account,
            'created_at'  => $createdAt
        ]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            return (new ProductReceipt($this->db))->fillFromDbRow($data);
        }
    }

    return null;
}



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

        $receipts = new ProductReceipt(pdo());

        $receipts->id_receipt = $row['id_receipt'];
        $receipts->created_at = $row['created_at'];
        $receipts->id_supplier = $row['id_supplier'];
        $receipts->id_account = (int) $row['id_account'];
        return $receipts;
    }

    public function getAll(): array
    {
        $statement = $this->db->prepare(
            'SELECT * FROM Product_receipt ORDER BY created_at DESC'
        );
        $statement->execute();

        $receipts = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $receipt = new self($this->db);
            $receipts[] = $receipt->fillFromDbRow($row);
        }
        return $receipts;
    }

    public function getByTime($time): array
    {
        // Ép về định dạng ngày Y-m-d để so sánh không kèm giờ
        $dateOnly = date('Y-m-d', strtotime($time));

        $statement = $this->db->prepare(
            'SELECT * FROM Product_receipt WHERE DATE(created_at) = :date ORDER BY created_at DESC'
        );
        $statement->execute([
            'date' => $dateOnly
        ]);

        $receipts = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $receipt = new self($this->db);
            $receipts[] = $receipt->fillFromDbRow($row);
        }

        return $receipts;
    }

    public function getByIdSuplider($id): array
    {

        $statement = $this->db->prepare(
            'SELECT * FROM Product_receipt WHERE id_supplier = :id ORDER BY created_at DESC'
        );
        $statement->execute([
            'id' => $id
        ]);

        $receipts = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $receipt = new self($this->db);
            $receipts[] = $receipt->fillFromDbRow($row);
        }

        return $receipts;
    }



}
