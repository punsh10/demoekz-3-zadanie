<?php
require 'config.php';
$login = 'admin';
$pass  = 'admin123';
$hash  = password_hash($pass, PASSWORD_DEFAULT);
$stmt  = $mysqli->prepare("INSERT INTO admins (login, password_hash) VALUES (?, ?)");
$stmt->bind_param('ss', $login, $hash);
if ($stmt->execute()) echo "Админ создан: $login / $pass. УДАЛИТЕ этот файл!";
else echo "Ошибка: " . $stmt->error;
?>
