<?php
    require_once("Db.php");

    if(file_exists(__DIR__ . "/../config/db.php") == false) {
        echo("Není nakonfigurována databáze!");
        exit();
    }

    if(is_readable(__DIR__ . "/../config/db.php") == false) {
        echo("Nelze číst z konfiguračního souboru databáze!");
        exit();
    }

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