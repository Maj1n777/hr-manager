<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($data['title']) ?></title>
</head>
<body>

<h1><?= htmlspecialchars($data['title']) ?></h1>

<p><?= htmlspecialchars($data['description']) ?></p>

<p>
    Добро пожаловать,
    <?= htmlspecialchars($user['username']) ?>!
</p>

<p>
    Ваша роль:
    <?= $user['role'] === 'admin'
            ? 'Администратор'
            : 'Сотрудник' ?>
</p>

<hr>

<?php if ($user['role'] === 'admin'): ?>

    <h2>Управление персоналом</h2>

    <a href="/?page=admin">
        Панель администратора
    </a>

<?php else: ?>

    <h2>Самообслуживание сотрудника</h2>

    <a href="/?page=employee">
        Личный кабинет
    </a>

<?php endif; ?>

<hr>

<form method="post" action="/?page=logout">
    <input type="hidden" name="csrf_token"
           value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

    <button type="submit">Выйти</button>
</form>

</body>
</html>