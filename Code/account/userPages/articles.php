<?php 
    // Author
    if($_SESSION["role"] == 1) {

    $articles = Db::queryAll("SELECT * FROM articles WHERE author = ?", $_SESSION["user_id"]);
?>

<div id="content-header">
    <h3>Vaše články</h3>
</div>

<div class="table-box">
    <div class="table-box-header">
        <a href="?page=new-article" class="theme-button">Přidat článek</a>
    </div>

    <?php 
        if(count($articles) == 0) {
            ?>
                <p>Nemáte žádný napsaný článek</p>
            <?php
        }

        else {
            ?>
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
                                <a href="?page=article-detail&article=<?php echo($article["articleID"]); ?>">Detail</a>
                            </td>
                        </tr>
                    <?php
                        }
                    ?>
                </tbody>
            </table>
            <?php
        }
    ?>
</div>

<?php 
    }
    // Redactor

    else if($_SESSION["role"] == 2) {
        $articles = Db::queryAll("SELECT * FROM articles WHERE status = 1 OR status = 3");
?>
<div id="content-header">
    <h3>Články ke schválení</h3>
</div>

<div class="table-box">
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
                            $statusText = array("Rozepsáno", "Schvalování", "Zamítnuto", "Schváleno", "Publikováno", "Vydáno");
                            echo($statusText[$article["status"]]);
                        ?>
                    </td>

                    <td>
                        <a href="?page=article-redactor&article=<?php echo($article["articleID"]); ?>">Detail</a>
                    </td>
                </tr>
            <?php
                }
            ?>
        </tbody>
    </table>
</div>

<?php 
    }
?>