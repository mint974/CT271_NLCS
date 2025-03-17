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
        if (!empty($this->id_promotion)) {
            $statement = $this->db->prepare(
                'UPDATE Promotions SET name = :name, description = :description, 
                start_day = :start_day, end_day = :end_day, discount_rate = :discount_rate, id_account = :id_account 
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
        } else {
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
    }

    public function delete(): bool
    {
        $statement = $this->db->prepare(
            'DELETE FROM Promotions WHERE id_promotion = :id_promotion'
        );
        return $statement->execute(['id_promotion' => $this->id_promotion]);
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
}
