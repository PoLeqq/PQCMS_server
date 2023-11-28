<?php
session_start();
if(empty($_SESSION["pqcms-logged_out"]))
{
    header("location: ../");
    die("Niepoprawne przekierowanie.");
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>PQMCS - Wylogowano!</title>

    <link rel="icon" type="image/x-icon" href="../../../images/PQCMS.svg">

    <link rel="stylesheet" href="../../bs5/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../../default.css">
    <link rel="stylesheet" href="../../login/index.css">
</head>
<body>
    <div id="site-container" class="d-flex justify-content-center align-items-center text-center">
        <div class="result">
            <header class="mb-4">
                <a id="main-link" class="navbar-brand fs-2 px-3 link-nav text-white" style="font-size: 40px!important;" href="http://localhost/pqcms/">
                    PQCMS
                    <img src="../../../images/PQCMS.svg" alt="logo">
                </a>
            </header>

            <?php
            if($_SESSION["pqcms-logged_out"]["suc"])
                echo $_SESSION["pqcms-logged_out"]["desc"];
            else
                echo $_SESSION["pqcms-logged_out"]["desc"];
            unset($_SESSION["pqcms-logged_out"]);
            ?>
            <a class="d-block mt-2" href="../../../">Powrót do Twojej strony</a>
        </div>
    </div>
</body>
</html>