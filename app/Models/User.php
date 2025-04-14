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
    public string $role = "khách hàng";
    public string $url = 'assets/image/default_avatar.jpg';

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

    public function wherearray(string $column, string $value): array
    {
        $allowedColumns = ['id_account', 'email', 'username'];
        if (!in_array($column, $allowedColumns)) {
            throw new \Exception("Invalid column: " . htmlspecialchars($column));
        }

        $statement = $this->db->prepare("
        SELECT 
            a.id_account, 
            a.username,  
            a.email,   
            a.role,    
            a.url,   
            ah.status
        FROM accounts a
        LEFT JOIN (
            SELECT ah1.id_account, ah1.status
            FROM activity_history ah1
            INNER JOIN (
                SELECT id_account, MAX(action_time) AS latest_action
                FROM activity_history
                GROUP BY id_account
            ) latest
            ON ah1.id_account = latest.id_account AND ah1.action_time = latest.latest_action
        ) ah
        ON a.id_account = ah.id_account
        WHERE a.$column = :value
        LIMIT 1
    ");
        $statement->execute(['value' => $value]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row ?: [];
    }


    public function save(): bool
    {
        if (!empty($this->id_account)) {
            $statement = $this->db->prepare(
                'UPDATE Accounts 
                 SET username = :username, email = :email, password = :password, role = :role, url = :url
                 WHERE id_account = :id_account'
            );
            $result = $statement->execute([
                'id_account' => $this->id_account,
                'username' => $this->username,
                'email' => $this->email,
                'password' => $this->password,
                'role' => $this->role,
                'url' => $this->url
            ]);
        } else {
            $statement = $this->db->prepare(
                'INSERT INTO Accounts (username, email, password, role, url) 
                 VALUES (:username, :email, :password, :role, :url)'
            );
            $result = $statement->execute([
                'username' => $this->username,
                'email' => $this->email,
                'password' => $this->password,
                'role' => $this->role,
                'url' => $this->url
            ]);

            if ($result) {
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

        if (isset($data['email']) && $data['email'] !== '') {
            $this->email = $data['email'];
        }
        if (isset($data['username']) && $data['username'] !== '') {
            $this->username = $data['username'];
        }
        if (!empty($data['new_password'])) {
            // Luôn hash new_password nếu được cung cấp
            $this->password = password_hash($data['new_password'], PASSWORD_DEFAULT);
        } elseif (!empty($data['password']) && !str_starts_with($data['password'], '$2y$')) {
            // Chỉ hash password nếu không phải dạng hash
            $this->password = password_hash($data['password'], PASSWORD_DEFAULT);
        } elseif (!empty($data['password'])) {
            // Nếu đã hash rồi
            $this->password = $data['password'];
        }
        


        if (isset($data['avatar']) && $data['avatar'] !== '') {
            $this->url = $data['avatar'];
        }

        if (isset($data['role'])) {
            $this->role = $data['role'] ?? $this->role;
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
        $this->url = $row['url'] ?? '/assets/image/default_avatar.jpg';
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

    public function isDuplicateAccountInfo($data)
    {
        $noChange =
            $this->email === $data['email'] &&
            $this->username === $data['username'] &&
            $data['avatar'] === '' &&
            $this->role === $data['role'] ;
        return $noChange;
    }


    public function getAllUser()
    {
        $query = 'SELECT a.id_account, a.username,  a.email,   a.role,    a.url,   ah.status
                    FROM  accounts a
                    LEFT JOIN (
                        SELECT ah1.id_account,   ah1.status
                        FROM  activity_history ah1
                        INNER JOIN (
                             SELECT   id_account,  MAX(action_time) AS latest_action
                            FROM  activity_history
                            GROUP BY  id_account
                            ) latest
                        ON ah1.id_account = latest.id_account  AND ah1.action_time = latest.latest_action
                        ) ah
                    ON a.id_account = ah.id_account';

        $statement = $this->db->prepare($query);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }


    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ.';
        } elseif ($this->isEmailInUse($data['email'], $this->id_account)) {
            $errors['email'] = 'Email đã được sử dụng.';
        }

        if (empty($data['username']) || strlen($data['username']) < 3) {
            $errors['username'] = 'Tên người dùng phải có ít nhất 3 ký tự.';
        }

        if (!isset($data['new_password'])) {
            // Trường hợp tạo mới mật khẩu (ví dụ: đăng ký)
            if (empty($data['password']) || strlen($data['password']) < 6) {
                $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự.';
            } elseif (!isset($data['password_confirm']) || $data['password'] !== $data['password_confirm']) {
                $errors['password'] = 'Mật khẩu xác nhận không khớp.';
            }
        } else {
            // Trường hợp thay đổi mật khẩu
            $old = trim($data['password'] ?? '');
            $new = trim($data['new_password'] ?? '');
            $confirm = trim($data['password_confirm'] ?? '');

            // Nếu cả ba trường đều rỗng => bỏ qua (không thay đổi mật khẩu)
            if ($old === '' && $new === '' && $confirm === '') {
                // Không làm gì, tiếp tục xử lý phần khác
            } elseif ($old === '' || $new === '' || $confirm === '') {
                $errors['password'] = 'Phải nhập đầy đủ mật khẩu cũ, mới và xác nhận.';
            } elseif (strlen($old) < 6) {
                $errors['password'] = 'Mật khẩu cũ phải có ít nhất 6 ký tự.';
            } elseif (strlen($new) < 6) {
                $errors['password'] = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
            } elseif ($new !== $confirm) {
                $errors['password'] = 'Mật khẩu mới xác nhận không khớp.';
            }
        }
        return $errors;
    }

}
