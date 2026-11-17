<?php
require '../config.php';
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

if (isset($_GET['action'], $_GET['id']) && in_array($_GET['action'], ['approved', 'rejected'])) {
    $id = (int)$_GET['id'];
    $st = $mysqli->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $st->bind_param('si', $_GET['action'], $id);
    $st->execute();
    header('Location: index.php'); exit;
}

$bookings = $mysqli->query(
    "SELECT b.*, r.name AS room_name FROM bookings b
     JOIN rooms r ON r.id = b.room_id
     ORDER BY FIELD(b.status,'pending','approved','rejected'), b.created_at DESC"
)->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><title>Модерация</title><link rel="stylesheet" href="../style.css"></head>
<body>
<header>
    <div><h1>Админ-панель</h1><p>Модерирование заявок</p></div>
    <nav><a href="../index.php">Сайт</a><a href="logout.php">Выйти</a></nav>
</header>
<div class="container">
    <h2>Заявки на бронирование</h2>
    <table class="admin-table">
        <tr><th>ID</th><th>Номер</th><th>Гость</th><th>Телефон</th><th>E-mail</th>
            <th>Заезд</th><th>Выезд</th><th>Статус</th><th>Действия</th></tr>
        <?php foreach ($bookings as $b): ?>
        <tr>
            <td><?= (int)$b['id'] ?></td>
            <td><?= htmlspecialchars($b['room_name']) ?></td>
            <td><?= htmlspecialchars($b['last_name'] . ' ' . $b['first_name']) ?></td>
            <td><?= htmlspecialchars($b['phone']) ?></td>
            <td><?= htmlspecialchars($b['email']) ?></td>
            <td><?= date('d.m.Y', strtotime($b['check_in'])) ?></td>
            <td><?= date('d.m.Y', strtotime($b['check_out'])) ?></td>
            <td><?php
                echo ['pending'=>'На рассмотрении','approved'=>'Одобрена','rejected'=>'Отклонена'][$b['status']];
            ?></td>
            <td>
                <?php if ($b['status'] === 'pending'): ?>
                    <a class="btn" style="width:auto; padding:5px 10px;" href="?action=approved&id=<?= (int)$b['id'] ?>">Одобрить</a>
                    <a class="btn" style="width:auto; padding:5px 10px; background:#c0392b;" href="?action=rejected&id=<?= (int)$b['id'] ?>">Отклонить</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
