<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Přihlášení Srsťoplsť</title>
    <link rel="stylesheet" type="text/css" href="../css/login.css" />
    <link rel="icon" type="image/png" href="../assets/Images/logo-short.png" />
    <script src="../js/login.js"></script>
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
                    <input type="text" name="username" autocomplete="off" autofocus="autofocus" placeholder="Uživatelské jméno" />
                </label>

                <label>
                    <input type="password" name="password" autocomplete="off" placeholder="Heslo"/>
                </label>

                <button type="submit" disabled="disabled">Přihlásit se</button>

                <a href="../">Zpět na články</a>
            </form>
        </div>
    </div>
</body>
</html>