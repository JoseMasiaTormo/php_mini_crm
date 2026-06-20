<?php 
session_start();

// Protección: si no hay sesión, te manda al login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'db.php';
$db = getDB();

// GET: recuperar tareas del usuario logeado
$stmt = $db->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Tareas</title>
</head>
<body>
    <h1>Hola, <?= htmlspecialchars($_SESSION['username']) ?></h1>
    <a href="logout.php">Cerrar Sesión</a>

    <h2>Nueva Tarea</h2>
    <form method="POST" action="new_task.php">
        <input type="text" name="title" placeholder="Escribe una tarea..." required>
        <button type="submit">Añadir</button>
    </form>

    <h2>Mis tareas</h2>
    <?php if (empty($tasks)): ?>
        <p>No tienes tareas aún.</p>
    <?php else: ?>
        <ul>
        <?php foreach ($tasks as $task): ?>
            <li>
                <?= htmlspecialchars($task['title']) ?>
                <small>(<?= $task['created_at'] ?>)</small>
                <a href="remove_task.php?id=<?= $task['id'] ?>">Borrar</a>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>