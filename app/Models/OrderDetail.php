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

    //thêm vào từ trang chi tiết
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

    //sửa từ trang giỏ hàng
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


    public function getTotalQuantity(string $id_order, string $id_product = null): int
    {
        if (empty($id_product)){
            $statement = $this->db->prepare(
                "SELECT SUM(quantity) AS total_quantity FROM order_details WHERE id_order = :id_order"
            );
            $statement->execute(['id_order' => $id_order]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            return $result['total_quantity'] ?? 0;
        } else {
            $statement = $this->db->prepare(
                "SELECT quantity FROM order_details WHERE id_order = :id_order and id_product = :id_product"
            );
            $statement->execute([
                'id_order' => $id_order,
                'id_product' => $id_product
             ]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            return $result['quantity'] ?? 0;

        }
        
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

    //đặt hàng
    public function Orders(string $new_id_order, array $id_products): bool
    {
        try {
            // Lấy id_account của người dùng đang đăng nhập
            $id_account = AUTHGUARD()->user()->id_account;
            $id_order = sprintf('REORD%05d', $id_account); // Xác định id_order của user hiện tại
    
            // Tạo danh sách placeholders cho mệnh đề IN
            $placeholders = implode(',', array_fill(0, count($id_products), '?'));
    
            // Cập nhật id_order trong Order_details
            $stmtUpdateOrder = $this->db->prepare("
                UPDATE Order_details 
                SET id_order = ? 
                WHERE id_order = ? AND id_product IN ($placeholders)
            ");
            $updateOrderSuccess = $stmtUpdateOrder->execute(array_merge([$new_id_order, $id_order], $id_products));
    
            // Trừ số lượng sản phẩm trong bảng Products 
            $stmtUpdateProduct = $this->db->prepare("
                UPDATE Products p
                JOIN Order_details od ON p.id_product = od.id_product
                SET p.quantity = p.quantity - od.quantity
                WHERE od.id_order = ? AND p.id_product IN ($placeholders)
            ");
            $updateProductSuccess = $stmtUpdateProduct->execute(array_merge([$new_id_order], $id_products));
    
            return $updateOrderSuccess && $updateProductSuccess; // Trả về true nếu cả 2 thành công
        } catch (\Exception $e) {
            return false; // Trả về false nếu có lỗi xảy ra
        }
    }
    
    public function getTotalPrice(string $id_order): float
    {
        $statement = $this->db->prepare("
            SELECT 
                (SUM(od.quantity * 
                    CASE 
                        WHEN p.id_promotion IS NOT NULL 
                        AND pr.start_day <= CURDATE() 
                        AND pr.end_day >= CURDATE() 
                        THEN p.price * (1 - pr.discount_rate / 100) 
                        ELSE p.price 
                    END
                ) + d.shipping_fee) AS total_price
            FROM order_details od
            JOIN products p ON od.id_product = p.id_product
            LEFT JOIN promotions pr ON p.id_promotion = pr.id_promotion
            JOIN orders o ON od.id_order = o.id_order
            JOIN Delivery_Information d ON o.id_delivery = d.id_delivery
            WHERE od.id_order = :id_order
        ");
        
        $statement->execute(['id_order' => $id_order]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
    
        return $result['total_price'] ?? 0;
    }
    
    public function getAllProductsOrderDetails($id_order)
{
    $sql = "SELECT 
                od.id_order,
                od.id_product,
                od.quantity,
                p.name AS product_name,
                p.price AS unit_price,
                COALESCE(pr.discount_rate, 0) AS discount_rate,
                (p.price * (1 - COALESCE(pr.discount_rate, 0) / 100)) AS discount_price,
                (od.quantity * (p.price * (1 - COALESCE(pr.discount_rate, 0) / 100))) AS total_price,
                COALESCE(img.URL_image, 'default.jpg') AS image
            FROM Order_details od
            JOIN Products p ON od.id_product = p.id_product
            LEFT JOIN Promotions pr ON p.id_promotion = pr.id_promotion
            LEFT JOIN Image_Product img ON od.id_product = img.id_product
            WHERE od.id_order = :id_order
            GROUP BY od.id_product"; 
    
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['id_order' => $id_order]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



}