<?php

require_once __DIR__ . '/../Core/Database.php';

class Employee
{
    public function getAll(): array
    {
        $db = Database::getConnection();

        return $db->query(
            "SELECT e.*,
                    d.name AS department_name,
                    p.name AS position_name
             FROM employees e
             JOIN departments d ON e.department_id = d.id
             JOIN positions p ON e.position_id = p.id
             ORDER BY e.is_active DESC, e.full_name"
        )->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $db = Database::getConnection();

        $stmt = $db->prepare(
            'SELECT * FROM employees WHERE id = :id'
        );

        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function create(array $data): void
    {
        $db = Database::getConnection();

        $stmt = $db->prepare(
            'INSERT INTO employees
             (full_name, email, phone, hire_date,
              department_id, position_id)
             VALUES
             (:full_name, :email, :phone, :hire_date,
              :department_id, :position_id)'
        );

        $stmt->execute($data);
    }

    public function update(int $id, array $data): void
    {
        $db = Database::getConnection();

        $stmt = $db->prepare(
            'UPDATE employees SET
                full_name = :full_name,
                email = :email,
                phone = :phone,
                hire_date = :hire_date,
                department_id = :department_id,
                position_id = :position_id
             WHERE id = :id'
        );

        $data['id'] = $id;

        $stmt->execute($data);
    }

    public function setActive(int $id, bool $active): void
    {
        $db = Database::getConnection();

        $stmt = $db->prepare(
            'UPDATE employees
             SET is_active = :active
             WHERE id = :id'
        );

        $stmt->execute([
            'active' => $active ? 1 : 0,
            'id' => $id
        ]);
    }

    public function getProfile(int $id): ?array
    {
        $db = Database::getConnection();

        $stmt = $db->prepare(
            "SELECT e.*,
                d.name AS department_name,
                p.name AS position_name
         FROM employees e
         JOIN departments d
             ON e.department_id = d.id
         JOIN positions p
             ON e.position_id = p.id
         WHERE e.id = :id
         AND e.is_active = 1"
        );

        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }
    public function getDepartments(): array
    {
        $db = Database::getConnection();

        return $db->query(
            'SELECT * FROM departments ORDER BY name'
        )->fetchAll();
    }

    public function getPositions(): array
    {
        $db = Database::getConnection();

        return $db->query(
            'SELECT * FROM positions ORDER BY name'
        )->fetchAll();
    }
}