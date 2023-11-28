<?php 
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if(
        isset($_SESSION) == false || 
        isset($_SESSION["logged"]) == false || 
        $_SESSION["logged"] != true) {
        http_response_code(403);
        echo "Unauthorized";
        exit();
    }

    $request_url = $_SERVER['REQUEST_URI'];

    if (strpos($request_url, '.php') !== false && substr($request_url, -4) === '.php') {
        http_response_code(403);
        echo "Forbidden";
        exit();
    } 

    require_once(__DIR__ . "/../src/dbConnect.php");

    if(isset($pathToSources) == false) {
        $pathToSources = "";
    }

    if(isset($_GET["page"]) == false) {
        $page = "articles";
    }

    else {
        $page = $_GET["page"];
    }
?>
<div id="user-nav" class="user-nav">
    <div id="content-header">
        <h3><i class="bi bi-arrow-left-short"></i>Účet</h3>
    </div>

    <div class="separator"></div>

    <ul class="user-nav-content">
        <li class="static-item">
            <span>@<?php echo($_SESSION["login"]);?></span>
        </li>

        <li <?php echo($page == "articles" ? 'class="active"' : ""); ?>>
            <a href="?page=articles">
                <span>Články</span>
            </a>
        </li>

        <li <?php echo($page == "helpdesk" ? 'class="active"' : ""); ?>>
            <a href="?page=helpdesk">
                <span>Helpdesk</span>
            </a>
        </li>
    </ul>

    <div class="separator"></div>
    <ul class="user-nav-content">
        <li <?php echo($page == "profile" ? 'class="active"' : ""); ?>>
            <a href="?page=profile">
                <span>Profil</span>
            </a>
        </li>

        <li>
            <a href="<?php echo($pathToSources); ?>account" class="logout">
                <span>Odhlásit</span>
            </a>
        </li>
    </ul>
</div>