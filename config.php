<?php

ob_start();

try {
    // set connection with mysql
    $conn = new PDO("mysql:dbname=cercoengine;host=localhost", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
}
catch (PDOExeption $e) {
    echo "[FAILED] " . $e->getMessage();
}

?>