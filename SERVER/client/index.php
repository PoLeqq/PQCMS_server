<?php

    session_start();
    if(empty($_SESSION["pqcms-client-logged"])) {
        header("location: ../");
        die("Nieprawidłowe przekierowanie.");
    }
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>PQCMS - Panel Klienta</title>

    <link rel="icon" type="image/x-icon" href="../images/PQCMS.svg">

    <link rel="stylesheet" href="../../bs5/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../default.css">

    <style>
        body {
            margin: 10px;
            display: flex;
            /*flex-direction: column;*/
            align-items: center;
            justify-content: space-around;
        }

        form {
            display: flex;
            flex-direction: column;
            border: 1px solid white;
            border-radius: 20px;
            padding: 30px;
        }

        form > * {
            margin: 10px 0;
        }
    </style>
</head>
<body>
<?php
if(!empty($_SESSION["pqcms-client-announce-panel"]))
{
    $keys = ["err","suc"];
    $message = null;
    $messageKey = "";
    foreach($keys as $key)
        if(key_exists($key,$_SESSION["pqcms-client-announce-panel"])){
            $message = $_SESSION["pqcms-client-announce-panel"][$key];
            $messageKey = $key;
            break;
        }
    echo<<<END
    <div id="panel-announce" class="{$messageKey}">
        {$message}
    </div>
END;
}

unset($_SESSION["pqcms-client-announce-panel"]);

?>

    <?php

        require_once(dirname(__DIR__) . "/objects/Website.inc.php");
        $website = new Website($_SESSION["pqcms-client-website-id"]);

//        echo "adminId:";
//        var_dump($website->getAdminId());
        if($website->getAdminId() == null)
        {
            $_SESSION["pqcms-client-token-first-account"] = bin2hex(random_bytes(32));
            echo <<<END
    <form action="accounts/addAdminAccount.php" method="post">
        <h3>Konto administratora</h3>
        <p>
            To jest Twoje pierwsze konto! Ma ono dostęp do wszystkich zasobów i czynności, dlatego zalecamy, 
            aby <b><u>nikomu nie podawać danych do logowania!</u></b>
        </p>
        <input type="hidden" name="token" value="{$_SESSION["pqcms-client-token-first-account"]}">

        Login: <input type="text" name="username" required>
        Nazwa użytkownika (wyświetlana): <input type="text" name="nickname" required>
        Hasło (w polu poniżej będzie ono widoczne!): <input type="text" name="password" required>
        E-mail: <input type="email" name="email" required>

        <input type="submit" value="Dodaj konto administratora">
    </form>
END;
        }
        else
        {
//            $_SESSION["pqcms-client-token-next-account"] = bin2hex(random_bytes(32));
            echo "Na razie wszystko zrobione! Powróć do panelu na swojej stronie, aby dokończyć konfigurację.";
//            echo <<<END
//    <form action="accounts/addUserAccount.php" method="post">
//        <input type="hidden" name="token" value="{$_SESSION["pqcms-client-token-next-account"]}">
//
//        Login: <input type="text" name="username" required>
//        Nazwa użytkownika: <input type="text" name="nickname" required>
//        Hasło: <input type="text" name="password" required>
//        Permisje jakoś dodać ;d<br>
//        <div>
//            Aktywne:
//            <input type="checkbox" name="active">
//        </div>
//
//
//        <input type="submit" value="Dodaj konto pracownika">
//    </form>
//END;
        }
    ?>

    <form method="post" action="account/logout.php">
        <input type="submit" value="Wyloguj">
    </form>
</body>
</html>
