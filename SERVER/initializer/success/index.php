<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>PQCMS - Inicjator</title>

    <link rel="icon" type="image/x-icon" href="../../images/ElectroCMS.svg">

    <link rel="stylesheet" href="../../../bs5/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../../default.css">
    <link rel="stylesheet" href="../index.css">
    <link rel="stylesheet" href="success.css">
</head>
<body>
<div id="site-container" class="d-flex justify-content-center align-items-center text-center">
    <div class="result">
        <header class="mb-4">
            <a id="main-link" class="navbar-brand px-3 text-white" style="font-size: 40px!important;" href="../../../index.html">
                ElectroCMS
                <img src="../../images/ElectroCMS.svg" alt="logo">
            </a>
        </header>

        <?php
            session_start();
            if(!isset($_SESSION["pqcms-initializer-success"])) {
                header("location: ../../");
                die("Niepoprawne przekierowanie.");
            }
            echo $_SESSION["pqcms-initializer-success"];
            unset($_SESSION["pqcms-initializer-success"])
        ?>
        Sukces
        <noscript>
            <a href="http://localhost/pqcms/server/client/">Wykryto wyłączony JavaScript! Kliknij tutaj, aby przekierować.</a>
        </noscript>
    </div>

    <script>
        setTimeout(() => {
             window.location.replace("http://localhost/pqcms/server/client/");
        },3000);
    </script>
</div>
</body>
</html>