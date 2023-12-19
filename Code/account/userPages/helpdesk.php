<?php 
    $request_url = $_SERVER['REQUEST_URI'];

    if (strpos($request_url, '.php') !== false && substr($request_url, -4) === '.php') {
        http_response_code(403);
        echo "Forbidden";
        exit();
    }

    if($_POST) {
        header('Content-Type: application/json; charset=utf-8');

        if(isset($_POST["close"])) {
            if(is_numeric($_POST["close"]) == false) {
                $responseText = array(
                    "success" => false,
                    "error-field" => "close",
                    "message" => "Neplatné ID ticketu"
                );
        
                http_response_code(400);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
                exit();
            }

            $helpdeskToClose = Db::queryOne("SELECT * FROM helpdesk WHERE helpdeskID = ?", $_POST["close"]);

            if($helpdeskToClose["user"] != $_SESSION["user_id"]) {
                $responseText = array(
                    "success" => false,
                    "message" => "Nemáte oprávnění pro zavření tohoto helpdesku"
                );
        
                http_response_code(403);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
                exit();
            }

            if($helpdeskToClose["solved"] != NULL) {
                $responseText = array(
                    "success" => false,
                    "message" => "Helpdesk byl již uzavřen"
                );
        
                http_response_code(400);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
                exit();
            }

            Db::query("UPDATE helpdesk SET solved = ? WHERE helpdeskID = ?", 1, $_POST["close"]);

            $responseText = array(
                "success" => true,
                "message" => "Helpdesk byl úspěšně uzavřen"
            );
    
            echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
            exit();
        }

        if(isset($_POST["helpdesk"]) || isset($_POST["new-ticket"])) {
            if(isset($_POST["new-ticket"])) {
                if(isset($_POST["ticket-name"]) == false || empty($_POST["ticket-name"])) {
                    $responseText = array(
                        "success" => false,
                        "error-field" => "ticket-name",
                        "message" => "Neplatný název ticketu"
                    );
            
                    http_response_code(400);
                    echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
                    exit();
                }

                Db::query("INSERT INTO helpdesk (title, user) VALUES (?,?)", $_POST["ticket-name"], $_SESSION["user_id"]);
                $helpdesk = Db::getLastId();
            }

            else if(is_numeric($_POST["helpdesk"])) {
                $helpdesk = $_POST["helpdesk"];
            }

            else {
                $responseText = array(
                    "success" => false,
                    "error-field" => "helpdesk",
                    "message" => "Neplatné ID helpdesku"
                );
        
                http_response_code(400);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
                exit();
            }

            if(isset($_POST["message-text"]) == false || empty($_POST["message-text"])) {
                $responseText = array(
                    "success" => false,
                    "error-field" => "message-text",
                    "message" => "Vyplňte všechny povinné údaje"
                );
        
                http_response_code(400);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
                exit();
            }

            if($_POST["message-text"] != strip_tags($_POST["message-text"])) {
                $responseText = array(
                    "success" => false,
                    "error-field" => "message-text",
                    "message" => "Zpráva obsahuje zakázané znaky"
                );
        
                http_response_code(400);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
                exit();
            }

            $targetHelpdesk = Db::queryOne("SELECT * FROM helpdesk WHERE helpdeskID = ?", $helpdesk);
            if(empty($targetHelpdesk)) {
                $responseText = array(
                    "success" => false,
                    "error-field" => "helpdesk",
                    "message" => "Helpdesk nebyl nalezen"
                );
        
                http_response_code(404);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
                exit();
            }

            if($targetHelpdesk["user"] != $_SESSION["user_id"] && $_SESSION["role"] != 5) {
                $responseText = array(
                    "success" => false,
                    "message" => "Nemáte oprávnění pro přidání zprávy do tohoto helpdesku"
                );
        
                http_response_code(403);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
                exit();
            }

            if($targetHelpdesk["solved"] != NULL) {
                $responseText = array(
                    "success" => false,
                    "message" => "Helpdesk byl již uzavřen a nelze do něj přidávat zprávy"
                );
        
                http_response_code(403);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
                exit();
            }

            Db::query("INSERT INTO messages (helpdesk, author, message) VALUES (?,?,?)", $helpdesk, $_SESSION["user_id"], $_POST["message-text"]);
            $lastId = Db::getLastId();
            $messageData = Db::queryOne("
                SELECT messages.*, CONCAT(users.firstname, \" \", users.lastname) AS full_name, IF(users.avatar IS NULL,\"default.png\",users.avatar) AS avatar
                FROM messages 
                INNER JOIN users ON users.userID = messages.author
                WHERE messageID = ?
            ", $lastId);

            $messages = Db::queryAll("
                SELECT messages.message AS 'message-text', CONCAT(users.firstname, \" \", users.lastname) AS 'author-name', IF(users.avatar IS NULL,\"default.png\",users.avatar) AS 'author-avatar', messages.date AS 'message-date', messages.author AS 'message-author'
                FROM messages 
                INNER JOIN users ON messages.author = users.userID
                WHERE helpdesk = ?
                ORDER BY date
            ", $helpdesk);

            if(isset($_POST["new-ticket"])) {
                $url = $_SERVER['REQUEST_URI'];
                $parsed_url = parse_url($url);
                $redirectUrl = $parsed_url['path'] . "?page=helpdesk&id=" . $helpdesk;

                $responseText = array(
                    "success" => true,
                    "message" => "Článek byl úspěšně uložen",
                    "redirect-page" => $redirectUrl
                );

            }

            else {
                $responseText = array(
                    "success" => true,
                    "message" => "Zpráva byla úspěšně odeslána",
                    "message-data" => array(
                        "message-text" => $messageData["message"],
                        "message-date" => $messageData["date"],
                        "message-author" => "author",
                        "author-avatar" => "../assets/avatars/" . $messageData["avatar"],
                        "author-name" => $messageData["full_name"],
                    ),
                    "new-hash" => hash("sha1", json_encode($messages))
                );
            }

    
            echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
            exit();
        }

        $responseText = array(
            "success" => false,
            "message" => "Neplatný požadavek"
        );

        http_response_code(400);
        echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
        exit();
    }

    if(isset($_GET["get-messages"])) {
        $messages = Db::queryAll("
            SELECT messages.message AS 'message-text', CONCAT(users.firstname, \" \", users.lastname) AS 'author-name', IF(users.avatar IS NULL,\"default.png\",users.avatar) AS 'author-avatar', messages.date AS 'message-date', messages.author AS 'message-author'
            FROM messages 
            INNER JOIN users ON messages.author = users.userID
            WHERE helpdesk = ?
            ORDER BY date
        ", $_GET["id"]);

        $hash = hash("sha1", json_encode($messages));

        foreach($messages as &$message) {
            $message["author-avatar"] = "../assets/avatars/" . $message["author-avatar"];
            $message["message-author"] = ($message["message-author"] != $_SESSION["user_id"] ? "foreign" : "author");
        }

        $responseData = array(
            "messages" => $messages,
            "hash" => $hash,
            "length" => count($messages)
        );

        echo(json_encode($responseData, JSON_UNESCAPED_UNICODE));

        return;
    }

    if(isset($_GET["new-ticket"])) {
        ?>
        <div class="content-header">
            <h3>Nový ticket</h3>
        </div>
        <div id="page-content">
            <div id="messages-box"></div>
        </div>

        <div id="message-form-box">
            <form method="POST">
                <input type="hidden" name="new-ticket" value="1">
                <input type="hidden" name="action-page" value="helpdesk" />

                <input type="text" name="ticket-name" placeholder="Zadejte název ticketu...">

                <textarea name="message-text" placeholder="Zadejte zprávu..."></textarea>

                <div class="input-row">
                    <span></span>

                    <span>
                        <button type="submit" class="theme-button">Odeslat</button>
                    </span>
                </div>
            </form>
        </div>
        <?php
        return;
    }

    if(isset($_GET["id"]) == false || empty($_GET["id"])) {
        ?>
        <div class="content-header">
            <h3>Helpdesk</h3>
        </div>
        <main>
            <div class="box-message">
                <h1 class="gray-heading">Vyberte konverzaci nebo vytvořte novou</h1>
            </div>
        </main>
        <?php
        return;
    }

    $helpdesk = Db::queryOne("SELECT * FROM helpdesk WHERE helpdeskID = ?", $_GET["id"]);

    if(empty($helpdesk)) {
        ?>
        <div class="content-header">
            <h3>Helpdesk</h3>
        </div>
        <main>
            <div class="box-message">
                <h1 class="gray-heading">Konverzace nebyla nalezena</h1>
            </div>
        </main>
        <?php
        return;
    }

    if($helpdesk["user"] != $_SESSION["user_id"] && $_SESSION["role"] != 5) {
        ?>
        <div class="content-header">
            <h3>Helpdesk</h3>
        </div>
        <main>
            <div class="box-message">
                <h1 class="gray-heading">K této konverzaci nemáte přístup</h1>
            </div>
        </main>
        <?php
        return;
    }
?>

<div class="content-header">
    <h3><?php echo($helpdesk["title"]); ?></h3>
</div>

<?php 
    if($helpdesk["solved"] != NULL) {
?>
    <div class="info-panel warn">
        <p>Ticket byl již uzavřen</p>
    </div>
<?php 
    }
?>

<div id="page-content">
    <div id="messages-box"></div>
</div>

<?php 
    if($helpdesk["solved"] == NULL) {
?>

    <div id="message-form-box">
        <form method="POST" data-form-events="none">
            <input type="hidden" name="helpdesk" value="<?php echo($_GET["id"]); ?>">
            <input type="hidden" name="action-page" value="helpdesk" />

            <textarea name="message-text" placeholder="Zadejte zprávu..."></textarea>

            <div class="input-row">
                <span>
                    <button type="button" class="icon-button" id="close-helpdesk-button" data-role="close-ticket" title="Uzamknout ticket">
                        <i class="bi bi-lock-fill"></i>
                    </button>
                </span>

                <span>
                    <button type="submit" class="theme-button">Odeslat</button>
                </span>
            </div>
        </form>
    </div>

<?php 
    }
?>

<script src="../js/helpdesk.js"></script>
<script>
    const messagesBox = document.querySelector("#messages-box");
    const messageForm = document.querySelector("#message-form-box form");
    new helpdesk(messagesBox, messageForm);
</script>