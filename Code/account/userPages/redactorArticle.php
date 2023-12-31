<?php
    if($_POST) {
        header('Content-Type: application/json; charset=utf-8');

        if($_SESSION["role"] != 2) {
            $responseText = array(
                "success" => false,
                "message" => "Neoprávněný přístup."
            );

            http_response_code(403);
            echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));

            exit();
        }

        
        if(isset($_POST["accept-article"]))
        {
            if(is_numeric($_POST["accept-article"]) == false) {
                $responseText = array(
                    "success" => false,
                    "message" => "Neplatné ID článku."
                );
    
                http_response_code(400);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
    
                exit();
            }
            
            $articleData = Db::queryOne("SELECT * FROM articles WHERE articleID = ?", $_POST["accept-article"]);
            if(empty($articleData)) {
                $responseText = array(
                    "success" => false,
                    "message" => "Článek neexistuje."
                );
    
                http_response_code(404);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
    
                exit();
            }
    
            if ($articleData["status"] != 1 && $articleData["status"] != 2) {
                $responseText = array(
                    "success" => false,
                    "message" => "Neplatný stav článku."
                );
    
                http_response_code(400);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
    
                exit();
            }
    
            try {
                Db::query("UPDATE articles SET status = 3 WHERE articleID = ?", $_POST["accept-article"]);
                $responseText = array(
                    "success" => true,
                    "message" => "Článek byl úspěšně schválen."
                );
                echo json_encode($responseText, JSON_UNESCAPED_UNICODE);
                exit();
            }
    
            catch(PDOException $e) {
                $responseText = array(
                    "success" => false,
                    "message" => "Nepodařilo se upravit stav článku",
                    "error" => $e->getMessage()
                );
    
                http_response_code(500);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
    
                exit();
            }
        }

        if(isset($_POST["reject-article"]))
        {
    
            if(is_numeric($_POST["reject-article"]) == false) {
                $responseText = array(
                    "success" => false,
                    "message" => "Neplatné ID článku."
                );
    
                http_response_code(400);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
    
                exit();
            }
            
            $articleData = Db::queryOne("SELECT * FROM articles WHERE articleID = ?", $_POST["reject-article"]);
            if(empty($articleData)) {
                $responseText = array(
                    "success" => false,
                    "message" => "Článek neexistuje."
                );
    
                http_response_code(404);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
    
                exit();
            }
    
            if ($articleData["status"] != 1) {
                $responseText = array(
                    "success" => false,
                    "message" => "Neplatný stav článku."
                );
    
                http_response_code(400);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
    
                exit();
            }
    
            try {
                Db::query("UPDATE articles SET status = 2 WHERE articleID = ?", $_POST["reject-article"]);
                Db::query("UPDATE validations SET redactor = ? WHERE article = ? AND redactor IS NULL", $_SESSION["user_id"], $_POST["reject-article"]);
                $responseText = array(
                    "success" => true,
                    "message" => "Článek byl zamítnut."
                );
                echo json_encode($responseText, JSON_UNESCAPED_UNICODE);
                exit();
            }
    
            catch(PDOException $e) {
                $responseText = array(
                    "success" => false,
                    "message" => "Nepodařilo se upravit stav článku",
                    "error" => $e->getMessage()
                );
    
                http_response_code(500);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
    
                exit();
            }
        }

        if(isset($_POST["publish-article"]))
        {
    
            if(is_numeric($_POST["publish-article"]) == false) {
                $responseText = array(
                    "success" => false,
                    "message" => "Neplatné ID článku."
                );
    
                http_response_code(400);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
    
                exit();
            }
            
            $articleData = Db::queryOne("SELECT * FROM articles WHERE articleID = ?", $_POST["publish-article"]);
            if(empty($articleData)) {
                $responseText = array(
                    "success" => false,
                    "message" => "Článek neexistuje."
                );
    
                http_response_code(404);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
    
                exit();
            }
    
            if ($articleData["status"] != 3) {
                $responseText = array(
                    "success" => false,
                    "message" => "Neplatný stav článku."
                );
    
                http_response_code(400);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
    
                exit();
            }
    
            try {
                Db::query("UPDATE articles SET status = 4 WHERE articleID = ?", $_POST["publish-article"]);
                Db::query("UPDATE validations SET redactor = ? WHERE article = ? AND redactor IS NULL", $_SESSION["user_id"], $_POST["publish-article"]);
                $responseText = array(
                    "success" => true,
                    "message" => "Článek byl úspěsně publikován."
                );
                echo json_encode($responseText, JSON_UNESCAPED_UNICODE);
                exit();
            }
    
            catch(PDOException $e) {
                $responseText = array(
                    "success" => false,
                    "message" => "Nepodařilo se upravit stav článku",
                    "error" => $e->getMessage()
                );
    
                http_response_code(500);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
    
                exit();
            }
        }
        exit();
        
    }

    if($_SESSION["role"] != 2) {
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
        SELECT articles.*, GROUP_CONCAT(tags.name) AS tagsName, GROUP_CONCAT(reviews.text SEPARATOR '---') AS reviews
        FROM articles 
        RIGHT JOIN article_tag ON article_tag.article = articles.articleID
        LEFT JOIN tags ON article_tag.tag = tags.tagID
        RIGHT JOIN validations ON articles.articleID = validations.article
        LEFT JOIN reviews ON validations.validationID = reviews.validation
        WHERE articles.articleID = ? AND validations.redactor IS NULL
        GROUP BY articles.articleID
    ", $_GET["article"]);

    if(empty($articleData)) {
        //TODO 
        echo("Článek nebyl nalezen");
        exit();
    }

    if($articleData["status"] != 1 && $articleData["status"] != 3) {
        //TODO 
        echo("Nemáte přístup k tomuto článku.");
        exit();
    }

    $selectedTagsName = explode(',', $articleData["tagsName"]);
    $selectedReviews = explode('---', (string)$articleData["reviews"]);
?>

<div class="content-header">
    <h3><?php echo($articleData["title"]); ?></h3>
</div>

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
            if(isset($articleData["banner"])) {
                ?>
                    <img src="../assets/banners/<?php echo($articleData["banner"]); ?>" class="article-banner">
                <?php
            }
        ?>

        <?php echo($articleData["text"]); ?>
    </div>

    <?php
        if($articleData["status"] == 3) {
    ?>

        <div class="buttons-row">
            <form action="." method="POST">
                <input type="hidden" name="action-page" value="author-article" />
                <input type="hidden" name="publish-article" value="<?php echo($articleData["articleID"]); ?>" />
                <button type="submit" class="theme-button">Publikovat</button>
            </form>
        </div>

    <?php
        }
        
        else {
    ?>

        <div class="separator"></div>
        <div class="reviews-box">
            <h2>Recenze: </h2>
            <?php
                foreach($selectedReviews as $review) {
            ?>

            <div class="review">
                <p><?php echo($review); ?></p>
            </div>
            
            <?php
                }
            ?>
        </div>

        <div class="buttons-row">
            <form action="." method="POST">
                <input type="hidden" name="action-page" value="author-article" />
                <input type="hidden" name="reject-article" value="<?php echo($articleData["articleID"]); ?>" />
                <button type="submit" class="theme-button">Zamitnout</button>
            </form>

            <form action="." method="POST" data-reload-onsuccess="true">
                <input type="hidden" name="action-page" value="author-article" />
                <input type="hidden" name="accept-article" value="<?php echo($articleData["articleID"]); ?>" />
                <button type="submit" class="theme-button">Schválit</button>
            </form>
        </div>

        <?php
            }
        ?>
</div>
