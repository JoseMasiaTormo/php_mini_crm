<?php
$db = new PDO('sqlite:tasks.db');

$db->exec("
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NULL UNIQUE,
        password TEXT NOT NULL
    );

    CRETAE TABLE IF NOT EXISTS tasks (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        title TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
");

// Usuario de prueba (la contraseña de hashea, nunca en texto plano)
$hash = password_hash('1234', PASSWORD_DEFAULT);
$db->exec("INSERT OR IGNORE INTO users (user, password) VALUES ('jose', '$hash')");

echo "Base de datos creada correctamente";
?>