<?php
require_once("../utils/database/Database.inc.php");
$setupDatabase = (Database::setupDefaultDatabase());
session_start();
if(empty($_SESSION["pqcms-panel-username"]))
{
    header("location: ../");
    die("Najpierw musisz się zalogować!");
}
unset($_SESSION["pqcms-panel-login-error"]);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PQCMS - Panel</title>

    <link rel="stylesheet" href="panel.css">
    <link rel="icon" href="../images/PQCMS.svg">

    <script src="panel.js" defer></script>

    <script>
        // Funkcja do odświeżania iframe "HR" w panelu
        function refreshHRFrame() {
            document.querySelector("#panelMain").contentWindow.location.reload();
        }

        setInterval(() => {
            var response = checkAuthKeyValidity();
            if (response.trim() !== "") {
                console.log(JSON.parse(response));
            } else {
                console.error("Pusta odpowiedź.");
            }
        },1000);

    </script>
</head>
<body>
    <div id="main">
        <nav>
            <ul>
                <li class="internalLink" internalLink="http://localhost/pqcms/server/client/system/homepage.php" tabindex="1">
                    PQCMS
                    <div class="nav-image">
                        <img src="../images/PQCMS.svg" alt="logo">
                    </div>
                </li>
                <li class="internalLink" internalLink="site/" tabindex="2">
                    Strona
                    <div class="nav-image">
                        <img src="images/edit_site.svg" id="edit_site" alt="Strona">
                    </div>
                </li>
                <li class="internalLink" internalLink="hr/" tabindex="4">
                    HR
                    <div class="nav-image">
                        <img src="images/hr.svg" alt="HR">
                    </div>
                </li>
                <li class="internalLink" internalLink="logs/" tabindex="5">
                    Logi
                    <div class="nav-image">
                        <img src="images/logs.svg" alt="Logi">
                    </div>
                </li>
                <li class="internalLink" internalLink="settings/" tabindex="6">
                    Ustawienia
                    <div class="nav-image">
                        <img src="images/settings.svg" alt="Logi">
                    </div>
                </li>
            </ul>
            <a href="logout/" id="logout">
                Wyloguj się
                <div class="nav-image">
                    <img src="images/logout.svg" alt="logout">
                </div>
            </a>
        </nav>
        <div id="mainIframe">
            <iframe id="panelMain" src="home/"></iframe>
        </div>


        <div id="background"></div>
    </div>
    <footer>
        PQCMS &copy Wszelkie prawa zastrzeżone.
        <a class="d-block" href="http://localhost/pqcms/kontakt/">Kontakt z administratorem</a>
    </footer>
</body>
</html>