<?php

require_once __DIR__ . '/../Core/Database.php';

$db = Database::getConnection();

try {
    $stmt = $db->prepare(
        'SELECT id FROM users WHERE username = :username'
    );

    $stmt->execute(['username' => 'employee']);

    if ($stmt->fetch()) {
        echo "Test employee already exists." . PHP_EOL;
        exit;
    }

    $db->beginTransaction();

    $db->exec(
        "INSERT OR IGNORE INTO departments (name)
         VALUES ('Отдел разработки')"
    );

    $db->exec(
        "INSERT OR IGNORE INTO positions (name)
         VALUES ('Разработчик')"
    );

    $departmentId = $db->query(
        "SELECT id FROM departments
         WHERE name = 'Отдел разработки'"
    )->fetchColumn();

    $positionId = $db->query(
        "SELECT id FROM positions
         WHERE name = 'Разработчик'"
    )->fetchColumn();

    $stmt = $db->prepare(
        'INSERT INTO employees
         (full_name, email, hire_date,
          department_id, position_id)
         VALUES
         (:name, :email, :date, :department, :position)'
    );

    $stmt->execute([
        'name' => 'Иванов Иван Иванович',
        'email' => 'employee@example.test',
        'date' => date('Y-m-d'),
        'department' => $departmentId,
        'position' => $positionId
    ]);

    $employeeId = $db->lastInsertId();

    $password = bin2hex(random_bytes(12));

    $stmt = $db->prepare(
        'INSERT INTO users
         (employee_id, username, password_hash, role)
         VALUES
         (:employee, :username, :password, :role)'
    );

    $stmt->execute([
        'employee' => $employeeId,
        'username' => 'employee',
        'password' => password_hash(
            $password,
            PASSWORD_DEFAULT
        ),
        'role' => 'employee'
    ]);

    $db->commit();

    echo "Employee created successfully!" . PHP_EOL;
    echo "Login: employee" . PHP_EOL;
    echo "Password: " . $password . PHP_EOL;

} catch (Throwable $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }

    echo "Error: " . $e->getMessage() . PHP_EOL;
    exit(1);
}