<?php

namespace App\Models;

use PDO;
use DateTime;
use DateTimeZone;

class ActivityHistory
{
    private PDO $db;

    public string $id_activity;
    public int $id_account;
    public string $action_time;
    public string $action;
    public string $status;
    public int $created_by;

    public $actor;
    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    // Lấy lịch sử hoạt động của một tài khoản
    public function getByAccountId(int $id_account): array
    {
        $activities = [];


        $statement = $this->db->prepare(
            'SELECT ah.*, a.role AS actor
             FROM activity_history ah
             JOIN accounts a ON ah.created_by = a.id_account
             WHERE ah.id_account = :id_account
             ORDER BY ah.action_time DESC'
        );
        $statement->execute(['id_account' => $id_account]);

        while ($row = $statement->fetch()) {
            $activity = new ActivityHistory($this->db);
            $activity->fillFromDbRow($row);

            // Gán actor vào object
            $activity->actor = $row['actor'];

            $activities[] = $activity;
        }

        return $activities;
    }

    public function getById(int $id_account): array
    {
        $activities = [];

        $statement = $this->db->prepare(
            'SELECT * FROM activity_history WHERE id_account = :id_account ORDER BY action_time DESC'
        );
        $statement->execute(['id_account' => $id_account]);

        while ($row = $statement->fetch()) {
            $activity = new ActivityHistory($this->db);
            $activity->fillFromDbRow($row);
            $activities[] = $activity;
        }

        return $activities;
    }


    // Lưu lịch sử hoạt động vào database
    public function save(): bool
    {
        $statement = $this->db->prepare(
            'INSERT INTO activity_history 
            (id_account, action_time, action, status, created_by) 
            VALUES (:id_account, :action_time, :action, :status, :created_by)'
        );

        // Lấy thời gian hiện tại theo múi giờ +7
        $now = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
        $this->action_time = $this->action_time ?? $now->format('Y-m-d H:i:s');

        $success = $statement->execute([
            'id_account' => $this->id_account,
            'action_time' => $this->action_time,
            'action' => $this->action,
            'status' => $this->status,
            'created_by' => $this->created_by
        ]);

        if ($success) {
            //Lấy id_activity mới nhất của tài khoản này từ DB
            $query = $this->db->prepare(
                'SELECT id_activity FROM activity_history
                 WHERE id_account = :id_account 
                 ORDER BY action_time DESC 
                 LIMIT 1'
            );
            $query->execute(['id_account' => $this->id_account]);
            $this->id_activity = $query->fetchColumn();

        }
        return $success;
    }


    public function find(string $status): array
    {
        $sql = "
       SELECT a.id_account, a.username, a.email, a.role, a.url, latest_ah.status
        FROM accounts a
        JOIN (
            SELECT ah.id_account, ah.status
            FROM activity_history ah
            INNER JOIN (
                SELECT id_account, MAX(action_time) AS latest_action
                FROM activity_history
                GROUP BY id_account
            ) latest
            ON ah.id_account = latest.id_account AND ah.action_time = latest.latest_action
            WHERE ah.status = :status
            ) latest_ah ON a.id_account = latest_ah.id_account
    ";

        $statement = $this->db->prepare($sql);
        $statement->execute(['status' => $status]);

        return $statement->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }


    public function countAccountStatuses(): array
    {
        $sql = "
            SELECT latest_status.status, COUNT(*) AS count
            FROM (
                SELECT ah1.id_account, ah1.status
                FROM activity_history ah1
                INNER JOIN (
                    SELECT id_account, MAX(action_time) AS max_time
                    FROM activity_history
                    GROUP BY id_account
                ) ah2 ON ah1.id_account = ah2.id_account AND ah1.action_time = ah2.max_time
            ) AS latest_status
            GROUP BY latest_status.status
        ";

        $statement = $this->db->prepare($sql);
        $statement->execute();

        $result = [
            'active' => 0,
            'suspend' => 0,
            'pending_activation' => 0
        ];

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            switch ($row['status']) {
                case 'Hoạt động':
                    $result['active'] = (int) $row['count'];
                    break;
                case 'Vô hiệu hóa tài khoản':
                    $result['suspend'] = (int) $row['count'];
                    break;
                case 'Khôi phục tài khoản':
                    $result['pending_activation'] = (int) $row['count'];
                    break;
            }
        }

        return $result;
    }


    // Xóa một lịch sử hoạt động
    public function delete(): bool
    {
        $statement = $this->db->prepare(
            'DELETE FROM activity_history WHERE id_activity = :id_activity'
        );
        return $statement->execute(['id_activity' => $this->id_activity]);
    }

    public function fill(array $data): ActivityHistory
    {
        $this->id_account = $data['id_account'] ?? 0;
        $this->action_time = $data['action_time'] ?? (new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh')))->format('Y-m-d H:i:s');
        $this->action = $data['action'] ?? '';
        $this->status = $data['status'] ?? '';
        $this->created_by = AUTHGUARD()->user() ? AUTHGUARD()->user()->id_account : $data['id_account'];
        return $this;
    }

    private function fillFromDbRow(array $row): ActivityHistory
    {
        $this->id_activity = $row['id_activity'];
        $this->id_account = $row['id_account'];
        $this->action_time = $row['action_time'];
        $this->action = $row['action'];
        $this->status = $row['status'];
        $this->created_by = $row['created_by'];
        return $this;
    }
}
