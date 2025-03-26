<?php

namespace App\Models;

use PDO;

class DeliveryInformation
{
    private PDO $db;
    public ?string $id_delivery = null;
    public int $id_account;
    public string $house_number;
    public string $ward;
    public string $district;
    public string $city;
    public string $receiver_name;
    public string $receiver_phone;
    public float $shipping_fee;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function where(string $column, string $value): ?self
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
                     receiver_phone = :receiver_phone, shipping_fee = :shipping_fee
                 WHERE id_delivery = :id_delivery'
            );
        } else {
            $statement = $this->db->prepare(
                'INSERT INTO Delivery_Information (id_account, house_number, ward, district, city, receiver_name, receiver_phone, shipping_fee, id_delivery) 
                 VALUES (:id_account, :house_number, :ward, :district, :city, :receiver_name, :receiver_phone, :shipping_fee, :id_delivery)'
            );
        }

        $success = $statement->execute([
            'id_account' => $this->id_account,
            'house_number' => $this->house_number,
            'ward' => $this->ward,
            'district' => $this->district,
            'city' => $this->city,
            'receiver_name' => $this->receiver_name,
            'receiver_phone' => $this->receiver_phone,
            'shipping_fee' => $this->shipping_fee,
            'id_delivery' => $this->id_delivery ?? null
        ]);

        if ($success && empty($this->id_delivery)) {
            $this->id_delivery = $this->db->lastInsertId();
        }
        return $success;
    }

    public function fill(array $data): self
    {
        $this->id_account = $data['id_account'] ?? AUTHGUARD()->user()->id_account;
        $this->house_number = $data['house_number'];
        $this->ward = $data['ward'];
        $this->district = $data['district'];
        $this->city = $data['city'];
        $this->receiver_name = $data['receiver_name'];
        $this->receiver_phone = $data['receiver_phone'];
        $this->shipping_fee = $data['shipping_fee'] ?? $this->calculateShippingFee($data['city']);

        if (!empty($data['id_delivery'])) {
            $this->id_delivery = $data['id_delivery'];
        }
        return $this;
    }

    private function fillFromDbRow(array $row): self
    {
        return $this->fill($row);
    }

    public function validate(array $data): array
    {
        $errors = [];
        if (empty($data['receiver_phone']) || !preg_match('/^\d{10,20}$/', $data['receiver_phone'])) {
            $errors['receiver_phone'] = 'Số điện thoại không hợp lệ.';
        }
        if (empty($data['house_number'])) {
            $errors['house_number'] = 'Số nhà không được để trống.';
        }
        if (empty($data['ward'])) {
            $errors['ward'] = 'Phường không được để trống.';
        }
        if (empty($data['district'])) {
            $errors['district'] = 'Quận/Huyện không được để trống.';
        }
        if (empty($data['city'])) {
            $errors['city'] = 'Thành phố không được để trống.';
        }
        return $errors;
    }

    public function getAllDeliveryInfo(int $id_user): array
    {
        $statement = $this->db->prepare(
            "SELECT * FROM Delivery_Information WHERE id_account = :id_user"
        );
        $statement->execute(['id_user' => $id_user]);

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        $deliveries = [];
        foreach ($results as $row) {
            $delivery = new DeliveryInformation($this->db);
            $delivery->fill($row);
            $deliveries[] = $delivery;
        }
        return $deliveries;
    }

    //kiểm tra trùng lặp
    private function calculateShippingFee(string $city): float
    {
        return $city === 'Cần Thơ' ? 15000.0 : 30000.0;
    }

    public function getById(string $id_delivery): ?array
    {
        $statement = $this->db->prepare("SELECT * FROM Delivery_Information WHERE id_delivery = :id_delivery");
        $statement->execute(['id_delivery' => $id_delivery]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function isDuplicateAddress(
        int $id_account,
        string $house_number,
        string $ward,
        string $district,
        string $city,
        string $id_delivery = null,
        string $receiver_name,
        string $receiver_phone
    ): bool {
        $query = "SELECT COUNT(*) FROM Delivery_Information 
                  WHERE id_account = :id_account 
                    AND house_number = :house_number 
                    AND ward = :ward 
                    AND district = :district 
                    AND city = :city
                    AND receiver_name = :receiver_name
                    AND receiver_phone = :receiver_phone";

        // Nếu có `id_delivery`, bỏ qua địa chỉ của chính nó khi kiểm tra trùng lặp
        if ($id_delivery !== null) {
            $query .= " AND id_delivery != :id_delivery";
        }

        $statement = $this->db->prepare($query);
        $params = [
            'id_account' => $id_account,
            'house_number' => $house_number,
            'ward' => $ward,
            'district' => $district,
            'city' => $city,
            'receiver_name' => $receiver_name,
            'receiver_phone' => $receiver_phone
        ];

        if ($id_delivery !== null) {
            $params['id_delivery'] = $id_delivery;
        }

        $statement->execute($params);
        return $statement->fetchColumn() > 0;
    }

}