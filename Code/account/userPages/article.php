<?php
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

    print_r($articleData);

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

    $tags = Db::queryAll("SELECT * FROM tags");
?>

<div id="content-header">
    <h3><?php echo($articleData["title"]); ?></h3>
</div>

<div class="separator"></div>

<div id="page-content" class="add-article">
    <form method="POST" action=".">
        <input type="hidden" name="action-page" value="new-article" />
        <label>
            <input name="article-title" type="text" placeholder=" " value="<?php echo($articleData["title"]); ?>"/>
            <span>Název článku</span>
        </label>

        <label>
            <span>Tagy</span>

            <div class="select-multiple" data-name="article-tags[]">
                <div class="select-multiple-trigger">
                    <div class="select-multiple-selected"></div>
                    <p class="select-multiple-placeholder">Vyberte...</p>
                </div>
                <div class="select-multiple-options">

                <?php
                    foreach($tags as $key=>$tag) {
                        ?>
                            <div class="select-multiple-option" data-index="<?php echo($key); ?>" data-value="<?php echo($tag["tagID"])?>"><?php echo($tag["name"])?></div>
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

        <label>
            <span>Text</span>
            <textarea name="article-text"><?php echo($articleData["text"]); ?></textarea>
        </label>

        <div class="buttons-row">
            <button type="button" class="theme-button">Odeslat ke kontrole</button>
            <button type="submit" class="theme-button">Uložit</button>
        </div>
    </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.0/tinymce.min.js" integrity="sha512-SOoMq8xVzqCe9ltHFsl/NBPYTXbFSZI6djTMcgG/haIFHiJpsvTQn0KDCEv8wWJFu/cikwKJ4t2v1KbxiDntCg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    tinymce.init({
        selector: 'textarea',
        deprecation_warnings: false
    });

</script>