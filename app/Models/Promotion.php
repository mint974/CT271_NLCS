<?php

namespace App\Models;

use PDO;

class Promotion
{
    private PDO $db;

    public string $id_promotion;
    public string $name;
    public string $description;
    public string $start_day;
    public string $end_day;
    public float $discount_rate;
    public int $id_account;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function find(string $id): ?Promotion
    {
        $statement = $this->db->prepare(
            'SELECT * FROM Promotions WHERE id_promotion = :id'
        );
        $statement->execute(['id' => $id]);

        if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            return $this->fillFromDbRow($row);
        }

        return null;
    }

    public function save(): bool
    {
        $statement = $this->db->prepare(
            'INSERT INTO Promotions (id_promotion, name, description, start_day, end_day, discount_rate, id_account) 
                VALUES (:id_promotion, :name, :description, :start_day, :end_day, :discount_rate, :id_account)'
        );
        return $statement->execute([
            'id_promotion' => $this->id_promotion,
            'name' => $this->name,
            'description' => $this->description,
            'start_day' => $this->start_day,
            'end_day' => $this->end_day,
            'discount_rate' => $this->discount_rate,
            'id_account' => $this->id_account
        ]);

    }

    public function update(): bool
    {
        $statement = $this->db->prepare(
            'UPDATE Promotions 
         SET name = :name, 
             description = :description, 
             start_day = :start_day, 
             end_day = :end_day, 
             discount_rate = :discount_rate, 
             id_account = :id_account 
         WHERE id_promotion = :id_promotion'
        );

        return $statement->execute([
            'id_promotion' => $this->id_promotion,
            'name' => $this->name,
            'description' => $this->description,
            'start_day' => $this->start_day,
            'end_day' => $this->end_day,
            'discount_rate' => $this->discount_rate,
            'id_account' => $this->id_account
        ]);
    }


    public function delete(): bool
    {
        $statement = $this->db->prepare(
            'DELETE FROM Promotions WHERE id_promotion = :id_promotion'
        );
        return $statement->execute(['id_promotion' => $this->id_promotion]);
    }

    public function fill(array $data): Promotion
    {
        $this->id_promotion = $data['id_promotion'] ?? $this->id_promotion;
        $this->name = $data['name'] ?? $this->name;
        $this->description = $data['description'] ?? $this->description;
        $this->discount_rate = $data['discount_percent'] ?? $this->discount_rate; // tên khác nhau với DB

        if (!empty($data['start_date'])) {
            $this->start_day = date('Y-m-d H:i:s', strtotime($data['start_date']));
        }

        if (!empty($data['end_date'])) {
            $this->end_day = date('Y-m-d H:i:s', strtotime($data['end_date']));
        }

        $this->id_account = AUTHGUARD()->user()->id_account;

        return $this;
    }



    private function fillFromDbRow(array $row): Promotion
    {
        [
            'id_promotion' => $this->id_promotion,
            'name' => $this->name,
            'description' => $this->description,
            'start_day' => $this->start_day,
            'end_day' => $this->end_day,
            'discount_rate' => $this->discount_rate,
            'id_account' => $this->id_account
        ] = $row;
        return $this;
    }

    public function getAll()
    {
        $statement = $this->db->prepare('SELECT * FROM Promotions');
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($rows as $row) {
            $promotion = new Promotion(pdo()); // tạo mới đối tượng mỗi lần
            $result[] = $promotion->fillFromDbRow($row);
        }

        return $result;
    }

    public function where(string $column, string $value): array
    {
        $allowedColumns = ['name', 'start_day', 'end_day'];
        if (!in_array($column, $allowedColumns)) {
            throw new \Exception("Invalid column: " . htmlspecialchars($column));
        }

        $value = trim($value);
        if ($value === '') {
            return [];
        }

        $query = "SELECT * FROM Promotions WHERE $column LIKE :value";

        $statement = $this->db->prepare($query);
        $statement->execute(['value' => '%' . $value . '%']);

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $promotions = [];

        foreach ($rows as $row) {
            $promotion = new Promotion($this->db);
            $promotions[] = $promotion->fillFromDbRow($row);
        }

        return $promotions;
    }


    public function validate(array $data): array
    {
        $errors = [];


        if (empty($data['id_promotion']) || strlen(trim($data['id_promotion'])) < 3) {
            $errors['id_promotion'] = 'Mã khuyến mãi phải có ít nhất 3 ký tự.';
        } elseif (preg_match('/\s/', $data['id_promotion'])) {
            $errors['id_promotion'] = 'Mã khuyến mãi không được chứa khoảng trắng.';
        }


        if (empty($data['name']) || strlen(trim($data['name'])) < 3) {
            $errors['name'] = 'Tên khuyến mãi phải có ít nhất 3 ký tự.';
        }

        if (empty($data['description']) || strlen(trim($data['description'])) < 5) {
            $errors['description'] = 'Mô tả phải có ít nhất 5 ký tự.';
        }


        $percent = $data['discount_percent'] ?? null;
        if (!is_numeric($percent)) {
            $errors['discount_percent'] = 'Phần trăm giảm phải là một số.';
        } elseif ($percent < 0 || $percent > 60) {
            $errors['discount_percent'] = 'Phần trăm giảm phải từ 0 đến 60.';
        }


        if (empty($data['start_date']) || !strtotime($data['start_date'])) {
            $errors['start_date'] = 'Ngày bắt đầu không hợp lệ.';
        }

        // Ngày kết thúc: phải hợp lệ và sau ngày bắt đầu
        if (empty($data['end_date']) || !strtotime($data['end_date'])) {
            $errors['end_date'] = 'Ngày kết thúc không hợp lệ.';
        } elseif (!isset($errors['start_date']) && strtotime($data['end_date']) < strtotime($data['start_date'])) {
            $errors['end_date'] = 'Ngày kết thúc phải sau ngày bắt đầu.';
        }

        return $errors;
    }

    public function countActivePromotions(): int
{
    $now = date('Y-m-d H:i:s');
    $statement = $this->db->prepare(
        'SELECT COUNT(*) as total FROM Promotions WHERE start_day <= :now AND end_day >= :now'
    );
    $statement->execute(['now' => $now]);

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return $result['total'] ?? 0;
}

}
