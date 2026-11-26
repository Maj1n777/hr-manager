<?php

require_once __DIR__ . '/../Core/Database.php';

try {
    $db = Database::getConnection();

    $sql = file_get_contents(__DIR__ . '/schema.sql');

    $db->beginTransaction();
    $db->exec($sql);
    $db->commit();

    echo "Database initialized successfully!" . PHP_EOL;
    echo "Created tables:" . PHP_EOL;

    $tables = $db->query(
        "SELECT name FROM sqlite_master
         WHERE type = 'table'
         AND name NOT LIKE 'sqlite_%'
         ORDER BY name"
    )->fetchAll();

    foreach ($tables as $table) {
        echo "- " . $table['name'] . PHP_EOL;
    }
} catch (Throwable $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }

    echo "Database error: " . $e->getMessage() . PHP_EOL;
    exit(1);
}