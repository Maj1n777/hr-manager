<?php

require_once __DIR__ . '/../Core/Database.php';

class User
{
    public function findByUsername(string $username): ?array
    {
        $db = Database::getConnection();

        $stmt = $db->prepare(
            'SELECT * FROM users WHERE username = :username'
        );

        $stmt->execute([
            'username' => $username
        ]);

        $user = $stmt->fetch();

        return $user ?: null;
    }
}