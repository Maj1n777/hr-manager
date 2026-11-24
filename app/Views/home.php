<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($data['title']) ?></title>
</head>
<body>
<h1><?= htmlspecialchars($data['title']) ?></h1>
<p><?= htmlspecialchars($data['description']) ?></p>
<p><?= htmlspecialchars($data['status']) ?></p>
</body>
</html>