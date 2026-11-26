<?php

require_once __DIR__ . '/../Models/User.php';

class AuthController
{
    public function login(): void
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';

            if (!is_string($token) ||
                !hash_equals($_SESSION['csrf_token'], $token)) {
                http_response_code(403);
                exit('Недействительный запрос');
            }

            $username = trim((string) ($_POST['username'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');

            $model = new User();
            $user = $model->findByUsername($username);

            if ($user &&
                password_verify($password, $user['password_hash'])) {

                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                header('Location: /');
                exit;
            }

            $error = 'Неверный логин или пароль';
        }

        require __DIR__ . '/../Views/login.php';
    }

    public function logout(): void
    {
        $token = $_POST['csrf_token'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' ||
            !is_string($token) ||
            !hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            exit('Недействительный запрос');
        }

        $_SESSION = [];

        setcookie(session_name(), '', time() - 3600, '/');

        session_destroy();

        header('Location: /?page=login');
        exit;
    }
}