<?php

require_once __DIR__ . '/../Core/Auth.php';
require_once __DIR__ . '/../Models/Department.php';

class DepartmentController
{
    public function index(): void
    {
        Auth::requireRole('admin');

        $model = new Department();

        $error = '';
        $editing = null;

        if (isset($_GET['edit'])) {
            $id = filter_var(
                $_GET['edit'],
                FILTER_VALIDATE_INT
            );

            $editing = $id
                ? $model->findById($id)
                : null;

            if (!$editing) {
                http_response_code(404);
                exit('Подразделение не найдено');
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

            $id = filter_var(
                $_POST['id'] ?? '',
                FILTER_VALIDATE_INT
            );

            if ($id === false || $id < 0) {
                http_response_code(400);
                exit('Некорректный идентификатор');
            }

            if ($id > 0 && !$model->findById($id)) {
                http_response_code(404);
                exit('Подразделение не найдено');
            }

            if ($action === 'delete') {
                if ($id === 0) {
                    http_response_code(400);
                    exit('Некорректный идентификатор');
                }

                try {
                    $model->delete($id);

                    header('Location: /?page=departments');
                    exit;

                } catch (PDOException $e) {
                    if ((string) $e->getCode() === '23000') {
                        $error = 'Нельзя удалить подразделение, '
                            . 'к которому привязаны сотрудники.';
                    } else {
                        error_log($e->getMessage());
                        $error = 'Ошибка удаления подразделения.';
                    }
                }

            } elseif ($action === 'save') {
                $data = [
                    'name' => $this->field('name'),
                    'description' => $this->field('description')
                ];

                $editing = $data;
                $editing['id'] = $id;

                if ($data['name'] === '') {
                    $error = 'Введите название подразделения.';

                } elseif (mb_strlen($data['name']) > 100) {
                    $error = 'Название слишком длинное.';

                } elseif (mb_strlen($data['description']) > 1000) {
                    $error = 'Описание слишком длинное.';

                } else {
                    try {
                        if ($id > 0) {
                            $model->update($id, $data);
                        } else {
                            $model->create($data);
                        }

                        header('Location: /?page=departments');
                        exit;

                    } catch (PDOException $e) {
                        if ((string) $e->getCode() === '23000') {
                            $error = 'Подразделение с таким '
                                . 'названием уже существует.';
                        } else {
                            error_log($e->getMessage());
                            $error = 'Ошибка сохранения данных.';
                        }
                    }
                }

            } else {
                http_response_code(400);
                exit('Неизвестное действие');
            }
        }

        $departments = $model->getAll();

        require __DIR__ . '/../Views/departments.php';
    }

    private function field(string $name): string
    {
        $value = $_POST[$name] ?? '';

        return is_string($value) ? trim($value) : '';
    }
}