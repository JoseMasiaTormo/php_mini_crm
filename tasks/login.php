<?php
session_start(); // SIEMPRE lo primero en archivos que usen sesiones

require_once 'db.php';

$error = '';

// ¿Ha llegado un formulario? (petición POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // $_POST contiene los datos del formulario
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $db = getDB();

        // Consulta preparada - NUNCA concatenes variables en SQL directamente
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Login correcto: guardamos datos en la sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            header('Location: index.php'); // Redirigir
            exit;
        } else {
            $error = 'Usuario o contraseña incorrectos';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Login</title>
    </head>
    <body>
        <h1>Iniciar Sesión</h1>
        <?php if ($error): ?>
            <p style="color:red"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <label>Usuario: <input type="text" name="username"></label><br>
            <label>Contraseña: <input type="password" name="password"></label><br>
            <button type="submit">Entrar</button>
        </form>
    </body>
</html>