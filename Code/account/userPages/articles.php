<?php 
    $articles = Db::queryAll("SELECT * FROM articles WHERE author = ?", $_SESSION["user_id"]);
?>

<div id="content-header">
    <h3>Vaše články</h3>
</div>

<div class="table-box">
    <div class="table-box-header">
        <a href="?page=new-article" class="theme-button">Přidat článek</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Název</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php 
                foreach($articles as $article) {
            ?>
                <tr>
                    <td>
                        <?php 
                            echo($article["title"]);
                        ?>
                    </td>

                    <td>
                        <?php 
                            echo($article["status"]);
                        ?>
                    </td>

                    <td>
                        <a href="?page=article-detail&article=<?php echo($article["status"]); ?>">Detail</a>
                    </td>
                </tr>
            <?php
                }
            ?>
        </tbody>
    </table>
</div>