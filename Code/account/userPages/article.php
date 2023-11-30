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
                Db::query("INSERT INTO validations (article) VALUES (?)", $_POST["submit-article"]);
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

        //Edit article

        if(isset($_POST["article-id"])) {
            if(is_numeric($_POST["article-id"]) == false) {
                $responseText = array(
                    "success" => false,
                    "error-field" => "article-id",
                    "message" => "Neplatný požadavek"
                );
    
                http_response_code(400);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
    
                exit();
            }

            $articleToEdit = Db::queryOne("SELECT * FROM articles WHERE articleID = ?", $_POST["article-id"]);
            if(empty($articleToEdit)) {
                $responseText = array(
                    "success" => false,
                    "error-field" => "article-id",
                    "message" => "Článek se nepodařilo najít"
                );
    
                http_response_code(404);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
    
                exit();
            }

            if($articleToEdit["author"] != $_SESSION["user_id"]) {
                $responseText = array(
                    "success" => false,
                    "error-field" => "article-id",
                    "message" => "Nemáte přístup k tomuto článku"
                );
    
                http_response_code(401);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
    
                exit();
            }
    
            if(isset($_POST["article-title"]) == false || empty($_POST["article-title"])) {
                $responseText = array(
                    "success" => false,
                    "error-field" => "article-title",
                    "message" => "Vyplňte všechny povinné údaje"
                );
    
                http_response_code(400);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
    
                exit();
            }
    
            if($_POST["article-title"] != strip_tags($_POST["article-title"])) {
                $responseText = array(
                    "success" => false,
                    "error-field" => "article-title",
                    "message" => "Název obsahuje zakázané znaky"
                );
    
                http_response_code(400);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
    
                exit();
            }
    
            if(isset($_POST["article-tags"]) == false || empty($_POST["article-title"])) {
                $responseText = array(
                    "success" => false,
                    "error-field" => "article-tags[]",
                    "message" => "Vyplňte všechny povinné údaje"
                );
    
                http_response_code(400);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
    
                exit();
            }

            $banner = $articleToEdit["banner"];

            if($_FILES && isset($_FILES['article-banner']['name']) && empty($_FILES['article-banner']['name']) == false) {
                $target_dir = __DIR__ . "/../../assets/banners/";
                $target_file = $target_dir . basename($_FILES["article-banner"]["name"]);
                $uploadOk = 1;
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    
                if(file_exists($target_dir) == false) {
                    mkdir($target_dir, 0777, true);
                }

                if(is_writable($target_dir) == false) {
                    $responseText = array(
                        "success" => false,
                        "error-field" => "article-banner",
                        "message" => "Banner nelze zapsat do adresáře"
                    );
        
                    http_response_code(400);
                    echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
                    exit();
                }
    
                if(isset($_POST["submit"])) {
                    $check = getimagesize($_FILES["article-banner"]["tmp_name"]);
                    if($check == false) {
                        $responseText = array(
                            "success" => false,
                            "error-field" => "article-banner",
                            "message" => "Neplatný soubor banneru"
                        );
            
                        http_response_code(400);
                        echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
                        exit();
                    }
                }
    
                if (file_exists($target_file)) {
                    $responseText = array(
                        "success" => false,
                        "error-field" => "article-banner",
                        "message" => "Soubor s tímto názvem již existuje"
                    );
        
                    http_response_code(400);
                    echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
                    exit();
                }
    
                if ($_FILES["article-banner"]["size"] > 1000000) {
                    $responseText = array(
                        "success" => false,
                        "error-field" => "article-banner",
                        "message" => "Soubor je moc veliký"
                    );
        
                    http_response_code(400);
                    echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
                    exit();
                }
    
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
                    $responseText = array(
                        "success" => false,
                        "error-field" => "article-banner",
                        "message" => "Nepodporovaný formát banneru"
                    );
        
                    http_response_code(400);
                    echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
                    exit();
                }

                if(file_exists($target_dir . $articleToEdit["banner"])) {
                    unlink($target_dir . $articleToEdit["banner"]);
                }
    
                if (move_uploaded_file($_FILES["article-banner"]["tmp_name"], $target_file)) {
                    $banner = $_FILES["article-banner"]["name"];
                } 
                
                else {
                    $responseText = array(
                        "success" => false,
                        "error-field" => "article-banner",
                        "message" => "Soubor se nepodařilo nahrát"
                    );
        
                    http_response_code(500);
                    echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
                    exit();
                }
            }

            try {
                Db::query("
                    UPDATE articles
                    SET title = ?, text = ?, banner = ?
                    WHERE articleID = ?
                ", $_POST["article-title"], $_POST["article-text"], $banner, $_POST["article-id"]);
            }
    
            catch(PDOException $e) {
                $responseText = array(
                    "success" => false,
                    "message" => "Článek se nepodařilo upravit",
                    "error" => $e->getMessage()
                );
    
                http_response_code(500);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
    
                exit();
            }

            Db::query("DELETE FROM article_tag WHERE article = ?", $_POST["article-id"]);

            foreach($_POST["article-tags"] as $tag) {
                Db::query("INSERT INTO article_tag (article, tag) VALUES(?,?)", $_POST["article-id"], $tag);
            }
    
            $responseText = array(
                "success" => true,
                "message" => "Článek byl úspěšně uložen",
            );
    
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
        SELECT articles.*, GROUP_CONCAT(tags.tagID) AS tags, GROUP_CONCAT(tags.name) AS tagsName
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
    $selectedTagsName = explode(',', $articleData["tagsName"]);
    $tags = Db::queryAll("SELECT * FROM tags");
?>

<div id="content-header">
    <h3><?php echo($articleData["title"]); ?></h3>

    <?php
        if($articleData["status"] == 0 || $articleData["status"] == 2) {
    ?>
        <form method="POST" action="." data-reload-onsuccess="true">
            <input type="hidden" name="action-page" value="edit-article" />
            <input type="hidden" name="submit-article" value="<?php echo($articleData["articleID"]); ?>" />
            <button type="submit" class="theme-button">Odeslat ke kontrole</button>
        </form>
    <?php 
        }
    ?>
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


<?php
    if($articleData["status"] == 0 || $articleData["status"] == 2) {
?>
    <div id="page-content" class="add-article">
        <form method="POST" action="." class="fullwidth-form" data-reload-onsuccess="true">
            <input type="hidden" name="action-page" value="edit-article" />
            <input type="hidden" name="article-id" value="<?php echo($articleData["articleID"]); ?>" />
            <label>
                <input name="article-title" type="text" placeholder=" " value="<?php echo($articleData["title"]); ?>" />
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

            <?php
                if($articleData["banner"] != NULL) {
            ?>
                <div class="banner-box">
                    <img src="../assets/banners/<?php echo($articleData["banner"]); ?>" />
                </div>
            <?php 
                }
            ?>

            <label>
                <span>Banner</span>
                <input name="article-banner" type="file" />
            </label>

            <label>
                <span>Text</span>
                <textarea name="article-text"><?php echo($articleData["text"]); ?></textarea>
            </label>

            <div class="buttons-row">
                <button type="submit" class="theme-button">Uložit</button>
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
        </form>

        <?php 
    }


    else {
    ?>

    <div id="page-content">
        <div class="article-box">
            <div class="article-tags">
                <?php 
                    foreach($selectedTagsName as $tag) {
                        ?>
                            <span><?php echo($tag); ?></span>
                        <?php
                    }
                ?>
            </div>

            <?php
                if($articleData["banner"] != NULL) {
                    ?>
                        <img src="../assets/banners/<?php echo($articleData["banner"]); ?>" class="article-banner">
                    <?php
                }
            ?>

            <?php echo($articleData["text"]); ?>
        </div>
    <?php 
        }

        if($articleData["status"] >= 2) {
            $reviews = Db::queryAll("
                SELECT DISTINCT reviews.text
                FROM validations
                LEFT JOIN reviews ON reviews.validation = (
                    SELECT validationID
                    FROM validations
                    WHERE article = ?
                    ORDER BY validationID DESC
                    LIMIT 1
                )
            ", $_GET["article"]);

            if(empty($reviews) == false) {
                ?>
                <div class="separator"></div>
                <div class="reviews-box">
                    <h2>Recenze: </h2>
                    <?php
                        foreach($reviews as $review) {
                    ?>
        
                    <div class="review">
                        <p><?php echo($review["text"]); ?></p>
                    </div>
                    
                    <?php
                        }
                    ?>
                </div>
                <?php
            }
        }
    ?>
    </div>
</div>