<?php 
    $request_url = $_SERVER['REQUEST_URI'];
    if (strpos($request_url, '.php') !== false && substr($request_url, -4) === '.php') {
        http_response_code(403);
        echo "Forbidden";
        exit();
    }

    $pathToSources = "../";
    include("../partials/header.php");

    $editions = Db::queryAll("SELECT * FROM editions");
?>

<main>
    <div class="content-header">
        <h3>Vydání</h3>
    </div>

    <div id="page-content">
    <?php
        if(count($editions) == 0) {
    ?>

        <div class="box-message">
            <h1 class="box-message-title">Zatím nebylo vytvořeno žádné vydání</h1>
        </div>

    <?php 
        }

        else {
    ?>

        <div class="table-box">
            <table id="editions-table">
                <thead>
                    <tr>
                        <th>Název</th>
                        <th>Datum</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        foreach($editions as $edition) {
                    ?>
                        <tr>
                            <td>
                                <?php echo($edition["title"]); ?>
                            </td>

                            <td>
                                <?php echo($edition["date"]); ?>
                            </td>

                            <td>
                                <a href="../assets/editions/<?php echo($edition["basename"] . "-" . $edition["editionID"] . ".pdf"); ?>">Stáhnout</a>
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
    </div>
</main>

<div id="suggestions-container">
    <div id="search-box">
        <input id="search" type="search" placeholder="Vyhledat článek..." autocomplete="off"  />
    </div>
</div>

<?php 
    include("../partials/footer.php");
?>