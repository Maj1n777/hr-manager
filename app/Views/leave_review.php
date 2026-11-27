<?php

$h = static fn($value): string =>
htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$statuses = [
    'pending' => 'На рассмотрении',
    'approved' => 'Одобрена',
    'rejected' => 'Отклонена'
];

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Рассмотрение заявок — HR Manager</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            color: #1e293b;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #cbd5e1;
            padding: 12px;
            text-align: left;
        }

        th {
            background: #f1f5f9;
        }

        button {
            padding: 8px 12px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            color: white;
            margin: 3px 0;
        }

        .approve-button {
            background: #15803d;
        }

        .reject-button {
            background: #b91c1c;
        }

        .approve-button:disabled {
            background: #94a3b8;
            cursor: not-allowed;
        }

        .error, .rejected {
            color: #b91c1c;
        }

        .success, .approved {
            color: #15803d;
        }

        .pending {
            color: #b45309;
        }

        .muted {
            color: #64748b;
        }

        .inline {
            display: inline;
        }
    </style>
</head>
<body>

<h1>Рассмотрение заявок на отпуск</h1>

<a href="/?page=admin">Панель администратора</a>

<?php if ($error): ?>
    <p class="error"><?= $h($error) ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p class="success"><?= $h($success) ?></p>
<?php endif; ?>

<h2>Все заявки сотрудников</h2>

<p>Всего заявок: <?= count($requests) ?></p>

<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Сотрудник</th>
        <th>Подразделение</th>
        <th>Дата начала</th>
        <th>Дата окончания</th>
        <th>Комментарий</th>
        <th>Статус</th>
        <th>Дата подачи (UTC)</th>
        <th>Рассмотрел</th>
        <th>Дата рассмотрения (UTC)</th>
        <th>Действия</th>
    </tr>
    </thead>

    <tbody>

    <?php foreach ($requests as $request): ?>

        <tr>
            <td><?= $h($request['id']) ?></td>

            <td>
                <?= $h($request['employee_name']) ?>

                <?php if (!$request['employee_active']): ?>
                    <small class="muted">(В архиве)</small>
                <?php endif; ?>
            </td>

            <td><?= $h($request['department_name']) ?></td>
            <td><?= $h($request['start_date']) ?></td>
            <td><?= $h($request['end_date']) ?></td>
            <td><?= $h($request['reason']) ?></td>

            <td class="<?= $h($request['status']) ?>">
                <?= $h(
                    $statuses[$request['status']]
                    ?? $request['status']
                ) ?>
            </td>

            <td><?= $h($request['created_at']) ?></td>
            <td><?= $h($request['reviewer_name'] ?? '—') ?></td>
            <td><?= $h($request['reviewed_at'] ?? '—') ?></td>

            <td>

                <?php if ($request['status'] === 'pending'): ?>

                    <form class="inline"
                          method="post"
                          action="/?page=leave_review"
                          onsubmit="return confirm('Одобрить заявку?')">

                        <input type="hidden"
                               name="csrf_token"
                               value="<?= $h($_SESSION['csrf_token']) ?>">

                        <input type="hidden"
                               name="id"
                               value="<?= $h($request['id']) ?>">

                        <input type="hidden"
                               name="status"
                               value="approved">

                        <button class="approve-button"
                                type="submit"
                            <?= !$request['employee_active']
                                ? 'disabled' : '' ?>>
                            Одобрить
                        </button>

                    </form>

                    <form class="inline"
                          method="post"
                          action="/?page=leave_review"
                          onsubmit="return confirm('Отклонить заявку?')">

                        <input type="hidden"
                               name="csrf_token"
                               value="<?= $h($_SESSION['csrf_token']) ?>">

                        <input type="hidden"
                               name="id"
                               value="<?= $h($request['id']) ?>">

                        <input type="hidden"
                               name="status"
                               value="rejected">

                        <button class="reject-button"
                                type="submit">
                            Отклонить
                        </button>

                    </form>

                <?php else: ?>

                    <span class="muted">Рассмотрена</span>

                <?php endif; ?>

            </td>
        </tr>

    <?php endforeach; ?>

    <?php if (!$requests): ?>
        <tr>
            <td colspan="11">Заявки отсутствуют.</td>
        </tr>
    <?php endif; ?>

    </tbody>
</table>

</body>
</html>