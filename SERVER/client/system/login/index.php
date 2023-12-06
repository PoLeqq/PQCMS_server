<?php

session_start();
if(!empty($_SESSION["pqcms-client-system-user_id"]))
{
    header("location: ../");
    die("Sesja jest już aktywna. Niepoprawne przekierowanie!");
}
?>

<!doctype html>
<html lang="pl-PL">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Strona główna | PQCMS</title>
    <meta name="description" content="Oficjalna strona systemu PQCMS">
    <meta name="keywords" content="">
    <meta name="author" content='Wiktor "PoLeq" Soliński'>
    <meta http-equiv="X-Ua-Compatible" content="IE=edge">
    <link rel="icon" type="image/x-icon" href="../../../images/PQCMS.svg">

    <link rel="stylesheet" href="../../../../bs5/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../../../default.css">
    <link rel="stylesheet" href="../../../../index.css">
    <link rel="stylesheet" href="login.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400&display=swap" rel="stylesheet">

    <meta content="PQCMS" property="og:title" />
    <meta content="Prosty a zarazem szybki i niezawodny system CMS, który jest ciągle rozwijany!" property="og:description" />
    <!-- <meta content="https://poleq.pl/" property="og:url" /> -->
    <!-- <meta content="https://poleq.pl/img/trex.jpg" property="og:image" /> -->
    <!-- <meta content="#6204dd" config-react-helmet="true" name="theme-color" /> -->

    <script src="passwordScript.js" defer></script>

</head>
<body>

<noscript>
    UWAGA! Korzystanie ze strony z wyłączonym JavaScriptem uniemożliwia korzystanie ze strony w pełni pięknej i funkcjonalnej! Rób jak uważasz!
</noscript>
<!--<div id="main-element" class="block-background">-->
<div id="site-container" class="d-flex justify-content-center align-items-center text-center py-5">
    <form method="POST" class="col-6 p-4" action="Login.php">
        <header class="mb-4">
            <a id="main-link" class="navbar-brand fs-2 px-3 link-nav text-white" style="font-size: 40px!important;" href="../../">
                PQCMS
                <img src="../../../images/PQCMS.svg" alt="logo">
            </a>
        </header>

        <fieldset class="form-group border border-white d-flex flex-column justify-content-center align-items-center" >
            <legend class="w-75 h2 pb-2 border border-white">Logowanie</legend>
            <i class="mb-3 small">
                Sesja jest już najprawdopodobniej aktywna na Twojej stronie, jednak nie jest przechowywana na naszym
                serwerze. Musimy wiedzieć, kto zgłasza problem, aby móc się skontaktować. Prosimy o zalogowanie.
            </i>

            <label class="mt-1" for="domain">Domena</label>
            <input type="text" id="domain" name="domain" class="w-75 form-control-lg m-2 rounded-0" placeholder="twojadomena.pl"/>

            <label class="mt-1" for="login">Nazwa użytkownika</label>
            <input type="text" id="login" name="login" class="w-75 form-control-lg m-2 rounded-0" placeholder="login"/>

            <label class="mt-3" for="password">Hasło</label>
            <div class="d-flex w-75 justify-content-center align-items-center">
                <input type="password" id="password" name="password" class="form-control-lg my-2 rounded-0" placeholder="hasło" value="<?php echo @$_POST['password'];?>" />
                <img id="showPassword" class="showPassword hidePassword" src="images/showPassword.svg" alt="oko" tabindex="0">
            </div>

            <input type="submit" name="submit" class="btn btn-primary my-4 rounded-0" value="Zaloguj">

        </fieldset>
    </form>
<!--        <form action="Login.php" method="post" class="d-flex flex-column">-->
<!--            <h1>Logowanie - PQCMS</h1>-->

<!---->
<!--            <label>-->
<!--                Domena:-->
<!--                <input name="domain" placeholder="mojadomena.pl"/>-->
<!--            </label>-->
<!---->
<!--            <label>-->
<!--                Login:-->
<!--                <input name="domain" placeholder="login123"/>-->
<!--            </label>-->
<!---->
<!--            <label>-->
<!--                Hasło:-->
<!--                <input name="domain"/>-->
<!--            </label>-->
<!---->
<!--            <input type="submit" value="Zaloguj się"/>-->
<!---->
<!--        </form>-->
    </div>
</body>
</html>
<!--            <b>Witaj w głównym panelu systemu! Bardzo cieszymy się, że korzystasz z naszysz usług. Naszym priorytetem jest, aby PQCMS był najwydajniejszy,-->
<!--            a jednocześnie spełniał wszystkie warunki bezpieczeństwa.</b> Tutaj jeszcze jakieś inne bzdury ładnie wyglądające.-->