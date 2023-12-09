<?php 
    $request_url = $_SERVER['REQUEST_URI'];

    if (strpos($request_url, '.php') !== false && substr($request_url, -4) === '.php') {
        http_response_code(403);
        echo "Forbidden";
        exit();
    }

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

    require_once(__DIR__ . "/../../src/dbConnect.php");

    if($_SESSION["role"] == 5) {
        $reports = Db::queryAll("
            SELECT h.*, messages.message
            FROM helpdesk h
            LEFT JOIN messages ON messages.helpdesk = (
                SELECT messageID
                FROM messages
                WHERE messages.helpdesk = h.helpdeskID
                ORDER BY messageID DESC
                LIMIT 1
            )
        ");
    }

    else {
        $reports = Db::queryAll("
            SELECT h.*, messages.message
            FROM helpdesk h
            LEFT JOIN messages ON messages.helpdesk = (
                SELECT messageID
                FROM messages
                WHERE messages.helpdesk = h.helpdeskID
                ORDER BY messageID DESC
                LIMIT 1
            )
            WHERE h.user = ?
        ", $_SESSION["user_id"]);
    }

?>
<div id="user-nav" class="user-nav">
    <div id="content-header">
        <h3>
            <a href="?"><i class="bi bi-arrow-left"></i></a> Helpdesk
        </h3>

        <h3>
            <a href="#">+</a>
        </h3>
    </div>
    <div class="separator"></div>
    <ul class="user-nav-content">
        <?php 
            if(count($reports) == 0) {
                ?>
                <li class="static-item">
                    <span>Nemáte žádnou konverzaci</span>
                </li>
                <?php
            }

            else {
                foreach($reports as $report) {
                    ?>
                        <li>
                            <a href="?page=helpdesk&id=<?php echo($report["helpdeskID"]); ?>">
                                <span><?php echo($report["title"]); ?></span>
                            </a>
                        </li>
                    <?php
                }
            }
        ?>
    </ul>
</div>