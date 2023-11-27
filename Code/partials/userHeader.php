<?php 
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
        <nav id="nav" class="user-nav">
            <div id="user-data">
                <img src="<?php echo($pathToSources); ?>assets/avatars/<?php echo($_SESSION["avatar"]); ?>">

                <div class="user-detail">
                    <h2><?php echo($_SESSION["fullName"]); ?></h2>
                    <h2>@<?php echo($_SESSION["login"]); ?></h2>
                </div>
            </div>

            <ul>
                <li class="active">
                    <a href="<?php echo($pathToSources); ?>#">
                        <i class="bi bi-chat-left-text"></i> 
                        <span>Články</span>
                    </a>
                </li>

                <li>
                    <a href="<?php echo($pathToSources); ?>#">
                        <i class="bi bi-info-square"></i>
                        <span>Helpdesk</span>
                    </a>
                </li>

                <li>
                    <a href="<?php echo($pathToSources); ?>#">
                        <i class="bi bi-box-arrow-left"></i>
                        <span>Odhlásit</span>
                    </a>
                </li>
            </ul>
        </nav>