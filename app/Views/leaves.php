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

    <title>Заявки на отпуск — HR Manager</title>

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

        label {
            display: block;
            margin-top: 12px;
            margin-bottom: 5px;
        }

        input, textarea {
            padding: 8px;
            width: 400px;
            max-width: 100%;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
        }

        button {
            padding: 10px 16px;
            cursor: pointer;
        }

        .form-container {
            background: #f8fafc;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }

        .error {
            color: #b91c1c;
        }

        .success {
            color: #15803d;
        }

        .pending {
            color: #b45309;
        }

        .approved {
            color: #15803d;
        }

        .rejected {
            color: #b91c1c;
        }
    </style>
</head>
<body>

<h1>Заявки на отпуск</h1>

<a href="/?page=employee">Личный кабинет</a>

<div class="form-container">

    <h2>Новая заявка</h2>

    <?php if ($error): ?>
        <p class="error"><?= $h($error) ?></p>
    <?php endif; ?>

    <?php if (($_GET['success'] ?? '') === '1'): ?>
        <p class="success">
            Заявка успешно отправлена.
        </p>
    <?php endif; ?>

    <form method="post" action="/?page=leaves">

        <input type="hidden" name="csrf_token"
               value="<?= $h($_SESSION['csrf_token']) ?>">

        <label for="start_date">Дата начала отпуска</label>

        <input type="date"
               id="start_date"
               name="start_date"
               min="<?= $h($minDate) ?>"
               value="<?= $h($form['start_date']) ?>"
               required>

        <label for="end_date">Дата окончания отпуска</label>

        <input type="date"
               id="end_date"
               name="end_date"
               min="<?= $h($minDate) ?>"
               value="<?= $h($form['end_date']) ?>"
               required>

        <label for="reason">Комментарий (необязательно)</label>

        <textarea id="reason"
                  name="reason"
                  maxlength="1000"
                  rows="4"><?= $h($form['reason']) ?></textarea>

        <p>
            <button type="submit">
                Отправить заявку
            </button>
        </p>

    </form>
</div>

<h2>Мои заявки</h2>

<p>Всего заявок: <?= count($requests) ?></p>

<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Дата начала</th>
        <th>Дата окончания</th>
        <th>Комментарий</th>
        <th>Статус</th>
        <th>Дата подачи</th>
    </tr>
    </thead>

    <tbody>

    <?php foreach ($requests as $request): ?>

        <tr>
            <td><?= $h($request['id']) ?></td>
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
        </tr>

    <?php endforeach; ?>

    <?php if (!$requests): ?>
        <tr>
            <td colspan="6">
                У вас пока нет заявок на отпуск.
            </td>
        </tr>
    <?php endif; ?>

    </tbody>
</table>

</body>
</html>