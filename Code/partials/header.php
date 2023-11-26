<?php 
    require_once(__DIR__ . "/../src/dbConnect.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Srsťoplsť</title>
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <link rel="icon" type="image/png" href="assets/Images/logo-short.png" />
    <script src="js/index.js"></script>
</head>
<body>
    <div id="content-container">
        <nav id="nav">
            <ul>
                <li class="active">
                    <a href="#">
                        <i class="bi bi-chat-left-text"></i> 
                        <span>Články</span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="bi bi-newspaper"></i> 
                        <span>Vydání</span>
                    </a>
                </li>

                <li>
                    <a href="login/">
                        <i class="bi bi-person"></i>
                        <span>Účet</span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="bi bi-info-circle"></i>
                        <span>O nás</span>
                    </a>
                </li>
            </ul>
        </nav>