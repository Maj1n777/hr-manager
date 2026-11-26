<?php

require_once __DIR__ . '/../Core/Database.php';

$db = Database::getConnection();

$stmt = $db->prepare(
    'SELECT id FROM users WHERE username = :username'
);

$stmt->execute(['username' => 'admin']);

if ($stmt->fetch()) {
    echo "Administrator already exists." . PHP_EOL;
    exit;
}

$password = bin2hex(random_bytes(12));
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $db->prepare(
    'INSERT INTO users (username, password_hash, role)
     VALUES (:username, :password_hash, :role)'
);

$stmt->execute([
    'username' => 'admin',
    'password_hash' => $passwordHash,
    'role' => 'admin'
]);

echo "Administrator created successfully!" . PHP_EOL;
echo "Login: admin" . PHP_EOL;
echo "Password: " . $password . PHP_EOL;