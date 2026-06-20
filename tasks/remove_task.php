<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'db.php';

// $_GET contiene los parámetros de la URL (?id=5)
$id = (int) ($_GET['id'] ?? 0);

if ($id > 0) {
    $db = getDB();
    // El AND user_id es CRÍTICO: evita que un usuario borre tareas de otro
    $stmt = $db->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
}

header("Location: index.php");
exit;
?>