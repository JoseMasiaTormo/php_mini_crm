<?php
session_start();

// Protección: si no hay sesión, te manda al login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'db.php';
$db = getDB();

$stmtPending = $db->prepare("SELECT * FROM tasks WHERE user_id = ? AND completed = 0 ORDER BY created_at DESC");
$stmtPending->execute([$_SESSION['user_id']]);
$pendingTasks = $stmtPending->fetchAll(PDO::FETCH_ASSOC);

$stmtDone = $db->prepare("SELECT * FROM tasks WHERE user_id = ? AND completed = 1 ORDER BY created_at DESC");
$stmtDone->execute([$_SESSION['user_id']]);
$doneTasks = $stmtDone->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Tareas — Mini CRM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #f3f4f6;
            color: #1f2937;
            min-height: 100vh;
        }

        /* ── Navbar ── */
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 0 1.5rem;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            color: #fff;
            font-weight: 700;
            font-size: 1.125rem;
            text-decoration: none;
        }

        .navbar-brand svg {
            width: 24px;
            height: 24px;
            fill: rgba(255,255,255,0.9);
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .navbar-user span {
            color: rgba(255,255,255,0.85);
            font-size: 0.875rem;
        }

        .navbar-user strong {
            color: #fff;
        }

        .btn-logout {
            background: rgba(255,255,255,0.15);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 6px;
            padding: 0.375rem 0.875rem;
            font-size: 0.8125rem;
            font-family: inherit;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.15s;
        }

        .btn-logout:hover {
            background: rgba(255,255,255,0.25);
        }

        /* ── Main layout ── */
        .container {
            max-width: 680px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        /* ── Add task card ── */
        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08), 0 4px 16px rgba(0,0,0,0.06);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .card h2 {
            font-size: 1rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 1rem;
        }

        .add-form {
            display: flex;
            gap: 0.75rem;
        }

        .add-form input[type="text"] {
            flex: 1;
            padding: 0.625rem 0.875rem;
            border: 1.5px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.9375rem;
            font-family: inherit;
            color: #1f2937;
            background: #f9fafb;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .add-form input[type="text"]:focus {
            border-color: #667eea;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.15);
        }

        .btn-add {
            padding: 0.625rem 1.25rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 0.9375rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            white-space: nowrap;
            transition: opacity 0.15s, transform 0.1s;
        }

        .btn-add:hover { opacity: 0.9; }
        .btn-add:active { transform: scale(0.97); }

        /* ── Task list ── */
        .tasks-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }

        .tasks-header h2 {
            font-size: 1rem;
            font-weight: 600;
            color: #374151;
        }

        .badge {
            background: #e5e7eb;
            color: #6b7280;
            border-radius: 999px;
            padding: 0.125rem 0.625rem;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .task-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .task-item {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.07);
            padding: 0.875rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.875rem;
            transition: box-shadow 0.15s;
        }

        .task-item:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .task-check {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid #d1d5db;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: border-color 0.15s, background 0.15s;
        }

        .task-check:hover {
            border-color: #667eea;
        }

        .task-check.checked {
            border-color: #667eea;
            background: #667eea;
        }

        .task-check.checked svg {
            width: 12px;
            height: 12px;
            fill: #fff;
        }

        .task-item.completed {
            background: #f9fafb;
            box-shadow: none;
        }

        .task-item.completed .task-title {
            text-decoration: line-through;
            color: #9ca3af;
            font-weight: 400;
        }

        .task-item.completed .task-date {
            color: #d1d5db;
        }

        .tasks-header.completed-header {
            margin-top: 1.5rem;
        }

        .task-body {
            flex: 1;
            min-width: 0;
        }

        .task-title {
            font-size: 0.9375rem;
            color: #1f2937;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .task-date {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 0.125rem;
        }

        .btn-delete {
            background: none;
            border: none;
            color: #d1d5db;
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.15s, background 0.15s;
            text-decoration: none;
            flex-shrink: 0;
        }

        .btn-delete:hover {
            color: #ef4444;
            background: #fef2f2;
        }

        .btn-delete svg {
            width: 18px;
            height: 18px;
            fill: currentColor;
        }

        /* ── Empty state ── */
        .empty-state {
            background: #fff;
            border-radius: 12px;
            padding: 3rem 1.5rem;
            text-align: center;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        }

        .empty-state svg {
            width: 48px;
            height: 48px;
            fill: #d1d5db;
            margin-bottom: 0.75rem;
        }

        .empty-state p {
            color: #9ca3af;
            font-size: 0.9375rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <span class="navbar-brand">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm7 13H5v-.23c0-.62.28-1.2.76-1.58C7.47 15.82 9.64 15 12 15s4.53.82 6.24 2.19c.48.38.76.97.76 1.58V19z"/>
            </svg>
            Mini CRM
        </span>
        <div class="navbar-user">
            <span>Hola, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
            <a href="logout.php" class="btn-logout">Cerrar sesión</a>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <h2>Nueva tarea</h2>
            <form method="POST" action="new_task.php" class="add-form">
                <input type="text" name="title" placeholder="Escribe una tarea..." required>
                <button type="submit" class="btn-add">+ Añadir</button>
            </form>
        </div>

        <div class="tasks-header">
            <h2>Mis tareas</h2>
            <span class="badge"><?= count($pendingTasks) ?></span>
        </div>

        <?php if (empty($pendingTasks) && empty($doneTasks)): ?>
            <div class="empty-state">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 14H7v-2h5v2zm3-4H7v-2h8v2zm0-4H7V7h8v2z"/>
                </svg>
                <p>No tienes tareas aún. ¡Añade una arriba!</p>
            </div>
        <?php else: ?>
            <?php if (!empty($pendingTasks)): ?>
            <ul class="task-list">
            <?php foreach ($pendingTasks as $task): ?>
                <li class="task-item">
                    <a href="toggle_task.php?id=<?= $task['id'] ?>" class="task-check" title="Marcar como completada"></a>
                    <div class="task-body">
                        <div class="task-title"><?= htmlspecialchars($task['title']) ?></div>
                        <div class="task-date"><?= htmlspecialchars($task['created_at']) ?></div>
                    </div>
                    <a href="remove_task.php?id=<?= $task['id'] ?>" class="btn-delete" title="Eliminar tarea">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                        </svg>
                    </a>
                </li>
            <?php endforeach; ?>
            </ul>
            <?php endif; ?>

            <?php if (!empty($doneTasks)): ?>
            <div class="tasks-header completed-header">
                <h2>Completadas</h2>
                <span class="badge"><?= count($doneTasks) ?></span>
            </div>
            <ul class="task-list">
            <?php foreach ($doneTasks as $task): ?>
                <li class="task-item completed">
                    <a href="toggle_task.php?id=<?= $task['id'] ?>" class="task-check checked" title="Marcar como pendiente">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                        </svg>
                    </a>
                    <div class="task-body">
                        <div class="task-title"><?= htmlspecialchars($task['title']) ?></div>
                        <div class="task-date"><?= htmlspecialchars($task['created_at']) ?></div>
                    </div>
                    <a href="remove_task.php?id=<?= $task['id'] ?>" class="btn-delete" title="Eliminar tarea">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                        </svg>
                    </a>
                </li>
            <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
