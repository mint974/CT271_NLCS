<?php

namespace App\Models;

use PDO;

class Order
{
    private PDO $db;
    public string $id_order;
    public string $created_at;
    public int $id_account;
    public ?string $id_delivery; // Có thể NULL
    public string $status;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
        $this->status = 'Đã gửi đơn đặt hàng'; // Mặc định
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
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $this->created_at = date('Y-m-d H:i:s');

        if (!empty($this->id_order)) {
            // Cập nhật đơn hàng đã có
            $statement = $this->db->prepare(
                'UPDATE Orders 
             SET id_account = :id_account, id_delivery = :id_delivery, created_at = :created_at, status = :status
             WHERE id_order = :id_order'
            );
            return $statement->execute([
                'id_order' => $this->id_order,
                'id_account' => $this->id_account,
                'id_delivery' => $this->id_delivery,
                'created_at' => $this->created_at,
                'status' => $this->status
            ]);
        } else {
            // Chèn đơn hàng mới (id_order sẽ được trigger tạo tự động)
            $statement = $this->db->prepare(
                'INSERT INTO Orders (id_account, id_delivery, created_at, status) 
             VALUES (:id_account, :id_delivery, :created_at, :status)'
            );
            $success = $statement->execute([
                'id_account' => $this->id_account,
                'id_delivery' => $this->id_delivery,
                'created_at' => $this->created_at,
                'status' => $this->status
            ]);

            if ($success) {
                // Lấy id_order mới nhất của tài khoản này từ DB
                $query = $this->db->prepare(
                    'SELECT id_order FROM Orders 
                 WHERE id_account = :id_account 
                 ORDER BY created_at DESC 
                 LIMIT 1'
                );
                $query->execute(['id_account' => $this->id_account]);
                $this->id_order = $query->fetchColumn();
            }

            return $success;
        }
    }


    public function save_def(): bool
    {
        $this->created_at = date('Y-m-d H:i:s');

        $statement = $this->db->prepare(
            'INSERT INTO Orders ( id_account, created_at) 
                  VALUES ( :id_account, :created_at)'
        );
        return $statement->execute([
           
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
        if (!empty($data['id_delivery'])) {
            $this->id_delivery = $data['id_delivery'];
        }
        if (!empty($data['status'])) {
            $this->status = $data['status'];
        }
        return $this;
    }

    private function fillFromDbRow(array $row): Order
    {
        $this->id_order = $row['id_order'];
        $this->created_at = $row['created_at'];
        $this->id_account = $row['id_account'];
        $this->id_delivery = $row['id_delivery'];
        $this->status = $row['status'];
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

    // Lấy tất cả đơn hàng theo danh sách id_account
    public function getUserOrders(int $id_account): array
    {
        $statement = $this->db->prepare("
            SELECT * FROM Orders 
            WHERE id_account = :id_account 
            AND id_order NOT LIKE 'REORD%' 
            ORDER BY created_at DESC
        ");
        $statement->execute(['id_account' => $id_account]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    //  Lấy tất cả chi tiết đơn hàng theo danh sách id_order

    public function getOrderDetails(array $orderIds): array
    {
        if (empty($orderIds)) {
            return [];
        }

        // Tạo danh sách placeholders (?, ?, ?) để truyền vào truy vấn
        $placeholders = implode(',', array_fill(0, count($orderIds), '?'));

        $statement = $this->db->prepare("
            SELECT * FROM Order_details 
            WHERE id_order IN ($placeholders)
        ");
        $statement->execute($orderIds);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchOrders(int $id_account, ?string $date, ?string $totalRange, ?string $status): array 
    {
        $query = "
            SELECT 
                o.*, 
                (
                    SELECT SUM(od.price * (1 - (od.discount_rate / 100)) * od.quantity)
                    FROM order_details od
                    WHERE od.id_order = o.id_order
                ) + COALESCE(d.shipping_fee, 0) AS total_price
            FROM orders o
            LEFT JOIN delivery_information d ON o.id_delivery = d.id_delivery
            WHERE o.id_account = :id_account
            AND o.id_order NOT LIKE '%REORD%'
        ";
    
        $params = ['id_account' => $id_account];
    
        // Lọc theo ngày
        if (!empty($date)) {
            $query .= " AND DATE(o.created_at) = :date";
            $params['date'] = $date;
        }
    
        // Lọc theo trạng thái
        if (!empty($status)) {
            $query .= " AND o.status = :status";
            $params['status'] = $status;
        }
    
        // Lọc theo khoảng tổng tiền nếu có
        if (!empty($totalRange)) {
            $query = "SELECT * FROM ($query) AS filtered_orders WHERE 1=1";
    
            if ($totalRange === 'under_300') {
                $query .= " AND total_price < 300000";
            } elseif ($totalRange === 'between_300_800') {
                $query .= " AND total_price BETWEEN 300000 AND 800000";
            } elseif ($totalRange === 'above_800') {
                $query .= " AND total_price > 800000";
            }
        }
    
        $statement = $this->db->prepare($query);
        $statement->execute($params);
    
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    


}
