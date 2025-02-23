<?php

namespace App\Models;

use PDO;

class User
{
    private PDO $db;

    public int $id_account = -1;
    public string $email;
    public string $username;
    public string $password;
    public ?string $phone_number = null;
    public ?string $address = null;
    public int $role = 0; // 0: Khách hàng, 1: Nhân viên, 2: Admin
    public string $created_at;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function where(string $column, string $value): User
    {
        $allowedColumns = ['id_account', 'email', 'username', 'phone_number'];
        if (!in_array($column, $allowedColumns)) {
            throw new \Exception("Invalid column: " . htmlspecialchars($column));
        }

        $statement = $this->db->prepare("SELECT * FROM accounts WHERE $column = :value LIMIT 1");
        $statement->execute(['value' => $value]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->fillFromDbRow($row);
        }
        return $this;
    }

    public function save(): bool
    {
        if ($this->id_account >= 0) {
            $statement = $this->db->prepare(
                'UPDATE accounts 
                 SET email = :email, username = :username, password = :password, 
                     phone_number = :phone_number, address = :address, role = :role 
                 WHERE id_account = :id_account'
            );
            return $statement->execute([
                'id_account' => $this->id_account,
                'email' => $this->email,
                'username' => $this->username,
                'password' => $this->password,
                'phone_number' => $this->phone_number,
                'address' => $this->address,
                'role' => $this->role
            ]);
        } else {
            $statement = $this->db->prepare(
                'INSERT INTO accounts (email, username, password, phone_number, address, role, created_at) 
                 VALUES (:email, :username, :password, :phone_number, :address, :role, NOW())'
            );
            $result = $statement->execute([
                'email' => $this->email,
                'username' => $this->username,
                'password' => $this->password,
                'phone_number' => $this->phone_number,
                'address' => $this->address,
                'role' => $this->role
            ]);
            if ($result) {
                $this->id_account = $this->db->lastInsertId();
            }
            return $result;
        }
    }

    public function fill(array $data): User
    {
        $this->email = $data['email'];
        $this->username = $data['username'];

        if (!str_starts_with($data['password'], '$2y$')) {
            $this->password = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            $this->password = $data['password'];
        }

        $this->phone_number = $data['phone_number'] ?? null;
        $this->address = $data['address'] ?? null;

        if ($this->id_account >= 0) { // Chỉ admin mới có quyền chỉnh role
            $this->role = $data['role'] ?? 0;
        }

        return $this;
    }

    private function fillFromDbRow(array $row)
    {
        $this->id_account = $row['id_account'];
        $this->email = $row['email'];
        $this->username = $row['username'];
        $this->password = $row['password'];
        $this->phone_number = $row['phone_number'];
        $this->address = $row['address'];
        $this->role = $row['role'];
        $this->created_at = $row['created_at'];
    }

    public function isEmailInUse(string $email, ?int $ignoreId = null): bool
    {
        $query = 'SELECT COUNT(*) FROM accounts WHERE email = :email';
        $params = ['email' => $email];

        if ($ignoreId !== null) {
            $query .= ' AND id_account != :ignoreId';
            $params['ignoreId'] = $ignoreId;
        }

        $statement = $this->db->prepare($query);
        $statement->execute($params);
        return $statement->fetchColumn() > 0;
    }

    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['email'])) {
            $errors['email'] = 'Email không hợp lệ.';
        } elseif ($this->isEmailInUse($data['email'], $this->id_account)) {
            $errors['email'] = 'Email đã được sử dụng.';
        }

        if (empty($data['username']) || strlen($data['username']) < 3) {
            $errors['username'] = 'Tên người dùng phải có ít nhất 3 ký tự.';
        }

        if (strlen($data['password']) < 6) {
            $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự.';
        } elseif (!isset($data['password_confirm']) || $data['password'] !== $data['password_confirm']) {
            $errors['password'] = 'Mật khẩu xác nhận không khớp.';
        }

        if (!empty($data['phone_number']) && !preg_match('/^[0-9]{10,15}$/', $data['phone_number'])) {
            $errors['phone_number'] = 'Số điện thoại không hợp lệ.';
        }

        return $errors;
    }
}
