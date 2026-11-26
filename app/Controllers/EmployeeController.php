<?php

require_once __DIR__ . '/../Core/Auth.php';
require_once __DIR__ . '/../Models/Employee.php';

class EmployeeController
{
    public function index(): void
    {
        Auth::requireRole('admin');

        $model = new Employee();
        $departments = $model->getDepartments();
        $positions = $model->getPositions();

        $error = '';
        $editing = null;

        if (isset($_GET['edit'])) {
            $editing = $model->findById(
                $this->getId($_GET['edit'])
            );

            if (!$editing) {
                http_response_code(404);
                exit('Сотрудник не найден');
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';

            if (!is_string($token) ||
                !hash_equals($_SESSION['csrf_token'], $token)) {
                http_response_code(403);
                exit('Недействительный запрос');
            }

            $action = $_POST['action'] ?? '';
            $id = $this->getId($_POST['id'] ?? 0);

            if ($action === 'archive' || $action === 'restore') {
                if (!$model->findById($id)) {
                    http_response_code(404);
                    exit('Сотрудник не найден');
                }

                $model->setActive($id, $action === 'restore');

                header('Location: /?page=employees');
                exit;
            }

            if ($action !== 'save') {
                http_response_code(400);
                exit('Неизвестное действие');
            }

            if ($id > 0 && !$model->findById($id)) {
                http_response_code(404);
                exit('Сотрудник не найден');
            }

            $data = [
                'full_name' => $this->field('full_name'),
                'email' => $this->field('email'),
                'phone' => $this->field('phone'),
                'hire_date' => $this->field('hire_date'),
                'department_id' => $this->getId(
                    $_POST['department_id'] ?? 0
                ),
                'position_id' => $this->getId(
                    $_POST['position_id'] ?? 0
                )
            ];

            $editing = $data;
            $editing['id'] = $id;

            $departmentIds = array_map(
                'intval',
                array_column($departments, 'id')
            );

            $positionIds = array_map(
                'intval',
                array_column($positions, 'id')
            );

            if ($data['full_name'] === '') {
                $error = 'Введите ФИО сотрудника.';
            } elseif (!filter_var(
                $data['email'],
                FILTER_VALIDATE_EMAIL
            )) {
                $error = 'Введите корректную электронную почту.';
            } elseif (!$this->validDate($data['hire_date'])) {
                $error = 'Укажите корректную дату приёма.';
            } elseif (!in_array(
                $data['department_id'],
                $departmentIds,
                true
            )) {
                $error = 'Выберите подразделение.';
            } elseif (!in_array(
                $data['position_id'],
                $positionIds,
                true
            )) {
                $error = 'Выберите должность.';
            } else {
                try {
                    if ($id > 0) {
                        $model->update($id, $data);
                    } else {
                        $model->create($data);
                    }

                    header('Location: /?page=employees');
                    exit;

                } catch (PDOException $e) {
                    if ((string) $e->getCode() === '23000') {
                        $error = 'Нарушено ограничение базы данных. '
                            . 'Проверьте уникальность электронной почты.';
                    } else {
                        error_log($e->getMessage());
                        $error = 'Ошибка сохранения данных.';
                    }
                }
            }
        }

        $employees = $model->getAll();

        require __DIR__ . '/../Views/employees.php';
    }

    private function field(string $name): string
    {
        $value = $_POST[$name] ?? '';

        return is_string($value) ? trim($value) : '';
    }

    private function getId(mixed $value): int
    {
        $id = filter_var(
            $value,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        return $id === false ? 0 : $id;
    }

    private function validDate(string $value): bool
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/D', $value)) {
            return false;
        }

        [$year, $month, $day] = explode('-', $value);

        return checkdate(
            (int) $month,
            (int) $day,
            (int) $year
        );
    }
}