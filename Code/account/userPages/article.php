<?php
    if($_POST) {
        header('Content-Type: application/json; charset=utf-8');

        if($_SESSION["role"] != 1) {
            $responseText = array(
                "success" => false,
                "message" => "Neoprávněný přístup."
            );

            http_response_code(403);
            echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));

            exit();
        }

        //Submit article to validation
        if(isset($_POST["submit-article"])) {
            if(is_numeric($_POST["submit-article"]) == false) {
                $responseText = array(
                    "success" => false,
                    "error-field" => "submit-article",
                    "message" => "Neplatné ID článku"
                );

                http_response_code(400);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));

                exit();
            }

            $articleToSubmit = Db::queryOne("SELECT * FROM articles WHERE articleID = ?", $_POST["submit-article"]);
            if(empty($articleToSubmit)) {
                $responseText = array(
                    "success" => false,
                    "message" => "Článek neexistuje"
                );
    
                http_response_code(404);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
    
                exit();
            }

            if(empty($articleToSubmit)) {
                $responseText = array(
                    "success" => false,
                    "message" => "Článek neexistuje"
                );
    
                http_response_code(404);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
    
                exit();
            }

            if($_SESSION["user_id"] != $articleToSubmit["author"]) {
                $responseText = array(
                    "success" => false,
                    "message" => "K této akci nemáte přístup"
                );
    
                http_response_code(404);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
    
                exit();
            }

            if($articleToSubmit["status"] != 0 && $articleToSubmit["status"] != 2) {
                $responseText = array(
                    "success" => false,
                    "message" => "Článek nelze odeslat ke kontrole"
                );
    
                http_response_code(404);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
    
                exit();
            }

            try {
                Db::query("UPDATE articles SET status = 1 WHERE articleID = ?", $_POST["submit-article"]);
                $responseText = array(
                    "success" => true,
                    "message" => "Článek byl odeslán k ověření"
                );
                echo json_encode($responseText, JSON_UNESCAPED_UNICODE);
                exit();
            }
    
            catch(PDOException $e) {
                $responseText = array(
                    "success" => false,
                    "message" => "Nepodařilo se odeslat článek k ověření",
                    "error" => $e->getMessage()
                );
    
                http_response_code(500);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
    
                exit();
            }

            exit();
        }



        if(isset($_POST["article-id"]) && is_numeric($_POST["article-id"]) == false) {
            $responseText = array(
                "success" => false,
                "error-field" => "article-id",
                "message" => "Neplatný požadavek"
            );

            http_response_code(400);
            echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));

            exit();
        }

        if(isset($_POST["article-title"]) || empty($_POST["article-title"])) {
            $responseText = array(
                "success" => false,
                "error-field" => "article-title",
                "message" => "Vyplňte všechny povinné údaje"
            );

            http_response_code(400);
            echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));

            exit();
        }

        exit();
    }

    if($_SESSION["role"] != 1) {
        //TODO 
        echo("K této stránce nemáte přístup.");
        exit();
    }

    if(isset($_GET["article"]) == false || is_numeric($_GET["article"]) == false) {
        //TODO 
        echo("Neplatné id článku");
        exit();
    }

    $articleData = Db::queryOne("
        SELECT articles.*, GROUP_CONCAT(tags.tagID) AS tags
        FROM articles 
        RIGHT JOIN article_tag ON article_tag.article = articles.articleID
        LEFT JOIN tags ON article_tag.tag = tags.tagID
        WHERE articles.articleID = ?
        GROUP BY articles.articleID
    ", $_GET["article"]);

    
    if(empty($articleData)) {
        //TODO 
        echo("Článek nebyl nalezen");
        exit();
    }
    
    if($_SESSION["user_id"] != $articleData["author"]) {
        //TODO 
        echo("K tomuto článku nemáte přístup");
        exit();
    }
    
    $selectedTags = explode(',', $articleData["tags"]);
    $tags = Db::queryAll("SELECT * FROM tags");
?>

<div id="content-header">
    <h3><?php echo($articleData["title"]); ?></h3>
</div>

<?php
    if($articleData["status"] != 0 && $articleData["status"] != 2) {
?>

<div class="info-panel warn">
    <p>Článek nelze upravovat!</p>
</div>

<?php 
    }
?>

<div class="separator"></div>

<div id="page-content" class="add-article">
    <form method="POST" action="." data-wait-on-change="true">
        <input type="hidden" name="action-page" value="edit-article" />
        <input type="hidden" name="article-id" value="<?php echo($articleData["articleID"]); ?>" />
        <label>
            <input name="article-title" type="text" placeholder=" " value="<?php echo($articleData["title"]); ?>" <?php echo(($articleData["status"] != 0 && $articleData["status"] != 2) ? "readonly" : "");?> />
            <span>Název článku</span>
        </label>

        <label>
            <span>Tagy</span>

            <div class="select-multiple" data-name="article-tags[]" <?php echo($articleData["title"]); ?>" <?php echo(($articleData["status"] != 0 && $articleData["status"] != 2) ? "data-readonly=\"readonly\"" : "");?>>
                <div class="select-multiple-trigger">
                    <div class="select-multiple-selected"></div>
                    <p class="select-multiple-placeholder">Vyberte...</p>
                </div>
                <div class="select-multiple-options">

                <?php
                    foreach($tags as $key=>$tag) {
                        ?>
                            <div class="select-multiple-option" data-selected="<?php echo(in_array($tag["tagID"], $selectedTags) ? "true" : "false")?>" data-index="<?php echo($key); ?>" data-value="<?php echo($tag["tagID"])?>"><?php echo($tag["name"])?></div>
                        <?php
                    }
                ?>

                </div>
                <div class="select-multiple-inputs"></div>
            </div>
        </label>

        <label>
            <span>Banner</span>
            <input name="article-banner" type="file" />
        </label>
        
        <?php
            if($articleData["status"] == 0 || $articleData["status"] == 2) {
        ?>

        <label>
            <span>Text</span>
            <textarea name="article-text"><?php echo($articleData["text"]); ?></textarea>
        </label>

        <?php 
            }

            else {
        ?>
        <span>Text</span>
        <div class="article-box"><?php echo($articleData["text"]); ?></div>
        <?php
            }
            
            if($articleData["status"] == 0 || $articleData["status"] == 2) {
        ?>
            <div class="buttons-row">
                <!-- <button type="button" class="theme-button">Odeslat ke kontrole</button> -->
                <button type="submit" class="theme-button">Uložit</button>
            </div>
        <?php 
            }
        ?>
    </form>

    <?php
        if($articleData["status"] == 0 || $articleData["status"] == 2) {
    ?>

    <div class="buttons-row">
        <form method="POST" action=".">
            <input type="hidden" name="action-page" value="edit-article" />
            <input type="hidden" name="submit-article" value="<?php echo($articleData["articleID"]); ?>" />
            <button type="submit" class="theme-button">Odeslat ke kontrole</button>
        </form>
    </div>

    <?php 
        }
    ?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.0/tinymce.min.js" integrity="sha512-SOoMq8xVzqCe9ltHFsl/NBPYTXbFSZI6djTMcgG/haIFHiJpsvTQn0KDCEv8wWJFu/cikwKJ4t2v1KbxiDntCg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    tinymce.init({
        selector: 'textarea',
        deprecation_warnings: false,
        setup: (editor) => {
            editor.on("KeyPress", () => {
                const form = document.querySelector(".add-article form");
                form.dispatchEvent(new Event("input"));
            });
        }
    });
</script>