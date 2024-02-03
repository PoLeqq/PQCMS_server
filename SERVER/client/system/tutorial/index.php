<?php

//session_start();
//if(empty($_SESSION["pqcms"]["client"]["system"]["token"]))
//{
//    header("location: ../");
//    die("Najpierw się zaloguj do panelu PQCMS. Niepoprawne przekierowanie!");
//}
?>


<!DOCTYPE html>
<html lang="pl-PL">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Poradnik | PQCMS</title>
    <meta name="description" content="Oficjalna strona systemu PQCMS">
    <meta name="keywords" content="">
    <meta name="author" content='Wiktor "PoLeq" Soliński'>
    <meta http-equiv="X-Ua-Compatible" content="IE=edge">
    <link rel="icon" type="image/x-icon" href="../../../images/PQCMS.svg">

    <link rel="stylesheet" href="../../../bs5/css/bootstrap.min.css">
<!--    <link rel="stylesheet" href="../default.css">-->
    <link rel="stylesheet" href="index.css">
<!--    <link rel="stylesheet" href="../index.css">-->
    <link rel="stylesheet" href="../homepage.css">

    <meta content="PQCMS" property="og:title"/>
    <meta content="Prosty a zarazem szybki i niezawodny system CMS, który jest ciągle rozwijany!"
          property="og:description"/>
    <!-- <meta content="https://poleq.pl/" property="og:url" /> -->
    <!-- <meta content="https://poleq.pl/img/trex.jpg" property="og:image" /> -->
    <!-- <meta content="#6204dd" config-react-helmet="true" name="theme-color" /> -->

</head>
<body>

<noscript>
    UWAGA! Korzystanie ze strony z wyłączonym JavaScriptem uniemożliwia korzystanie ze strony w pełni pięknej i
    funkcjonalnej! Rób jak uważasz!
</noscript>

    <div id="site-container">

        <nav class="navbar navbar-expand-lg navbar-dark">

            <div class="container-fluid px-5">

                <a id="main-link" class="navbar-brand fs-2 link-nav" href="#">
                    Panel PQCMS
                    <img src="../../../images/PQCMS.svg" alt="logo">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">

                    <ul class="navbar-nav mb-2 mb-lg-0 fs-4">
                        <li class="nav-item link-nav">
                            <a class="nav-link" aria-current="page" href="../">Powrót</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div id="site" class="col-12 d-flex">
            <ul id="tutorial-nav" class="col-3 py-4 px-3">
                <li><a href="start/" target="content">Start</a></li>
                <li data-category>Edycja strony</li>
                <li><a href="site/format/" target="content">Formatowanie tekstu</a></li>
                <li data-category>HR</li>
                <li><a href="hr/users/" target="content">Użytkownicy</a></li>
                <li><a href="hr/ranks/" target="content">Rangi</a></li>
                <li><a href="hr/permissions/" target="content">Uprawnienia</a></li>
                <li data-category>Ustawienia</li>
                <li><a href="settings/pqcms/" target="content">PQCMS</a></li>
                <li><a href="settings/database/" target="content">Baza danych</a></li>
                <li><a href="settings/system/" target="content">System</a></li>
            </ul>

            <div id="tutorial-iframe" class="col-9">
                <iframe name="content" src="start/" style="width: 100%; height: 100%"></iframe>
            </div>
        </div>

        <footer class="d-flex justify-content-center align-items-center">
            PQCMS &copy Wszelkie prawa zastrzeżone
        </footer>
    </div>

    <script src="../../../bs5/js/bootstrap.min.js"></script>
</body>
</html>