<?php 
function getDB() {
    static $db = null;

    if ($db === null) {
        $db = new PDO('sqlite:' . __DIR__ . '/../tasks.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
            $db->exec("ALTER TABLE tasks ADD COLUMN completed INTEGER NOT NULL DEFAULT 0");
        } catch (PDOException) {
            // columna ya existe
        }
    }

    return $db;
}
?>