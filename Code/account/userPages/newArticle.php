<?php
    if($_POST) {
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

        $banner = NULL;
        if($_FILES) {
            $target_dir = __DIR__ . "/../../assets/banners/";
            $target_file = $target_dir . basename($_FILES["article-banner"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

            if(isset($_POST["submit"])) {
                $check = getimagesize($_FILES["article-banner"]["tmp_name"]);
                if($check == false) {
                    $responseText = array(
                        "success" => false,
                        "error-field" => "article-banner",
                        "message" => "Neplatný soubor banneru"
                    );
        
                    http_response_code(400);
                    echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
                    exit();
                }
            }

            if (file_exists($target_file)) {
                $responseText = array(
                    "success" => false,
                    "error-field" => "article-banner",
                    "message" => "Soubor s tímto názvem již existuje"
                );
    
                http_response_code(400);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
                exit();
            }

            if ($_FILES["article-banner"]["size"] > 1000000) {
                $responseText = array(
                    "success" => false,
                    "error-field" => "article-banner",
                    "message" => "Soubor je moc veliký"
                );
    
                http_response_code(400);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
                exit();
            }

            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
                $responseText = array(
                    "success" => false,
                    "error-field" => "article-banner",
                    "message" => "Nepodporovaný formát bannmeru"
                );
    
                http_response_code(400);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
                exit();
            }

            if (move_uploaded_file($_FILES["article-banner"]["tmp_name"], $target_file)) {
                $banner = $_FILES["article-banner"]["name"];
            } 
            
            else {
                $responseText = array(
                    "success" => false,
                    "error-field" => "article-banner",
                    "message" => "Soubor se nepodařilo nahrát"
                );
    
                http_response_code(500);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
                exit();
            }
        }

        try {
            Db::query("
                INSERT INTO articles (title, author, text, banner, status) 
                VALUES (?,?,?,?,?)
            ", $_POST["article-title"], $_SESSION["user_id"], $_POST["article-text"], $banner, 0);
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

    $tags = Db::queryAll("SELECT * FROM tags");
?>

<div id="content-header">
    <h3>Nový článek</h3>
</div>

<div class="separator"></div>

<div id="page-content" class="add-article">
    <form method="POST" action=".">
        <input type="hidden" name="action-page" value="new-article" />
        <label>
            <input name="article-title" type="text" placeholder=" " />
            <span>Název článku</span>
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
            <span>Banner</span>
            <input name="article-banner" type="file" />
        </label>

        <label>
            <span>Text</span>
            <textarea name="article-text"></textarea>
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
        selector: 'textarea',  // change this value according to your HTML
        deprecation_warnings: false
    });

</script>