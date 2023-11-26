<?php 
    include("partials/header.php");

    $tags = Db::queryAll("SELECT * FROM tags");
    $articles = Db::queryAll("
        SELECT articles.*, CONCAT(users.firstname, \" \", users.lastname) AS author, IF(users.avatar IS NULL,\"default.png\",users.avatar) AS avatar
        FROM articles
        INNER JOIN users ON articles.author = users.userID
    ");
?>

<main id="page-content">
    <div id="content-header">
        <h3>Články</h3>
    </div>

    <?php
        if(count($tags) > 0) {
    ?>
        <div class="tags-row">
            <button class="tag selected">Vše</button>
            <?php 
                foreach($tags as $tag) {
                    ?>
                        <button class="tag"><?php echo($tag["name"]); ?></button>
                    <?php
                }
            ?>
        </div>
    <?php
        }
    ?>

    <div class="articles-box">
        <?php
            if(count($tags) == 0) {
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
                        <h6 class="author-name"><?php echo($article["author"]); ?></h6>

                        <?php 
                            if($article["banner"] != null) {
                                
                            }
                        ?>
                        
                    </div>
                </div>
        <?php 
                }
            }
        ?>
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