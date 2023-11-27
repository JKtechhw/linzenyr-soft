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
    <link rel="stylesheet" type="text/css" href="<?php echo($pathToSources); ?>css/user.css" />
    <link rel="icon" type="image/png" href="<?php echo($pathToSources); ?>assets/Images/logo-short.png" />
    <script src="<?php echo($pathToSources); ?>js/user.js" defer></script>
</head>
<body>
    <div id="content-container">
        <nav id="nav" class="user-nav">
            <div class="user-data">
                <img src="<?php echo($pathToSources); ?>assets/avatars/<?php echo($_SESSION["avatar"]); ?>">

                <div class="user-detail">
                    <h2 class="user-name"><?php echo($_SESSION["fullName"]); ?></h2>
                    <h2 class="user-username"><?php echo($_SESSION["roleName"]); ?></h2>
                </div>
            </div>

            <ul>
                <li class="active">
                    <a href="#">
                        <i class="bi bi-chat-left-text"></i> 
                        <span>Články</span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="bi bi-info-square"></i>
                        <span>Helpdesk</span>
                    </a>
                </li>

                <li>
                    <a href="#" class="logout">
                        <i class="bi bi-box-arrow-left"></i>
                        <span>Odhlásit</span>
                    </a>
                </li>
            </ul>
        </nav>