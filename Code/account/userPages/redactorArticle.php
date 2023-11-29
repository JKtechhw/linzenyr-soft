<?php
    if($_SESSION["role"] != 2) {
        //TODO 
        echo("K této stránce nemáte přístup.");
        exit();
    }

    if($_POST) {
        header('Content-Type: application/json; charset=utf-8');
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
        GROUP BY articles.articleID, tags.tagID
    ", $_GET["article"]);

    if(empty($articleData)) {
        //TODO 
        echo("Článek nebyl nalezen");
        exit();
    }

    if($articleData["status"] != 1 || $articleData["status"] != 3) {
        //TODO 
        echo("Nemáte přístup k tomuto článku.");
        exit();
    }
?>

<div id="content-header">
    <h3><?php echo($articleData["title"]); ?></h3>
</div>

<div id="page-content">

    <?php
        if(isset($articleData["banner"])){
    ?>

    <img src="/assets/banners/<?php echo $articleData["banner"]?>">

    <?php
        }
    ?>

    <div class="article-text"><?php echo($articleData["text"]); ?></div>
    <div class="buttons-row">
        <?php
            if($articleData["status"] == 3) {
        ?>

        <form action="." method="POST">
            <input type="hidden" name="action-page" value="author-article" />
            <input type="hidden" name="publish-article" value="<?php echo($articleData["articleID"]); ?>" />
            <button type="submit" class="theme-button">Publikovat</button>
        </form>

        <?php
            }
        
            else {
        ?>

        <form action="." method="POST">
            <input type="hidden" name="action-page" value="author-article" />
            <input type="hidden" name="accept-article" value="<?php echo($articleData["articleID"]); ?>" />
            <button type="submit" class="theme-button">Schválit</button>
        </form>
        
        <form action="." method="POST">
            <input type="hidden" name="action-page" value="author-article" />
            <input type="hidden" name="reject-article" value="<?php echo($articleData["articleID"]); ?>" />
            <button type="submit" class="theme-button">Zamitnout</button>
        </form>

        <?php
            }
        ?>
    </div>
</div>
