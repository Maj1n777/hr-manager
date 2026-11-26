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
    <meta name="viewport"
          content="width=device-width, initial-scale=1">
    <title>Должности — HR Manager</title>

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
            padding: 8px 14px;
            cursor: pointer;
        }

        .error {
            color: #b91c1c;
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

<h1>Управление должностями</h1>

<a href="/?page=admin">Панель администратора</a>

<div class="form-container">

    <h2>
        <?= $isEditing
            ? 'Редактирование должности'
            : 'Добавление должности' ?>
    </h2>

    <?php if ($error): ?>
        <p class="error"><?= $h($error) ?></p>
    <?php endif; ?>

    <form method="post" action="/?page=positions">

        <input type="hidden" name="csrf_token"
               value="<?= $h($_SESSION['csrf_token']) ?>">

        <input type="hidden" name="action" value="save">

        <input type="hidden" name="id"
               value="<?= $h($form['id'] ?? 0) ?>">

        <label for="name">Название должности</label>

        <input id="name" name="name"
               maxlength="100"
               value="<?= $h($form['name'] ?? '') ?>"
               required>

        <label for="description">Описание</label>

        <textarea id="description" name="description"
                  maxlength="1000"
                  rows="4"><?= $h($form['description'] ?? '') ?></textarea>

        <p>
            <button type="submit">
                <?= $isEditing ? 'Сохранить' : 'Добавить' ?>
            </button>

            <?php if ($isEditing): ?>
                <a href="/?page=positions">Отмена</a>
            <?php endif; ?>
        </p>

    </form>
</div>

<h2>Список должностей</h2>

<p>Всего должностей: <?= count($positions) ?></p>

<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Название</th>
        <th>Описание</th>
        <th>Количество сотрудников</th>
        <th>Действия</th>
    </tr>
    </thead>

    <tbody>

    <?php foreach ($positions as $position): ?>

        <tr>
            <td><?= $h($position['id']) ?></td>
            <td><?= $h($position['name']) ?></td>
            <td><?= $h($position['description']) ?></td>
            <td><?= $h($position['employee_count']) ?></td>

            <td>
                <a href="/?page=positions&edit=<?= $h($position['id']) ?>">
                    Изменить
                </a>

                <form class="inline" method="post"
                      action="/?page=positions"
                      onsubmit="return confirm('Удалить должность?')">

                    <input type="hidden" name="csrf_token"
                           value="<?= $h($_SESSION['csrf_token']) ?>">

                    <input type="hidden" name="action"
                           value="delete">

                    <input type="hidden" name="id"
                           value="<?= $h($position['id']) ?>">

                    <button type="submit">Удалить</button>

                </form>
            </td>
        </tr>

    <?php endforeach; ?>

    <?php if (!$positions): ?>
        <tr>
            <td colspan="5">Должности отсутствуют.</td>
        </tr>
    <?php endif; ?>

    </tbody>
</table>

</body>
</html>