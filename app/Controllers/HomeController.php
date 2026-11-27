<?php

require_once __DIR__ . '/../Models/AppInfo.php';
require_once __DIR__ . '/../Core/Auth.php';

class HomeController
{
    public function index(): void
    {
        $user = Auth::requireLogin();

        $model = new AppInfo();
        $data = $model->getInfo();

        require __DIR__ . '/../Views/home.php';
    }

    public function admin(): void
    {
        Auth::requireRole('admin');

        $heading = 'Панель администратора';
        $message = 'Раздел управления персоналом.';

        $links = [
            '/?page=employees' => 'Управление сотрудниками',
            '/?page=departments' => 'Управление подразделениями',
            '/?page=positions' => 'Управление должностями'
        ];

        require __DIR__ . '/../Views/role.php';
    }

    public function employee(): void
    {
        Auth::requireRole('employee');

        $heading = 'Личный кабинет сотрудника';
        $message = 'Раздел персональной информации.';

        $links = [
            '/?page=leaves' => 'Заявки на отпуск'
        ];

        require __DIR__ . '/../Views/role.php';
    }
}