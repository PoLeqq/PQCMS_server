<?php

session_start();
if(!isset($_SESSION["pqcms-initializer-error"])) {
    header("location: ../../");
    die("Niepoprawne przekierowanie.");
}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>PQCMS - Inicjator</title>

    <link rel="icon" type="image/x-icon" href="../../images/PQCMS.svg">

    <link rel="stylesheet" href="../../../bs5/css/bootstrap.min.css">
    <link rel="stylesheet" href="../default.css">
    <link rel="stylesheet" href="../index.css">
    <link rel="stylesheet" href="error.css">
</head>
<body>
    <div id="site-container" class="d-flex justify-content-center align-items-center text-center">
        <div class="result">
            <header class="mb-4">
                <a id="main-link" class="navbar-brand px-3 text-white" style="font-size: 40px!important;" href="../../../">
                    PQCMS
                    <img src="../../images/PQCMS.svg" alt="logo">
                </a>
            </header>

            <?php
            echo $_SESSION["pqcms-initializer-error"];
            unset($_SESSION["pqcms-initializer-error"]);
            ?>
        </div>
    </div>
</body>
</html>