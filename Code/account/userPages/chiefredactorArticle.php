<?php
    if($_POST) {
        header('Content-Type: application/json; charset=utf-8');

        if($_SESSION["role"] != 4) {
            $responseText = array(
                "success" => false,
                "message" => "Neoprávněný přístup"
            );

            http_response_code(403);
            echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));

            exit();
        }

        if(isset($_POST["article"])) {
            if(is_array($_POST["article"]) == false) {
                $responseText = array(
                    "success" => false,
                    "message" => "Neplatný formát článků"
                );
    
                http_response_code(400);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
    
                exit();
            }

            $isNumeric = count($_POST["article"]) === count(array_filter($_POST["article"], 'is_numeric'));

            if($isNumeric == false) {
                $responseText = array(
                    "success" => false,
                    "message" => "Neplatné ID článků"
                );
    
                http_response_code(400);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
    
                exit();
            }

            $checkIfExists = Db::queryAll("
                SELECT * 
                FROM articles 
                WHERE status = ? AND articleID IN (" . implode(',', array_map('intval', $_POST["article"])) . ")
            ", 4);

            if(count($checkIfExists) != count($_POST["article"])) {
                $responseText = array(
                    "success" => false,
                    "message" => "Neplatný výběr článků"
                );
    
                http_response_code(400);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
    
                exit();
            }

            Db::query("
                INSERT INTO editions (date, basename, title) 
                VALUES (?,?,?)
            ", date('Y-m-d H:i:s'), "test-basename", "Test title");

            $editionID = Db::getLastId();

            foreach($_POST["article"] as $index=>$articleID) {
                Db::query("
                    INSERT INTO article_edition (article, editionID, `order`) 
                    VALUES (?,?,?)
                ", $articleID, $editionID, $index);

                Db::query("UPDATE articles SET status = ? WHERE articleID = ?", 5, $articleID);
            }

            $responseText = array(
                "success" => true,
                "message" => "Vydání bylo úspěšné"
            );
            echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));

            exit();
        }

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
        WHERE articles.articleID = ?
        GROUP BY articles.articleID
    ", $_GET["article"]);

    if(empty($articleData)) {
        //TODO 
        echo("Článek nebyl nalezen");
        exit();
    }

    if($articleData["status"] != 3 && $articleData["status"] != 4 && $articleData["status"] != 5) {
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
</div>
