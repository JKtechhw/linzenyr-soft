<?php
    if($_POST) {
        session_start();
        require_once("../../src/dbConnect.php");
        header('Content-Type: application/json; charset=utf-8');

        if(isset($_POST["article-title"]) == false || empty($_POST["article-title"])) {
            $responseText = array(
                "success" => false,
                "error-field" => "article-title",
                "message" => "Vyplňte všechny povinné údaje"
            );

            http_response_code(400);
            echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));

            exit();
        }

        if($_POST["article-title"] != strip_tags($_POST["article-title"])) {
            $responseText = array(
                "success" => false,
                "error-field" => "article-title",
                "message" => "Název obsahuje zakázané znaky"
            );

            http_response_code(400);
            echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));

            exit();
        }

        if(isset($_POST["article-tags"]) == false || empty($_POST["article-title"])) {
            $responseText = array(
                "success" => false,
                "error-field" => "article-tags",
                "message" => "Vyplňte všechny povinné údaje"
            );

            http_response_code(400);
            echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));

            exit();
        }

        if(isset($_POST["article-text"]) == false || empty($_POST["article-text"])) {
            $responseText = array(
                "success" => false,
                "error-field" => "article-text",
                "message" => "Vyplňte všechny povinné údaje"
            );

            http_response_code(400);
            echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));

            exit();
        }

        try {
            Db::query("
                INSERT INTO articles (title, author, text, status) 
                VALUES (?,?,?,?)
            ", $_POST["article-title"], $_SESSION["user_id"], $_POST["article-text"], 0);
        }

        catch(PDOException $e) {
            $responseText = array(
                "success" => false,
                "message" => "Článek se nepodařilo přidat",
                "error" => $e->getMessage()
            );

            http_response_code(500);
            echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));

            exit();
        }

        $responseText = array(
            "success" => true,
            "message" => "Článek byl úspěšně uložen",
        );

        echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));

        exit();
    }

    $pathToSources = "../../";
    include("../../partials/userHeader.php");

    $tags = Db::queryAll("SELECT * FROM tags");
?>

<main>
    <div id="content-header">
        <h3>Nový článek</h3>
    </div>

    <div id="page-content" class="add-article">
        <form method="POST" action=".">
            <label>
                <span>Název článku</span>
                <input name="article-title" type="text" />
            </label>

            <label>
                <span>Tagy</span>
                <select multiple="multiple" name="article-tags[]">
                    <?php
                        foreach($tags as $tag) {
                            ?>
                                <option value="<?php echo($tag["tagID"])?>"><?php echo($tag["name"])?></option>
                            <?php
                        }
                    ?>
                </select>
            </label>

            <label>
                <span>Text</span>
                <textarea name="article-text"></textarea>
            </label>

            <div class="buttons-row">
                <button type="button">Odeslat ke kontrole</button>
                <button type="submit">Uložit</button>
            </div>
        </form>
    </div>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.0/tinymce.min.js" integrity="sha512-SOoMq8xVzqCe9ltHFsl/NBPYTXbFSZI6djTMcgG/haIFHiJpsvTQn0KDCEv8wWJFu/cikwKJ4t2v1KbxiDntCg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    tinymce.init({
        selector: 'textarea',  // change this value according to your HTML
        deprecation_warnings: false
    });

</script>
<?php

    include("../../partials/footer.php");