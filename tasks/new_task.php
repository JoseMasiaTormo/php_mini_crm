<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'db.php';

$title = trim($_POST['title'] ?? '');

if ($title) {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO tasks (user_id, title) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $title]);
}

header("Location: index.php");
exit;
?>