<?php 
function getDB() {
    static $db = null;

    if ($db === null) {
        $db = new PDO('sqlite:' . __DIR__ . '/tasks.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    return $db;
}
?>