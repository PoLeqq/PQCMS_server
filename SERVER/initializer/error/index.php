<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>ElecroCMS - Inicjator</title>

    <link rel="icon" type="image/x-icon" href="../../PQCMS/images/ElectroCMS.svg">

    <link rel="stylesheet" href="../../../bs5/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../../default.css">
    <link rel="stylesheet" href="../index.css">
    <link rel="stylesheet" href="error.css">
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
                if(!isset($_SESSION["initializer-error"])) {
                    header("location: ../../");
                    die("Niepoprawne przekierowanie.");
                }
                echo $_SESSION["initializer-error"];
                unset($_SESSION["initializer-error"]);
            ?>
        </form>
    </div>
</body>
</html>