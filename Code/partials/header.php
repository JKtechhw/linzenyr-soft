<?php
    $request_url = $_SERVER['REQUEST_URI'];

    if (strpos($request_url, '.php') !== false && substr($request_url, -4) === '.php') {
        http_response_code(403);
        echo "Error 403";
        exit();
    } 

    require_once(__DIR__ . "/../src/dbConnect.php");

    if(isset($pathToSources) == false) {
        $pathToSources = "";
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Srsťoplsť</title>
    <link rel="stylesheet" type="text/css" href="<?php echo($pathToSources); ?>css/style.css" />
    <link rel="icon" type="image/png" href="<?php echo($pathToSources); ?>assets/Images/logo-short.png" />
    <script src="<?php echo($pathToSources); ?>js/index.js" defer></script>
</head>
<body>
    <div id="content-container">
        <nav id="nav">
            <ul>
                <li class="active">
                    <a href="<?php echo($pathToSources); ?>#">
                        <i class="bi bi-chat-left-text"></i> 
                        <span>Články</span>
                    </a>
                </li>

                <li>
                    <a href="<?php echo($pathToSources); ?>#">
                        <i class="bi bi-newspaper"></i> 
                        <span>Vydání</span>
                    </a>
                </li>

                <li>
                    <a href="<?php echo($pathToSources); ?>account/">
                        <i class="bi bi-person"></i>
                        <span>Účet</span>
                    </a>
                </li>

                <li>
                    <a href="<?php echo($pathToSources); ?>">
                        <i class="bi bi-info-circle"></i>
                        <span>O nás</span>
                    </a>
                </li>
            </ul>
        </nav>