<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($heading) ?></title>
</head>
<body>

<h1><?= htmlspecialchars($heading) ?></h1>

<p><?= htmlspecialchars($message) ?></p>

<?php foreach ($links as $url => $label): ?>

    <p>
        <a href="<?= htmlspecialchars($url) ?>">
            <?= htmlspecialchars($label) ?>
        </a>
    </p>

<?php endforeach; ?>

<hr>

<a href="/">На главную</a>

</body>
</html>