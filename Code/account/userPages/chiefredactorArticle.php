<?php
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
