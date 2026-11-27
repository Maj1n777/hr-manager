<?php

$h = static fn($value): string =>
htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Личный кабинет — HR Manager</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
        }

        header {
            background: white;
            padding: 20px 30px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        .brand {
            font-size: 22px;
            font-weight: bold;
        }

        .container {
            max-width: 1100px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
        }

        .profile-name {
            font-size: 25px;
            margin: 0 0 8px;
        }

        .muted {
            color: #64748b;
        }

        .active {
            color: #15803d;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 25px;
            margin-top: 25px;
            overflow-wrap: anywhere;
        }

        .label {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 7px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 15px;
        }

        .stat-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
        }

        .stat-number {
            font-size: 32px;
            font-weight: bold;
            margin-top: 10px;
        }

        .total {
            color: #2563eb;
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

        .button {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 13px 20px;
            border-radius: 7px;
            text-decoration: none;
            margin-top: 25px;
        }

        .button:hover {
            background: #1d4ed8;
        }

        .logout {
            border: 1px solid #cbd5e1;
            background: white;
            border-radius: 6px;
            padding: 9px 14px;
            cursor: pointer;
        }

        @media (max-width: 700px) {
            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .profile-grid {
                grid-template-columns: 1fr;
            }

            header {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="brand">HR Manager</div>

    <span class="muted">
        <?= $h($user['username']) ?> — Сотрудник
    </span>

    <form method="post" action="/?page=logout">

        <input type="hidden" name="csrf_token"
               value="<?= $h($_SESSION['csrf_token']) ?>">

        <button class="logout" type="submit">
            Выйти
        </button>

    </form>
</header>

<main class="container">

    <a href="/">На главную</a>

    <h1>Личный кабинет сотрудника</h1>

    <div class="card">

        <h2 class="profile-name">
            <?= $h($employee['full_name']) ?>
        </h2>

        <p class="muted">
            <?= $h($employee['position_name']) ?>
        </p>

        <p class="active">● Работает</p>

        <hr>

        <div class="profile-grid">

            <div>
                <div class="label">Подразделение</div>
                <?= $h($employee['department_name']) ?>
            </div>

            <div>
                <div class="label">Дата трудоустройства</div>
                <?= $h(
                    date(
                        'd.m.Y',
                        strtotime($employee['hire_date'])
                    )
                ) ?>
            </div>

            <div>
                <div class="label">Электронная почта</div>
                <?= $h($employee['email']) ?>
            </div>

            <div>
                <div class="label">Телефон</div>
                <?= $h($employee['phone'] ?: 'Не указан') ?>
            </div>

        </div>

    </div>

    <h2>Мои заявки на отпуск</h2>

    <div class="stats-grid">

        <div class="stat-card">
            <div class="label">Всего заявок</div>
            <div class="stat-number total">
                <?= $stats['total'] ?>
            </div>
        </div>

        <div class="stat-card">
            <div class="label">На рассмотрении</div>
            <div class="stat-number pending">
                <?= $stats['pending'] ?>
            </div>
        </div>

        <div class="stat-card">
            <div class="label">Одобрено</div>
            <div class="stat-number approved">
                <?= $stats['approved'] ?>
            </div>
        </div>

        <div class="stat-card">
            <div class="label">Отклонено</div>
            <div class="stat-number rejected">
                <?= $stats['rejected'] ?>
            </div>
        </div>

    </div>

    <a class="button" href="/?page=leaves">
        Перейти к заявкам на отпуск
    </a>

</main>

</body>
</html>