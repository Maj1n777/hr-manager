<?php

require_once __DIR__ . '/../Core/Database.php';

class Department
{
    public function getAll(): array
    {
        $db = Database::getConnection();

        return $db->query(
            "SELECT d.*,
                    COUNT(e.id) AS employee_count
             FROM departments d
             LEFT JOIN employees e
                 ON d.id = e.department_id
             GROUP BY d.id
             ORDER BY d.name"
        )->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $db = Database::getConnection();

        $stmt = $db->prepare(
            'SELECT * FROM departments WHERE id = :id'
        );

        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function create(array $data): void
    {
        $db = Database::getConnection();

        $stmt = $db->prepare(
            'INSERT INTO departments (name, description)
             VALUES (:name, :description)'
        );

        $stmt->execute($data);
    }

    public function update(int $id, array $data): void
    {
        $db = Database::getConnection();

        $stmt = $db->prepare(
            'UPDATE departments SET
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
            'DELETE FROM departments WHERE id = :id'
        );

        $stmt->execute(['id' => $id]);
    }
}