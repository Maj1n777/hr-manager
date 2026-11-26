<?php

$h = static fn($value): string =>
htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$form = $editing ?? [];
$isEditing = !empty($form['id']);

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Сотрудники — HR Manager</title>

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
            padding: 10px;
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

        input, select {
            padding: 8px;
            width: 320px;
            max-width: 100%;
            box-sizing: border-box;
        }

        button {
            padding: 8px 14px;
            cursor: pointer;
        }

        .error {
            color: #b91c1c;
        }

        .active {
            color: #15803d;
        }

        .archived {
            color: #64748b;
        }

        .inline {
            display: inline;
        }

        .form-container {
            background: #f8fafc;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<h1>Управление сотрудниками</h1>

<a href="/?page=admin">Панель администратора</a>

<div class="form-container">

    <h2>
        <?= $isEditing
            ? 'Редактирование сотрудника'
            : 'Добавление сотрудника' ?>
    </h2>

    <?php if ($error): ?>
        <p class="error"><?= $h($error) ?></p>
    <?php endif; ?>

    <form method="post" action="/?page=employees">

        <input type="hidden" name="csrf_token"
               value="<?= $h($_SESSION['csrf_token']) ?>">

        <input type="hidden" name="action" value="save">

        <input type="hidden" name="id"
               value="<?= $h($form['id'] ?? 0) ?>">

        <label for="full_name">ФИО</label>
        <input id="full_name" name="full_name"
               value="<?= $h($form['full_name'] ?? '') ?>"
               required>

        <label for="email">Электронная почта</label>
        <input type="email" id="email" name="email"
               value="<?= $h($form['email'] ?? '') ?>"
               required>

        <label for="phone">Телефон</label>
        <input type="tel" id="phone" name="phone"
               value="<?= $h($form['phone'] ?? '') ?>">

        <label for="hire_date">Дата приёма на работу</label>
        <input type="date" id="hire_date" name="hire_date"
               value="<?= $h($form['hire_date'] ?? '') ?>"
               required>

        <label for="department_id">Подразделение</label>

        <select id="department_id" name="department_id" required>
            <option value="">Выберите подразделение</option>

            <?php foreach ($departments as $department): ?>

                <option value="<?= $h($department['id']) ?>"
                    <?= (int) ($form['department_id'] ?? 0)
                    === (int) $department['id']
                        ? 'selected' : '' ?>>

                    <?= $h($department['name']) ?>

                </option>

            <?php endforeach; ?>
        </select>

        <label for="position_id">Должность</label>

        <select id="position_id" name="position_id" required>
            <option value="">Выберите должность</option>

            <?php foreach ($positions as $position): ?>

                <option value="<?= $h($position['id']) ?>"
                    <?= (int) ($form['position_id'] ?? 0)
                    === (int) $position['id']
                        ? 'selected' : '' ?>>

                    <?= $h($position['name']) ?>

                </option>

            <?php endforeach; ?>
        </select>

        <p>
            <button type="submit">
                <?= $isEditing ? 'Сохранить' : 'Добавить' ?>
            </button>

            <?php if ($isEditing): ?>
                <a href="/?page=employees">Отмена</a>
            <?php endif; ?>
        </p>

    </form>
</div>

<h2>Список сотрудников</h2>

<p>Всего записей: <?= count($employees) ?></p>

<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>ФИО</th>
        <th>Почта</th>
        <th>Телефон</th>
        <th>Подразделение</th>
        <th>Должность</th>
        <th>Дата приёма</th>
        <th>Статус</th>
        <th>Действия</th>
    </tr>
    </thead>

    <tbody>

    <?php foreach ($employees as $employee): ?>

        <tr>
            <td><?= $h($employee['id']) ?></td>
            <td><?= $h($employee['full_name']) ?></td>
            <td><?= $h($employee['email']) ?></td>
            <td><?= $h($employee['phone']) ?></td>
            <td><?= $h($employee['department_name']) ?></td>
            <td><?= $h($employee['position_name']) ?></td>
            <td><?= $h($employee['hire_date']) ?></td>

            <td class="<?= $employee['is_active']
                ? 'active' : 'archived' ?>">

                <?= $employee['is_active']
                    ? 'Работает' : 'В архиве' ?>

            </td>

            <td>
                <a href="/?page=employees&edit=<?= $h($employee['id']) ?>">
                    Изменить
                </a>

                <form class="inline" method="post"
                      action="/?page=employees">

                    <input type="hidden" name="csrf_token"
                           value="<?= $h($_SESSION['csrf_token']) ?>">

                    <input type="hidden" name="id"
                           value="<?= $h($employee['id']) ?>">

                    <input type="hidden" name="action"
                           value="<?= $employee['is_active']
                               ? 'archive' : 'restore' ?>">

                    <button type="submit">
                        <?= $employee['is_active']
                            ? 'Архивировать' : 'Восстановить' ?>
                    </button>

                </form>
            </td>
        </tr>

    <?php endforeach; ?>

    <?php if (!$employees): ?>
        <tr>
            <td colspan="9">Сотрудники отсутствуют.</td>
        </tr>
    <?php endif; ?>

    </tbody>
</table>

</body>
</html>