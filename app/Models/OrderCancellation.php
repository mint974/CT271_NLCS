<?php

namespace App\Models;

use PDO;

class OrderCancellation
{
    private PDO $db;
    public string $id_cancel;
    public string $id_order;
    public string $reason;
    public int $canceled_by;
    public string $canceled_at;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function where(string $column, string $value): ?OrderCancellation
    {
        $allowedColumns = ['id_cancel', 'id_order'];
        if (!in_array($column, $allowedColumns)) {
            throw new \Exception("Invalid column: " . htmlspecialchars($column));
        }

        $statement = $this->db->prepare("SELECT * FROM Order_Cancellations WHERE $column = :value LIMIT 1");
        $statement->execute(['value' => $value]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->fillFromDbRow($row) : null;
    }

    public function save(): bool
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $this->canceled_at = date('Y-m-d H:i:s');
        $this->canceled_by = AUTHGUARD()->user()->id_account;
        if (!empty($this->id_cancel)) {
            // Cập nhật lý don hủy đơ hàng
            $statement = $this->db->prepare(
                'UPDATE Order_Cancellations 
                 SET id_order = :id_order, reason = :reason, canceled_by = :canceled_by, canceled_at = :canceled_at 
                 WHERE id_cancel = :id_cancel'
            );
            return $statement->execute([
                'id_cancel' => $this->id_cancel,
                'id_order' => $this->id_order,
                'reason' => $this->reason,
                'canceled_by' => $this->canceled_by,
                'canceled_at' => $this->canceled_at
            ]);
        } else {
            // Thêm mới lý do hủy đơn hàng
            $statement = $this->db->prepare(
                'INSERT INTO Order_Cancellations (id_order, reason, canceled_by, canceled_at) 
                 VALUES (:id_order, :reason, :canceled_by, :canceled_at)'
            );
            return $statement->execute([
                'id_order' => $this->id_order,
                'reason' => $this->reason,
                'canceled_by' => $this->canceled_by,
                'canceled_at' => $this->canceled_at
            ]);
        }
    }

    public function fill(array $data): OrderCancellation
    {
        $this->id_order = $data['id_order'];
        $this->reason = $data['reason'];
        $this->canceled_by = $data['canceled_by'];
        if (!empty($data['id_cancel'])) {
            $this->id_cancel = $data['id_cancel'];
        }
        if (!empty($data['canceled_at'])) {
            $this->canceled_at = $data['canceled_at'];
        }
        return $this;
    }

    private function fillFromDbRow(array $row): OrderCancellation
    {
        $this->id_cancel = $row['id_cancel'];
        $this->id_order = $row['id_order'];
        $this->reason = $row['reason'];
        $this->canceled_by = $row['canceled_by'];
        $this->canceled_at = $row['canceled_at'];
        return $this;
    }

    public function getAllCancellations(): array
    {
        $statement = $this->db->prepare("SELECT * FROM Order_Cancellations ORDER BY canceled_at DESC");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
