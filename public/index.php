<?php

ini_set('session.use_strict_mode', '1');

session_set_cookie_params([
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

header('Cache-Control: no-store');

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/../app/Controllers/HomeController.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Controllers/EmployeeController.php';
require_once __DIR__ . '/../app/Controllers/DepartmentController.php';

$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'login':
        $controller = new AuthController();
        $controller->login();
        break;

    case 'logout':
        $controller = new AuthController();
        $controller->logout();
        break;

    case 'home':
        $controller = new HomeController();
        $controller->index();
        break;

    case 'admin':
        $controller = new HomeController();
        $controller->admin();
        break;

    case 'employee':
        $controller = new HomeController();
        $controller->employee();
        break;

    case 'employees':
        $controller = new EmployeeController();
        $controller->index();
        break;

    case 'departments':
        $controller = new DepartmentController();
        $controller->index();
        break;

    default:
        http_response_code(404);
        echo 'Страница не найдена';
}