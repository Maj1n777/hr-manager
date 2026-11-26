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
    <title>Подразделения — HR Manager</title>

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

<h1>Управление подразделениями</h1>

<a href="/?page=admin">Панель администратора</a>

<div class="form-container">

    <h2>
        <?= $isEditing
            ? 'Редактирование подразделения'
            : 'Добавление подразделения' ?>
    </h2>

    <?php if ($error): ?>
        <p class="error"><?= $h($error) ?></p>
    <?php endif; ?>

    <form method="post" action="/?page=departments">

        <input type="hidden" name="csrf_token"
               value="<?= $h($_SESSION['csrf_token']) ?>">

        <input type="hidden" name="action" value="save">

        <input type="hidden" name="id"
               value="<?= $h($form['id'] ?? 0) ?>">

        <label for="name">Название подразделения</label>

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
                <a href="/?page=departments">Отмена</a>
            <?php endif; ?>
        </p>

    </form>
</div>

<h2>Список подразделений</h2>

<p>Всего подразделений: <?= count($departments) ?></p>

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

    <?php foreach ($departments as $department): ?>

        <tr>
            <td><?= $h($department['id']) ?></td>
            <td><?= $h($department['name']) ?></td>
            <td><?= $h($department['description']) ?></td>
            <td><?= $h($department['employee_count']) ?></td>

            <td>
                <a href="/?page=departments&edit=<?= $h($department['id']) ?>">
                    Изменить
                </a>

                <form class="inline" method="post"
                      action="/?page=departments"
                      onsubmit="return confirm('Удалить подразделение?')">

                    <input type="hidden" name="csrf_token"
                           value="<?= $h($_SESSION['csrf_token']) ?>">

                    <input type="hidden" name="action"
                           value="delete">

                    <input type="hidden" name="id"
                           value="<?= $h($department['id']) ?>">

                    <button type="submit">Удалить</button>

                </form>
            </td>
        </tr>

    <?php endforeach; ?>

    <?php if (!$departments): ?>
        <tr>
            <td colspan="5">Подразделения отсутствуют.</td>
        </tr>
    <?php endif; ?>

    </tbody>
</table>

</body>
</html>