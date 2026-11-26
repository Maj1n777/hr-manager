<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($data['title']) ?></title>
</head>
<body>

<h1><?= htmlspecialchars($data['title']) ?></h1>

<p><?= htmlspecialchars($data['description']) ?></p>

<p>
    Добро пожаловать,
    <?= htmlspecialchars($_SESSION['username']) ?>!
</p>

<p><?= htmlspecialchars($data['status']) ?></p>

<form method="post" action="/?page=logout">
    <input type="hidden" name="csrf_token"
           value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

    <button type="submit">Выйти</button>
</form>

</body>
</html>