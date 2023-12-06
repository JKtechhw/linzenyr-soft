<?php 
    $request_url = $_SERVER['REQUEST_URI'];

    if (strpos($request_url, '.php') !== false && substr($request_url, -4) === '.php') {
        http_response_code(403);
        echo "Forbidden";
        exit();
    }

    if($_POST) {
        header('Content-Type: application/json; charset=utf-8');

        if(isset($_POST["helpdesk"])) {
            if(is_numeric($_POST["helpdesk"]) == false) {
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

            $targetHelpdesk = Db::queryOne("SELECT * FROM helpdesk WHERE helpdeskID = ?", $_POST["helpdesk"]);
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

            if($targetHelpdesk["user"] != $_SESSION["user_id"]) {
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
                    "message" => "Helpdesk byl již uzavřen"
                );
        
                http_response_code(403);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
                exit();
            }

            Db::query("INSERT INTO messages (helpdesk, author, message) VALUES (?,?,?)", $_POST["helpdesk"], $_SESSION["user_id"], $_POST["message-text"]);

            $responseText = array(
                "success" => true,
                "message" => "Zpráva byla úspěšně odeslána"
            );
    
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

    if($helpdesk["user"] != $_SESSION["user_id"]) {
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

    $messages = Db::queryAll("
        SELECT messages.*, CONCAT(users.firstname, \" \", users.lastname) AS full_name, IF(users.avatar IS NULL,\"default.png\",users.avatar) AS avatar
        FROM messages 
        INNER JOIN users ON messages.author = users.userID
        WHERE helpdesk = ?
    ", $_GET["id"]);
?>

<div class="content-header">
    <h3><?php echo($helpdesk["title"]); ?></h3>
</div>

<div id="page-content">
    <div id="messages-box">
        <?php 
            foreach($messages as $key => $message) {
                $articleDate = new DateTime($message["date"]);
                $articleDate->setTimezone(new DateTimeZone('Europe/Prague'));
                ?>

                    <div class="message-box <?php echo(($message["author"] != $_SESSION["user_id"]) ? "foreign-message" : "author-message" ); ?>">
                        <div class="avatar-box">
                            <img src="../assets/avatars/<?php echo($message["avatar"]); ?>">
                        </div>
                        <div class="message">
                            <p class="message-text"><?php echo($message["message"]); ?></p>
                            <p class="message-date">
                                <?php 
                                    echo($articleDate->format('H:i'));
                                ?>
                            </p>
                        </div>
                    </div>
                <?php
                    if(isset($messages[$key+1])) {
                        $nextArticleDate = new DateTime($messages[$key+1]["date"]);
                        $nextArticleDate->setTimezone(new DateTimeZone('Europe/Prague'));

                        echo($articleDate->format("Y") . " - " . $nextArticleDate->format("Y") . " * "); 
                        echo($articleDate->format("m") . " - " . $nextArticleDate->format("m") . " * ");
                        echo($articleDate->format("d") . " - " . $nextArticleDate->format("d") . " * ");

                        if(
                            $articleDate->format("Y") != $nextArticleDate->format("Y") || 
                            $articleDate->format("m") != $nextArticleDate->format("m") ||
                            $articleDate->format("d") != $nextArticleDate->format("d")
                        ) {
                            ?>
                                <div class="date-separator"><?php echo($articleDate->format('M j')); ?></div> 
                            <?php
                        }
                    }
            }
        ?>
    </div>
</div>

<div id="message-form-box">
    <form method="POST" data-form-events="none">
        <input type="hidden" name="helpdesk" value="<?php echo($_GET["id"]); ?>">
        <input type="hidden" name="action-page" value="helpdesk" />

        <textarea name="message-text" placeholder="Zadejte zprávu..." oninput='this.style.height = "";this.style.height = this.scrollHeight + "px"'></textarea>

        <div class="input-row">
            <span>
                <button type="button" class="icon-button">
                    <i class="bi bi-image"></i>
                </button>

                <button type="button" class="icon-button">
                    <i class="bi bi-lock-fill"></i>
                </button>
            </span>

            <span>
                <button type="submit" class="theme-button">Odeslat</button>
            </span>
        </div>
    </form>
</div>