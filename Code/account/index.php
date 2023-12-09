<?php 
    $request_url = $_SERVER['REQUEST_URI'];

    if (strpos($request_url, '.php') !== false && substr($request_url, -4) === '.php') {
        http_response_code(403);
        echo "Forbidden";
        exit();
    } 

    session_start();

    if($_POST) {
        require_once("../src/dbConnect.php");
        header('Content-Type: application/json; charset=utf-8');

        if(isset($_POST["action-page"])) {
            if(isset($_SESSION["logged"]) == false || $_SESSION["logged"] == false) {
                $responseText = array(
                    "success" => false,
                    "message" => "Nejste přihlášeni"
                );
        
                http_response_code(403);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
                exit();
            }

            if($_POST["action-page"] == "articles") {
                include("userPages/articles.php");
            }
    
            else if($_POST["action-page"] == "new-article") {
                include("userPages/newArticle.php");
            }

            else if($_POST["action-page"] == "author-article") {
                include("userPages/redactorArticle.php");
            }

            else if($_POST["action-page"] == "edit-article") {
                include("userPages/article.php");
            }

            else if($_POST["action-page"] == "helpdesk") {
                include("userPages/helpdesk.php");
            }

            else {
                $responseText = array(
                    "success" => false,
                    "message" => "Neplatná hodnota action-page"
                );
        
                http_response_code(400);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
                exit();
            }

            exit();
        }

        else {
            if(isset($_POST["logout"])) {
                $_SESSION["logged"] == false;
                unset($_SESSION["user_id"]);
                unset($_SESSION["avatar"]);
                session_destroy();
    
                $responseText = array(
                    "success" => true,
                    "message" => "Úspěšně jsme vás odhlásili"
                );
    
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
                exit();
            }
    
            if(isset($_POST["username"]) == false || empty($_POST["username"])) {
                $responseText = array(
                    "success" => false,
                    "error-field" => "username",
                    "message" => "Vyplňte všechny povinné údaje"
                );
    
                http_response_code(400);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
    
                exit();
            }
    
            if(isset($_POST["password"]) == false || empty($_POST["password"])) {
                $responseText = array(
                    "success" => false,
                    "error-field" => "password",
                    "message" => "Vyplňte všechny povinné údaje"
                );
                
                http_response_code(400);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
                exit();
            }
    
            $user = Db::queryOne("
                SELECT users.*, CONCAT(users.firstname, \" \", users.lastname) AS full_name, IF(users.avatar IS NULL,\"default.png\",users.avatar) AS avatar, roles.name AS role_name
                FROM users 
                INNER JOIN roles ON users.role = roles.roleID
                WHERE login = ?
            ", $_POST["username"]);
            
            if(empty($user)) {
                $responseText = array(
                    "success" => false,
                    "message" => "Neplatné přihlášení"
                );
                
                http_response_code(400);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
                exit();
            }
    
            if(password_verify($_POST["password"], $user["password"]) == false) {
                $responseText = array(
                    "success" => false,
                    "message" => "Neplatné přihlášení"
                );
                
                http_response_code(400);
                echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
                exit();
            }
    
            $_SESSION["logged"] = true;
            $_SESSION["user_id"] = $user["userID"];
            $_SESSION["avatar"] = $user["avatar"];
            $_SESSION["full_name"] = $user["full_name"];
            $_SESSION["login"] = $user["login"];
            $_SESSION["role_name"] = $user["role_name"];
            $_SESSION["role"] = $user["role"];
    
            $responseText = array(
                "success" => false,
                "message" => "Úspěšně jsme vás přihlásili"
            );
            
            echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
            exit();
        }

        exit();
    }

    if(isset($_GET["action-page"])) {
        require_once("../src/dbConnect.php");
        header('Content-Type: application/json; charset=utf-8');

        if($_GET["action-page"] == "helpdesk") {
            include("userPages/helpdesk.php");
        }

        else {
            $responseText = array(
                "success" => false,
                "message" => "Neplatná hodnota action-page"
            );
    
            http_response_code(403);
            echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
            exit();
        }

        exit();
    }

    if(isset($_SESSION["logged"]) && $_SESSION["logged"] == true) {
        $pathToSources = "../";
        include("../partials/header.php");

        if(isset($_GET["page"]) == false || $_GET["page"] == "articles") {
            $headerTitle = "Články";
        }

        else if($_GET["page"] == "helpdesk") {
            $headerTitle = "Helpdesk";
        }

        else if($_GET["page"] == "profile") {
            $headerTitle = "Profil";
        }

        else if($_GET["page"] == "new-article") {
            $headerTitle = "Nový článek";
        }

        else if($_GET["page"] == "article-detail") {
            $headerTitle = "Detail článku";
        }

        else if($_GET["page"] == "article-redactor") {
            $headerTitle = "Detail článku";
        }

        include("userPages/userHeader.php");
    ?>

    <main>

    <?php
        if(isset($_GET["page"]) == false || $_GET["page"] == "articles") {
            include("userPages/articles.php");
        }

        else if($_GET["page"] == "helpdesk") {
            include("userPages/helpdesk.php");
        }

        else if($_GET["page"] == "profile") {
            include("userPages/profile.php");
        }

        else if($_GET["page"] == "new-article") {
            include("userPages/newArticle.php");
        }

        else if($_GET["page"] == "article-detail") {
            include("userPages/article.php");
        }

        else if($_GET["page"] == "article-redactor") {
            include("userPages/redactorArticle.php");
        }

        else {
            http_response_code(404);
            echo("Stránka nenalezena");
        }
    ?>

    </main>
    <script src="<?php echo($pathToSources); ?>js/user.js"></script>
    <?php
        include("../partials/footer.php");
    }

    else {
        include("userPages/login.php");
    }
?>