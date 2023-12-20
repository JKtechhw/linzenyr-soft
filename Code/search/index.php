<?php 
    header('Content-Type: application/json; charset=utf-8');
    $request_url = $_SERVER['REQUEST_URI'];

    if (strpos($request_url, '.php') !== false && substr($request_url, -4) === '.php') {
        $responseText = array(
            "success" => false,
            "message" => "Nemáte oprávnění zobrazit tento soubor"
        );
        
        http_response_code(403);
        echo(json_encode($responseText, JSON_UNESCAPED_UNICODE));
        exit();
    }

    require_once(__DIR__ . "/../src/dbConnect.php");

    if(isset($_GET["q"]) == false || empty($_GET["q"])) {
        $articles = [];
    }

    else {
        $query = urldecode($_GET["q"]);
            $articles = Db::queryAll("
            SELECT articles.*, CONCAT(users.firstname, ' ', users.lastname) AS author, users.login, IF(users.avatar IS NULL, 'default.png', users.avatar) AS avatar
            FROM articles
            INNER JOIN users ON articles.author = users.userID
            RIGHT JOIN article_tag ON article_tag.article = articles.articleID
            LEFT JOIN tags ON tags.tagID = article_tag.tag
            WHERE articles.title LIKE ?
            ORDER BY articles.articleID DESC
        ", "%" . $query . "%");
    }



    echo(json_encode($articles, JSON_UNESCAPED_UNICODE));

?>