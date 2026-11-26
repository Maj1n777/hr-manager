<?php

require_once __DIR__ . '/../Models/User.php';

class Auth
{
    public static function requireLogin(): array
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /?page=login');
            exit;
        }

        $model = new User();

        $user = $model->findById(
            (int) $_SESSION['user_id']
        );

        if (!$user) {
            unset(
                $_SESSION['user_id'],
                $_SESSION['username'],
                $_SESSION['role']
            );

            header('Location: /?page=login');
            exit;
        }

        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        return $user;
    }

    public static function requireRole(string $role): array
    {
        $user = self::requireLogin();

        if ($user['role'] !== $role) {
            http_response_code(403);

            exit(
                '403 — Доступ запрещён. ' .
                'У вас недостаточно прав.'
            );
        }

        return $user;
    }
}