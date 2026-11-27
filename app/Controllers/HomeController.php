<?php

require_once __DIR__ . '/../Models/AppInfo.php';
require_once __DIR__ . '/../Models/Employee.php';
require_once __DIR__ . '/../Models/LeaveRequest.php';
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
            '/?page=positions' => 'Управление должностями',
            '/?page=leave_review' => 'Рассмотрение заявок на отпуск'
        ];

        require __DIR__ . '/../Views/role.php';
    }

    public function employee(): void
    {
        $user = Auth::requireRole('employee');

        $employeeId = (int) $user['employee_id'];

        $employeeModel = new Employee();
        $employee = $employeeModel->getProfile($employeeId);

        if (!$employee) {
            http_response_code(404);
            exit('Информация о сотруднике не найдена');
        }

        $leaveModel = new LeaveRequest();
        $requests = $leaveModel->getByEmployee($employeeId);

        $stats = [
            'total' => count($requests),
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0
        ];

        foreach ($requests as $request) {
            $status = $request['status'];

            if (isset($stats[$status])) {
                $stats[$status]++;
            }
        }

        require __DIR__ . '/../Views/employee_profile.php';
    }
}