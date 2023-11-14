<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>ElecroCMS - Inicjator</title>

    <link rel="icon" type="image/x-icon" href="../../PQCMS/images/ElectroCMS.svg">

    <link rel="stylesheet" href="../../../bs5/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../../default.css">
    <link rel="stylesheet" href="../index.css">
    <link rel="stylesheet" href="success.css">
</head>
<body>
<div id="site-container" class="d-flex justify-content-center align-items-center text-center">
    <form method="post" action="http://localhost/electrocms/server/initializer">
        <header class="mb-4">
            <a id="main-link" class="navbar-brand px-3 text-white" style="font-size: 40px!important;" href="../../../index.html">
                ElectroCMS
                <img src="../../PQCMS/images/ElectroCMS.svg" alt="logo">
            </a>
        </header>

        <?php
            session_start();
            if(!isset($_SESSION["initializer-success"])) {
                header("location: ../../server/client/");
                die("Niepoprawne przekierowanie.");
            }
            echo $_SESSION["initializer-success"];
            unset($_SESSION["initializer-success"])
        ?>
        <noscript>
            <a href="http://localhost/electrocms/server/client/">Wykryto wyłączony JavaScript! Kliknij tutaj, aby przekierować.</a>
        </noscript>
    </form>

    <script>
        setTimeout(() => {
             window.location.replace("http://localhost/electrocms/server/client/");
        },3000);
    </script>
</div>
</body>
</html>