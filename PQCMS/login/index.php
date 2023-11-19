<?php
// todo połączenie jakoś tego z serwerem
//    require_once(dirname(__DIR__)."/config/settings/JSONLogin.php");
//    $login = new JSONLogin();
//    $_SESSION["loginAmount"] = $login->getAttempts();

    session_start();
    if(!empty($_SESSION["pqcms-panel-username"]))
        header("location: ../panel");

//    require_once("loginUser.php");
//    if(isset($_POST["submit"])){
//        $error = loginUser($_POST["username"],$_POST["password"]);
//    }
?>

<!doctype html>
<html lang="pl-PL">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <title>Logowanie | PQCMS</title>
    <meta name="description" content="Panel logowania do systemu PQCMS">
    <meta name="author" content='Wiktor "PoLeq" Soliński'>
    <meta http-equiv="X-Ua-Compatible" content="IE=edge">
    <link rel="icon" type="image/x-icon" href="../../images/ElectroCMS.svg">

    <link rel="stylesheet" href="../../bs5/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../default.css">
    <link rel="stylesheet" href="index.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400&display=swap" rel="stylesheet">
  
</head>
<body>

    <div id="site-container" class="d-flex justify-content-center align-items-center text-center">

        <form method="POST" class="p-4 w-25" action="Login.php">
            <header class="mb-4">
                <a id="main-link" class="navbar-brand fs-2 px-3 link-nav text-white" style="font-size: 40px!important;" href="../../index.html">
                    PQCMS
                    <img src="../../images/ElectroCMS.svg" alt="logo">
                </a>
            </header>

            <fieldset class="form-group border border-white d-flex flex-column justify-content-center align-items-center" >
                <legend class="w-75 h2 pb-2 border border-white">Logowanie</legend>

<!--                <p class="text-danger mt-2">--><?php //echo @$error;?><!--</p>-->

                <label class="mt-1">Nazwa użytkownika</label>
                <input type="text" name="username" class="w-75 form-control-lg m-2 rounded-0" placeholder="nazwa użytkownika" value="<?php echo @$_POST['username'];?>" />

                <label class="mt-3">Hasło</label>
                <div class="w-75 m-0">
                    <input type="password" id="password" name="password" class="form-control-lg my-2 rounded-0" placeholder="hasło" value="<?php echo @$_POST['password'];?>" />
<!--                    TODO (raczej SEO też zalicza, bo niby to też jest jakoś dostępna strona - trzeba przerobić, żeby było src w img -->
                    <img id="showPass" class="hidePass showPass">
                </div>

                <input type="submit" name="submit" class="btn btn-primary my-4 rounded-0" value="Zaloguj">

            </fieldset>
            <?php
               require_once(dirname(__DIR__)."/initializer/Checker.php");
               if(is_null(isFirstTime()))
                   echo "Błąd API! Skontaktuj się z administratorem PQCMS!";
               if(isFirstTime()) {
                   echo<<<END
                        <div style="text-align: left">
                            Pierwszy raz? <a href="../initializer/">Kliknij tutaj!</a>
                        </div>
                   END;
               }
            ?>
            <?php
                if(!empty($_SESSION["pqcms-panel-login-error"]))
                    echo<<<END
                        <div style="color: red">
                            {$_SESSION["pqcms-panel-login-error"]}
                        </div>
                    END;

            ?>
        </form>
        
    </div>

    <script src="passwordScript.js"></script>
    <script src="../../bs5/js/bootstrap.min.js"></script>
</body>
</html>