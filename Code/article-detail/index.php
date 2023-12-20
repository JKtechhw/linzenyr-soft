<?php
    $request_url = $_SERVER['REQUEST_URI'];
    if (strpos($request_url, '.php') !== false && substr($request_url, -4) === '.php') {
        http_response_code(403);
        echo "Forbidden";
        exit();
    }

    $pathToSources = "../";
    include("../partials/header.php");

    if(isset($_GET["article"]) == false || is_numeric($_GET["article"]) == false) {
        echo("Článek nenalezen");
        http_response_code(404);
        exit();
    }

    $articleData = Db::queryOne("
        SELECT articles.*, GROUP_CONCAT(tags.name) AS tagsName, CONCAT(users.firstname, \" \", users.lastname) AS author, IF(users.avatar IS NULL,\"default.png\",users.avatar) AS avatar, users.login
        FROM articles 
        RIGHT JOIN article_tag ON article_tag.article = articles.articleID
        LEFT JOIN tags ON article_tag.tag = tags.tagID
        INNER JOIN users ON articles.author = users.userID
        WHERE articles.articleID = ?
        GROUP BY articles.articleID
    ", $_GET["article"]);

    if(empty($articleData)) {
        ?>
        <main>
            <div class="box-message">
                <h1 class="box-message-title">Článek nebyl nalezen</h1>
            </div>
        </main>
        <?php
    }

    else {
        if($articleData["status"] <= 3) {
            ?>
            <main>
                <div class="box-message">
                    <h1 class="box-message-title">Článek nebyl nalezen</h1>
                </div>
            </main>
            <?php
        }

        else {
            $selectedTagsName = explode(',', $articleData["tagsName"]);

            ?>
                <main>
                    <div class="content-header">
                        <a href="../">
                            <h3><i class="bi bi-arrow-left"></i> <?php echo($articleData["title"]); ?></h3>
                        </a>
                    </div>

                    <div id="page-content">
                        <div class="article-box">
                        <div class="article-header">
                            <img class="author-avatar" src="../assets/avatars/<?php echo($articleData["avatar"]); ?>" />
                            <div class="author-detail">
                                <h6 class="author-name"><?php echo($articleData["author"]); ?></h6>
                                <p class="author-username">@<?php echo($articleData["login"]); ?></p>
                            </div>
                        </div>

                        <div class="article-tags tags-row">
                            <?php 
                                foreach($selectedTagsName as $tag) {
                                    ?>
                                        <a href="../?tag=<?php echo(urlencode($tag)); ?>" class="tag"><?php echo($tag); ?></a>
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

                        <?php echo(htmlspecialchars_decode($articleData["text"])); ?>
                        </div>
                    </div>
                </main>
            <?php
        }
    }

?>

<div id="suggestions-container">
    <div id="search-box">
        <div class="search-input-container">
            <input id="search" type="search" placeholder="Vyhledat článek..." autocomplete="off" data-action="../search/" />
        </div>
        <div id="search-output"></div>
    </div>
</div>

<?php
    include("../partials/footer.php");
?>