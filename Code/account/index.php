<?php 
    session_start();

    if($_POST) {
        require_once("../src/dbConnect.php");
        header('Content-Type: application/json; charset=utf-8');

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
            return;
        }

        if(isset($_POST["username"]) == false || empty($_POST["username"])) {
            $responseText = array(
                "success" => false,
                "error-field" => "username",
                "message" => "Vyplňte všechny povinné údaje"
            );

            http_response_code(400);
            echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));

            return;
        }

        if(isset($_POST["password"]) == false || empty($_POST["password"])) {
            $responseText = array(
                "success" => false,
                "error-field" => "password",
                "message" => "Vyplňte všechny povinné údaje"
            );
            
            http_response_code(400);
            echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
            return;
        }

        $user = Db::queryOne("
            SELECT users.*, CONCAT(users.firstname, \" \", users.lastname) AS fullName, IF(users.avatar IS NULL,\"default.png\",users.avatar) AS avatar, roles.name AS roleName
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
            return;
        }

        if(password_verify($_POST["password"], $user["password"]) == false) {
            $responseText = array(
                "success" => false,
                "message" => "Neplatné přihlášení"
            );
            
            http_response_code(400);
            echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
            return;
        }

        $_SESSION["logged"] = true;
        $_SESSION["user_id"] = $user["userID"];
        $_SESSION["avatar"] = $user["avatar"];
        $_SESSION["fullName"] = $user["fullName"];
        $_SESSION["login"] = $user["login"];
        $_SESSION["roleName"] = $user["roleName"];

        $responseText = array(
            "success" => false,
            "message" => "Úspěšně jsme vás přihlásili"
        );
        
        echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
        return;
    }

    if(isset($_SESSION["logged"]) && $_SESSION["logged"] == true) {
        $pathToSources = "../";
        include("../partials/userHeader.php");
    ?>

    <main>
        <div class="table-box">
            
        </div>
    </main>

    <?php
        include("../partials/footer.php");
    }

    else {
        ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Přihlášení Srsťoplsť</title>
                <link rel="stylesheet" type="text/css" href="../css/login.css" />
                <link rel="icon" type="image/png" href="../assets/Images/logo-short.png" />
                <script src="../js/login.js" defer></script>
            </head>
            <body>
                <div id="login-content">
                    <div class="description-box box">
                        <img src="../assets/Images/logo-full-transparent.png">
                        <h2>Přihlašte se a začněte psát články</h2>
                    </div>

                    <div class="form-box box">
                        <form action="./" method="POST" autocomplete="off">
                            <h3>Přihlášení</h3>

                            <label>
                                <input type="text" name="username" autocomplete="off" autofocus="autofocus" name="username" placeholder="Uživatelské jméno" required="required" />
                            </label>

                            <label>
                                <input type="password" name="password" autocomplete="off" name="password" placeholder="Heslo" required="required" />
                            </label>

                            <button type="submit" disabled="disabled">Přihlásit se</button>

                            <a href="../">Zpět na články</a>
                        </form>
                    </div>
                </div>
            </body>
            </html>
        <?php
    }
?>