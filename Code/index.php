<?php 
    include("partials/header.php");

    $tags = Db::queryAll("
        SELECT tags.*
        FROM tags
        RIGHT JOIN article_tag ON article_tag.tag = tags.tagID
        LEFT JOIN articles ON articles.articleID = article_tag.tag
        GROUP BY tags.tagID
        ORDER BY COUNT(articles.articleID)
    ");

    if(isset($_GET["tag"]) && empty($_GET["tag"]) == false) {
        $selectedTag = urldecode($_GET["tag"]);
        $articles = Db::queryAll("
            SELECT articles.*, CONCAT(users.firstname, ' ', users.lastname) AS author, users.login, IF(users.avatar IS NULL, 'default.png', users.avatar) AS avatar
            FROM articles
            INNER JOIN users ON articles.author = users.userID
            RIGHT JOIN article_tag ON article_tag.article = articles.articleID
            LEFT JOIN tags ON tags.tagID = article_tag.tag
            WHERE articles.status >= 4 AND tags.name = ?
            ORDER BY articles.articleID DESC
        ", urldecode($_GET["tag"]));
    }

    else {
        $selectedTag = "all";
        $articles = Db::queryAll("
            SELECT articles.*, CONCAT(users.firstname, \" \", users.lastname) AS author, users.login, IF(users.avatar IS NULL,\"default.png\",users.avatar) AS avatar
            FROM articles
            INNER JOIN users ON articles.author = users.userID
            WHERE status >= 4
            ORDER BY articleID DESC
        ");
    }
?>

<main>
    <div class="content-header">
        <h3>Články</h3>
    </div>

    <div id="page-content">
        <?php
            if(count($tags) > 0) {
        ?>
            <div class="tags-row">
                <a href="?" class="tag <?php echo($selectedTag == "all" ? "selected" : "");?>">Vše</a>
                <?php 
                    foreach($tags as $tag) {
                        ?>
                            <a href="?tag=<?php echo( urlencode($tag["name"])) ?>"class="tag <?php echo($selectedTag == $tag["name"] ? "selected" : "");?>"><?php echo($tag["name"]); ?></a>
                        <?php
                    }
                ?>
            </div>
        <?php
            }
        ?>
        <div class="articles-box">
            <?php
                if(count($articles) == 0) {
            ?>

            <div class="box-message">
                <h1 class="box-message-title">Žádný článek nebyl nalezen</h1>
                <h3 class="box-message-description">Zkuste upravit filtry</h3>
            </div>

            <?php 
                }

                else {
                    foreach($articles as $article) {
            ?>
                    <div class="article">
                        <div class="article-header">
                            <img class="author-avatar" src="assets/avatars/<?php echo($article["avatar"]); ?>" />
                            <div class="author-detail">
                                <h6 class="author-name"><?php echo($article["author"]); ?></h6>
                                <p class="author-username">@<?php echo($article["login"]); ?></p>
                            </div>
                        </div>


                        <?php 
                            if($article["banner"] != null && file_exists("assets/banners/" . $article["banner"])) {
                                ?>
                                    <div class="article-banner">
                                        <img src="assets/banners/<?php echo($article["banner"]); ?>">
                                    </div>
                                <?php
                            }
                        ?>

                        <h1 class="article-title"><?php echo($article["title"]); ?></h1>
                        <div class="article-text">
                            <p><?php echo($article["text"]); ?></p>
                        </div>
                        <div class="article-link">
                            <a href="article-detail?article=<?php echo($article["articleID"]); ?>">Celý článek <i class="bi bi-arrow-right-short"></i></a>
                        </div>
                    </div>
            <?php 
                    }
                }
            ?>
        </div>
    </div>
</main>

<div id="suggestions-container">
    <div id="search-box">
        <input id="search" type="search" placeholder="Vyhledat článek..." autocomplete="off"  />
    </div>
</div>

<?php 
    include("partials/footer.php");
?>