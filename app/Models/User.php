<?php

require_once __DIR__ . '/../Core/Database.php';

class User
{
    public function findByUsername(string $username): ?array
    {
        $db = Database::getConnection();

        $stmt = $db->prepare(
            "SELECT u.* FROM users u
             LEFT JOIN employees e ON u.employee_id = e.id
             WHERE u.username = :username
             AND (u.role = 'admin' OR e.is_active = 1)"
        );

        $stmt->execute(['username' => $username]);

        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $db = Database::getConnection();

        $stmt = $db->prepare(
            "SELECT u.* FROM users u
             LEFT JOIN employees e ON u.employee_id = e.id
             WHERE u.id = :id
             AND (u.role = 'admin' OR e.is_active = 1)"
        );

        $stmt->execute(['id' => $id]);

        $user = $stmt->fetch();

        return $user ?: null;
    }
}