<?php

namespace App\Models;

use PDO;

class Contact
{
    private PDO $db;

    public string $id_contact;
    public string $subject;
    public string $content;
    public string $status;
    public string $created_at;
    public ?int $id_account;
    public string $phone;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function save(): bool
    {
        $result = false;

        if (!empty($this->id_contact)) {
            $statement = $this->db->prepare(
                'UPDATE Contacts SET subject = :subject, content = :content, 
                 status = :status, id_account = :id_account, phone = :phone
                 WHERE id_contact = :id_contact'
            );

            $result = $statement->execute([
                'subject' => $this->subject,
                'content' => $this->content,
                'status' => $this->status,
                'id_account' => $this->id_account,
                'phone' => $this->phone,
                'id_contact' => $this->id_contact
            ]);
        } else {
            $statement = $this->db->prepare(
                'INSERT INTO Contacts ( subject, content, status, id_account, phone, created_at)
                 VALUES ( :subject, :content, :status, :id_account, :phone, NOW())'
            );

            $result = $statement->execute([
                'subject' => $this->subject,
                'content' => $this->content,
                'status' => $this->status,
                'id_account' => $this->id_account,
                'phone' => $this->phone
            ]);
        }

        return $result;
    }

    public function find(string $id): ?Contact
    {
        $statement = $this->db->prepare(
            'SELECT * FROM Contacts WHERE id_contact = :id_contact'
        );
        $statement->execute(['id_contact' => $id]);

        if ($row = $statement->fetch()) {
            return $this->fillFromDbRow($row);
        }

        return null;
    }

    public function delete(): bool
    {
        $statement = $this->db->prepare(
            'DELETE FROM Contacts WHERE id_contact = :id_contact'
        );
        return $statement->execute(['id_contact' => $this->id_contact]);
    }

    public function fill(array $data): Contact
    {
        $this->id_contact = $data['id_contact'] ?? '';
        $this->subject = $data['subject'] ?? 'Góp ý chung';
        $this->content = $data['content'] ?? '';
        $this->status = $data['status'] ?? 'Chưa phản hồi';
        $this->id_account = AUTHGUARD()->user()->id_account;
        $this->phone = $data['phone'] ?? '';
        return $this;
    }

    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['phone']) || !preg_match('/^\d{10,20}$/', $data['phone'])) {
            $errors['phone'] = 'Số điện thoại không hợp lệ.';
        }

        if (empty(trim($data['content'] ?? ''))) {
            $errors['content'] = 'Nội dung không được để trống.';
        }

        return $errors;
    }

    private function fillFromDbRow(array $row): Contact
    {
        $this->id_contact = $row['id_contact'];
        $this->subject = $row['subject'];
        $this->content = $row['content'];
        $this->status = $row['status'];
        $this->created_at = $row['created_at'];
        $this->id_account = $row['id_account'];
        $this->phone = $row['phone'];
        return $this;
    }

    public function getAll(): array
    {
        $statement = $this->db->prepare('SELECT * FROM Contacts ORDER BY created_at DESC');
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($rows as $row) {
            $contact = new Contact($this->db);
            $result[] = $contact->fillFromDbRow($row);
        }

        return $result;
    }

    public function getBySubject(string $subject): int
    {
        $statement = $this->db->prepare('SELECT COUNT(*) FROM Contacts WHERE subject = :subject');
        $statement->execute(['subject' => $subject]);
        
        return (int)$statement->fetchColumn();
    }    

}
