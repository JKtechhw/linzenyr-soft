<?php
    $request_url = $_SERVER['REQUEST_URI'];

    if (strpos($request_url, '.php') !== false && substr($request_url, -4) === '.php') {
        http_response_code(403);
        echo "Error 403";
        exit();
    } 

    require_once(__DIR__ . "/../src/dbConnect.php");

    if(isset($pathToSources) == false) {
        $pathToSources = "../";
    }

    $segments = explode('/', trim(parse_url($request_url, PHP_URL_PATH), '/'));
    $page = end($segments);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>O Nás</title>
    <link rel="icon" type="image/png" href="<?php echo($pathToSources); ?>assets/Images/logo-short.png" />
    <link rel="stylesheet" type="text/css" href="<?php echo($pathToSources); ?>css/about.css"" />
    
    </head>
<body>
<div id="about-logo">

<a href="<?php echo($pathToSources); ?>">
<img src="<?php echo($pathToSources); ?>assets/Images/logo-full.png" alt="">
</a>
    
</div>
<div id="about-text">
<h1>Kdo Jsme</h2>
<p>
Jsme Srsťoplsť. Náš časopis poskytuje nejlepší prostředí pro všechny milovníky zvířat, malých či velkých, mladých nebo starých. Milujeme každé jedno zvíře. Pro nás jsou zvířata inspirací stejně jako našimi nejlepšími přáteli. Náš časopis se snaží skloubit vše, co je "NEJLEPŠÍ". Snažíme se přinášet články o celém živočišném království, nejnovější zprávy, rady, jak pečovat o vaše chlupaté přátele, a vzdělávat o exotických zvířatech.

Věděli jste, že kapybary dokážou spát ve vodě? Drží své nosy těsně nad hladinou, aby mohli dýchat.

Každopádně, my vás budeme neustále informovat o nejnovějších a nejlepších nástrojích pro vaše drahé zvířecí přátele a také o tom, na co si dát pozor.

A každý má šanci, tak neváhejte a také se staňte součástí naší chlupaté rodiny <3
</p>

</div>

<div id="about-logo-background">
    <img src="<?php echo($pathToSources); ?>assets/Images/logo-short.png" alt="">
</div>