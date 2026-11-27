<?php

require_once __DIR__ . '/../Core/Auth.php';
require_once __DIR__ . '/../Models/LeaveRequest.php';

class LeaveController
{
    public function index(): void
    {
        $user = Auth::requireRole('employee');

        $employeeId = (int) $user['employee_id'];

        $model = new LeaveRequest();

        $error = '';

        $form = [
            'start_date' => '',
            'end_date' => '',
            'reason' => ''
        ];

        $today = new DateTimeImmutable(
            'today',
            new DateTimeZone('Europe/Moscow')
        );

        $minDate = $today->format('Y-m-d');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';

            if (!is_string($token) ||
                !hash_equals($_SESSION['csrf_token'], $token)) {
                http_response_code(403);
                exit('Недействительный запрос');
            }

            $form = [
                'start_date' => $this->field('start_date'),
                'end_date' => $this->field('end_date'),
                'reason' => $this->field('reason')
            ];

            if (!$this->validDate($form['start_date']) ||
                !$this->validDate($form['end_date'])) {

                $error = 'Укажите корректные даты отпуска.';

            } elseif ($form['start_date'] < $minDate) {

                $error = 'Дата начала отпуска не может '
                    . 'быть в прошлом.';

            } elseif ($form['end_date'] < $form['start_date']) {

                $error = 'Дата окончания не может быть '
                    . 'раньше даты начала.';

            } elseif (mb_strlen($form['reason']) > 1000) {

                $error = 'Комментарий слишком длинный.';

            } else {
                try {
                    $created = $model->create(
                        $employeeId,
                        $form['start_date'],
                        $form['end_date'],
                        $form['reason']
                    );

                    if (!$created) {
                        $error = 'Выбранный период пересекается '
                            . 'с существующей заявкой '
                            . 'или сотрудник неактивен.';
                    } else {
                        header('Location: /?page=leaves&success=1');
                        exit;
                    }

                } catch (PDOException $e) {
                    error_log($e->getMessage());
                    $error = 'Ошибка сохранения заявки.';
                }
            }
        }

        $requests = $model->getByEmployee($employeeId);

        require __DIR__ . '/../Views/leaves.php';
    }

    public function review(): void
    {
        $user = Auth::requireRole('admin');

        $model = new LeaveRequest();

        $error = '';
        $success = $_SESSION['leave_review_success'] ?? '';

        unset($_SESSION['leave_review_success']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';

            if (!is_string($token) ||
                !hash_equals($_SESSION['csrf_token'], $token)) {
                http_response_code(403);
                exit('Недействительный запрос');
            }

            $id = filter_var(
                $_POST['id'] ?? '',
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            );

            $status = $_POST['status'] ?? '';

            if ($id === false ||
                !in_array(
                    $status,
                    ['approved', 'rejected'],
                    true
                )) {
                http_response_code(400);
                exit('Некорректные данные запроса');
            }

            try {
                $updated = $model->review(
                    $id,
                    $status,
                    (int) $user['id']
                );

                if ($updated) {
                    $_SESSION['leave_review_success'] =
                        $status === 'approved'
                            ? 'Заявка успешно одобрена.'
                            : 'Заявка успешно отклонена.';

                    header('Location: /?page=leave_review');
                    exit;
                }

                $error = 'Заявка уже рассмотрена, не существует '
                    . 'или сотрудник находится в архиве.';

            } catch (PDOException $e) {
                error_log($e->getMessage());
                $error = 'Ошибка обработки заявки.';
            }
        }

        $requests = $model->getAll();

        require __DIR__ . '/../Views/leave_review.php';
    }

    private function field(string $name): string
    {
        $value = $_POST[$name] ?? '';

        return is_string($value) ? trim($value) : '';
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