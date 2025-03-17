<?php

namespace App\Models;

use PDO;

class User
{
    private PDO $db;

    public ?int $id_account = null;
    public string $email;
    public string $username;
    public string $password;
    public int $role = 0; // 0: Khách hàng, 1: Nhân viên, 2: Admin
    public string $created_at;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function where(string $column, string $value): User
    {
        $allowedColumns = ['id_account', 'email', 'username'];
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
        if (!empty($this->id_account)) {
            // Cập nhật tài khoản nếu đã có ID
            $statement = $this->db->prepare(
                'UPDATE Accounts 
                 SET username = :username, email = :email, password = :password, role = :role
                 WHERE id_account = :id_account'
            );
            $result = $statement->execute([
                'id_account' => $this->id_account,
                'username' => $this->username,
                'email' => $this->email,
                'password' => $this->password,
                'role' => $this->role
            ]);
        } else {
            $statement = $this->db->prepare(
                'INSERT INTO Accounts (username, email, password, role) 
                 VALUES (:username, :email, :password, :role)'
            );
            $result = $statement->execute([
                'username' => $this->username,
                'email' => $this->email,
                'password' => $this->password,
                'role' => $this->role
            ]);

            if ($result) {
                // Truy vấn lại id_account từ database
                $query = $this->db->prepare("SELECT id_account FROM Accounts WHERE email = :email");
                $query->execute(['email' => $this->email]);
                $account = $query->fetch(PDO::FETCH_ASSOC);

                if ($account) {
                    $this->id_account = $account['id_account'];
                } else {
                    throw new \Exception("Lỗi: Không lấy được id_account.");
                }
            }
        }
        return $result;
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
        $this->role = $row['role'];
        $this->created_at = $row['created_at'];
    }

    public function isEmailInUse(string $email, ?int $ignoreId = null): bool
    {
        $query = 'SELECT COUNT(*) FROM accounts WHERE email = :email';
        $params = ['email' => $email];

        if (!is_null($ignoreId)) {
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

        return $errors;
    }
}
