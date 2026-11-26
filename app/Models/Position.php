<?php

require_once __DIR__ . '/../Core/Database.php';

class Position
{
    public function getAll(): array
    {
        $db = Database::getConnection();

        return $db->query(
            "SELECT p.*,
                    COUNT(e.id) AS employee_count
             FROM positions p
             LEFT JOIN employees e
                 ON p.id = e.position_id
             GROUP BY p.id
             ORDER BY p.name"
        )->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $db = Database::getConnection();

        $stmt = $db->prepare(
            'SELECT * FROM positions WHERE id = :id'
        );

        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function create(array $data): void
    {
        $db = Database::getConnection();

        $stmt = $db->prepare(
            'INSERT INTO positions (name, description)
             VALUES (:name, :description)'
        );

        $stmt->execute($data);
    }

    public function update(int $id, array $data): void
    {
        $db = Database::getConnection();

        $stmt = $db->prepare(
            'UPDATE positions SET
                name = :name,
                description = :description
             WHERE id = :id'
        );

        $data['id'] = $id;

        $stmt->execute($data);
    }

    public function delete(int $id): void
    {
        $db = Database::getConnection();

        $stmt = $db->prepare(
            'DELETE FROM positions WHERE id = :id'
        );

        $stmt->execute(['id' => $id]);
    }
}