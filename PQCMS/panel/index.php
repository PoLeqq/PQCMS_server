<?php
require_once("../utils/database/Database.inc.php");
$setupDatabase = (Database::setupDefaultDatabase());
session_start();
if(empty($_SESSION["pqcms-panel-username"]))
    header("location: ../");
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PQCMS - Panel</title>

    <link rel="stylesheet" href="panel.css">

    <script src="panel.js" defer></script>
</head>
<body>
    <div id="main">
        <nav>
            <ul>
                <li id="logo" class="internalLink" internalLink="home/"><img src="../../images/ElectroCMS.svg" alt="logo"></li>
                <li class="internalLink" internalLink="site/">Site</li>
                <li class="internalLink" internalLink="data/">Data</li>
                <li class="internalLink" internalLink="hr/">HR</li>
                <li class="internalLink" internalLink="logs/">Logi</li>
            </ul>
        </nav>
        <iframe id="panelMain" src="home/"></iframe>
    </div>
    <footer>
        PQCMS &copy Wszelkie prawa zastrzeżone.<br>
        Kontakt: xxx
    </footer>

    <script>
        // Funkcja do odświeżania iframe "HR" w panelu
        function refreshHRFrame() {
            var hrFrame = document.getElementById("panelMain");
            hrFrame.src = hrFrame.src;
        }
    </script>
</body>
</html>