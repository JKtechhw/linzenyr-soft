<?php
    require_once("Db.php");

    try {
        $connectData = include(__DIR__ . "/../config/db.php");
    }

    catch(Exception $e) {
        throw new Exception($e->getMessage());
    }

    try {
        Db::connect($connectData["DB_HOST"], $connectData["DB_DATABASE"], $connectData["DB_USER"], $connectData["DB_PASSWORD"]);
    }

    catch(PDOException $e) {
        echo("Nepodařilo se připojit k DB");
        throw new Exception($e->getMessage());
    }
?>