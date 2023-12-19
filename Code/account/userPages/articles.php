<?php 
    // Author
    if($_SESSION["role"] == 1) {

    $articles = Db::queryAll("SELECT * FROM articles WHERE author = ? ORDER BY articleID DESC", $_SESSION["user_id"]);
?>

<div class="content-header">
    <h3>Vaše články</h3>
    <a href="?page=new-article" class="theme-button">Přidat článek</a>
</div>

<div class="table-box">
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
                                    $statusText = array("Rozepsáno", "Schvalování", "Zamítnuto", "Schváleno", "Publikováno", "Vydáno");
                                    echo($statusText[$article["status"]]);
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
    <?php 
        if(count($articles) == 0) {
            ?>
                <p>Nemáte žádný článek ke schálení</p>
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
    <?php 
        }
    ?>
</div>

<?php 
    }
    //Recenzent
    else if($_SESSION["role"] == 3) {
        $articles = Db::queryAll("SELECT * FROM articles WHERE status = 1");
?>
<div id="content-header">
    <h3>Články ke zhodnocení</h3>
</div>

<div class="table-box">
    <?php 
        if(count($articles) == 0) {
            ?>
                <p>Nemáte žádný článek ke zhodnocení</p>
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
    <?php 
        }
    }
    
    else if($_SESSION["role"] == 4) {
        //Šéfredaktor
        $articles = Db::queryAll("SELECT * FROM articles WHERE status = 4");
?>
<div id="content-header">
    <h3>Články k vydání</h3>
</div>

<div class="table-box">
    <?php 
        if(count($articles) == 0) {
            ?>
                <p>Nemáte žádný článek k vydání</p>
            <?php
        }

        else {
    ?>

    <table id="release-articles-table">
        <thead>
            <tr>
                <th></th>
                <th></th>
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
                    <td class="handle">
                        <i class="bi bi-grip-vertical"></i>
                    </td>

                    <td>
                        <input type="checkbox" />
                    </td>

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
                        <a href="?page=article-chiefredactor&article=<?php echo($article["articleID"]); ?>">Detail</a>
                    </td>
                </tr>
            <?php
                }
            ?>
        </tbody>
    </table>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.1/Sortable.min.js"></script>
    <script>
        const table = document.querySelector("#release-articles-table tbody");
        new Sortable(table, {
            handle: '.handle', // handle's class
            animation: 150
        });
    </script>

    <?php 
        }
    }
    ?>