<?php

namespace App\Models;

use PDO;

class Product
{
    private PDO $db;

    public string $id_product;
    public string $name;
    public ?string $description = null;
    public int $quantity = 0;
    public float $price;

    public ?string $unit = null;
    public ?string $id_promotion = null;
    public array $images = []; // Khai báo trước, tránh lỗi deprecated
    public ?array $promotion = null;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function where(string $column, string $value): Product
    {
        $allowedColumns = ['id_product', 'name', 'id_promotion'];
        if (!in_array($column, $allowedColumns)) {
            throw new \Exception("Invalid column: " . htmlspecialchars($column));
        }

        $query = "
            SELECT p.*, 
                   GROUP_CONCAT(ip.URL_image) AS images, 
                   pr.name AS promotion_name, pr.description AS promotion_description, 
                   pr.start_day, pr.end_day, pr.discount_rate
            FROM Products p
            LEFT JOIN Image_Product ip ON p.id_product = ip.id_product
            LEFT JOIN Promotions pr ON p.id_promotion = pr.id_promotion
            WHERE p.$column = :value 
            LIMIT 1
        ";

        $statement = $this->db->prepare($query);
        $statement->execute(['value' => $value]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->fillFromDbRow($row);
        }
        return $this;
    }


    public function save(): bool
    {
        if (!empty($this->id_product)) {
            $statement = $this->db->prepare(
                'UPDATE Products 
                 SET name = :name, description = :description, quantity = :quantity, 
                     price = :price, unit = :unit, id_promotion = :id_promotion 
                 WHERE id_product = :id_product'
            );
            return $statement->execute([
                'id_product' => $this->id_product,
                'name' => $this->name,
                'description' => $this->description,
                'quantity' => $this->quantity,
                'price' => $this->price,
                
                'unit' => $this->unit,
                'id_promotion' => $this->id_promotion
            ]);
        } else {
            $statement = $this->db->prepare(
                'INSERT INTO Products (id_product, name, description, quantity, price, unit, id_promotion) 
                 VALUES (:id_product, :name, :description, :quantity, :price,  :unit, :id_promotion)'
            );
            $this->id_product = uniqid('"PROD_"');
            $result = $statement->execute([
                'id_product' => $this->id_product,
                'name' => $this->name,
                'description' => $this->description,
                'quantity' => $this->quantity,
                'price' => $this->price,
                
                'unit' => $this->unit,
                'id_promotion' => $this->id_promotion
            ]);
            return $result;
        }
    }

    public function fill(array $data): Product
    {
        $this->id_product = $data['id_product'] ?? uniqid('"PROD_"');
        $this->name = $data['name'];
        $this->description = $data['description'] ?? null;
        $this->quantity = $data['quantity'];
        $this->price = $data['price'];
        
        $this->unit = $data['unit'] ?? null;
        $this->id_promotion = $data['id_promotion'] ?? null;

        return $this;
    }

    private function fillFromDbRow(array $row)
    {
        $this->id_product = $row['id_product'];
        $this->name = $row['name'];
        $this->description = $row['description'];
        $this->quantity = $row['quantity'];
        $this->price = $row['price'];
       
        $this->unit = $row['unit'];
        $this->id_promotion = $row['id_promotion'] ?? null;

        // Chuyển chuỗi images thnành mảg (nếu có ảnh)
        $this->images = !empty($row['images']) ? explode(',', $row['images']) : [];

        // Thêm thông tin khuyến mãi (nếu có)
        if (!empty($row['promotion_name'])) {
            $this->promotion = [
                'name' => $row['promotion_name'],
                'description' => $row['promotion_description'],
                'start_day' => $row['start_day'],
                'end_day' => $row['end_day'],
                'discount_rate' => $row['discount_rate']
            ];
        } else {
            $this->promotion = null;
        }
    }


    public function getByCatalogId(string $catalog_id): array
    {
        $statement = $this->db->prepare("
            SELECT 
                p.*, 
                GROUP_CONCAT(ip.URL_image) AS images 
            FROM Products p
            JOIN Product_Catalog_details pcd ON p.id_product = pcd.id_product
            LEFT JOIN Image_Product ip ON p.id_product = ip.id_product
            WHERE pcd.id_catalog = :catalog_id and p.id_promotion is null
            GROUP BY p.id_product
        ");
        $statement->execute(['catalog_id' => $catalog_id]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDiscountedProducts(): array
    {
        $query = "
        SELECT 
            p.id_product,
            p.name,
            p.description,
            p.quantity,
            p.price,
            p.unit,
            p.id_promotion,
            pr.name AS promotion_name,
            pr.description AS promotion_description,
            pr.start_day,
            pr.end_day,
            pr.discount_rate,
            GROUP_CONCAT(ip.URL_image) AS images
        FROM Products p
        JOIN Promotions pr ON p.id_promotion = pr.id_promotion
        LEFT JOIN Image_Product ip ON p.id_product = ip.id_product
        WHERE p.id_promotion IS NOT NULL
        GROUP BY p.id_product
    ";

        $statement = $this->db->prepare($query);
        $statement->execute();

        $products = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Xử lý dữ liệu trước khi trả về
        foreach ($products as &$product) {
            $product['name'] = htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8');
            $product['description'] = htmlspecialchars($product['description'] ?? '', ENT_QUOTES, 'UTF-8');
            $product['promotion_name'] = htmlspecialchars($product['promotion_name'], ENT_QUOTES, 'UTF-8');
            $product['promotion_description'] = htmlspecialchars($product['promotion_description'] ?? '', ENT_QUOTES, 'UTF-8');
        }

        return $products;
    }

    // Lấy danh sách product theo một mảng chứa id_product
    public function getProductsByIds(array $productIds): array
{
    if (empty($productIds)) {
        return [];
    }

    // Tạo chuỗi placeholder cho truy vấn chuẩn bị
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));

    $statement = $this->db->prepare(
        "SELECT 
            p.id_product,
            p.name,
            p.price,
            p.quantity,
            p.unit,
            pr.discount_rate,
            GROUP_CONCAT(ip.URL_image) AS images
         FROM Products p
         LEFT JOIN Promotions pr ON p.id_promotion = pr.id_promotion
         LEFT JOIN Image_Product ip ON p.id_product = ip.id_product
         WHERE p.id_product IN ($placeholders)
         GROUP BY p.id_product"
    );

    $statement->execute($productIds);
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}




}