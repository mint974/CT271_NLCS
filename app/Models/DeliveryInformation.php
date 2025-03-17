<?php

namespace App\Models;

use PDO;

class DeliveryInformation
{
    private PDO $db;
    public string $id_delivery;
    public int $id_account;
    public string $house_number;
    public string $ward;
    public string $district;
    public string $city;
    public string $receiver_name;
    public string $receiver_phone;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function where(string $column, string $value): ?DeliveryInformation
    {
        $allowedColumns = ['id_delivery', 'id_account'];
        if (!in_array($column, $allowedColumns)) {
            throw new \Exception("Invalid column: " . htmlspecialchars($column));
        }

        $statement = $this->db->prepare("SELECT * FROM Delivery_Information WHERE $column = :value LIMIT 1");
        $statement->execute(['value' => $value]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->fillFromDbRow($row) : null;
    }

    public function save(): bool
    {
        if (!empty($this->id_delivery)) {
            $statement = $this->db->prepare(
                'UPDATE Delivery_Information 
                 SET id_account = :id_account, house_number = :house_number, ward = :ward, 
                     district = :district, city = :city, receiver_name = :receiver_name, 
                     receiver_phone = :receiver_phone
                 WHERE id_delivery = :id_delivery'
            );
        } else {
            $statement = $this->db->prepare(
                'INSERT INTO Delivery_Information ( id_account, house_number, ward, district, city, receiver_name, receiver_phone) 
                 VALUES ( :id_account, :house_number, :ward, :district, :city, :receiver_name, :receiver_phone)'
            );
        }

        return $statement->execute([
            
            'id_account' => $this->id_account,
            'house_number' => $this->house_number,
            'ward' => $this->ward,
            'district' => $this->district,
            'city' => $this->city,
            'receiver_name' => $this->receiver_name,
            'receiver_phone' => $this->receiver_phone
        ]);
    }

    public function fill(array $data): DeliveryInformation
    {
        $this->id_account = $data['id_account'];
        $this->house_number = $data['house_number'];
        $this->ward = $data['ward'];
        $this->district = $data['district'];
        $this->city = $data['city'];
        $this->receiver_name = $data['receiver_name'];
        $this->receiver_phone = $data['receiver_phone'];

        if (!empty($data['id_delivery'])) {
            $this->id_delivery = $data['id_delivery'];
        }
        return $this;
    }

    private function fillFromDbRow(array $row): DeliveryInformation
    {
        $this->id_delivery = $row['id_delivery'];
        $this->id_account = $row['id_account'];
        $this->house_number = $row['house_number'];
        $this->ward = $row['ward'];
        $this->district = $row['district'];
        $this->city = $row['city'];
        $this->receiver_name = $row['receiver_name'];
        $this->receiver_phone = $row['receiver_phone'];
        return $this;
    }

    public function validate(array $data): array
    {
        $errors = [];
        if (empty($data['id_account']) || !is_numeric($data['id_account'])) {
            $errors['id_account'] = 'ID tài khoản không hợp lệ.';
        }
        if (empty($data['receiver_name'])) {
            $errors['receiver_name'] = 'Tên người nhận không được để trống.';
        }
        if (empty($data['receiver_phone']) || !preg_match('/^\d{10,20}$/', $data['receiver_phone'])) {
            $errors['receiver_phone'] = 'Số điện thoại không hợp lệ.';
        }
        return $errors;
    }
}
