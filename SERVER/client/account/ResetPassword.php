<?php

if(empty($_GET["token"]))
    die("Dostęp zabroniony.");

?>

<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset hasła - PQCMS</title>
</head>
<body>
    <form method="post" action="ResetPass.php">
        <input type="hidden" name="token" value="<?php echo $_GET["token"] ?>"/>
        <label>
            Hasło: <input name="password" type="password"/>
        </label>
        <br/><br/>
        <label>
            Powtórz hasło: <input name="password_confirm" type="password"/>
        </label>
        <br/><br/>
        <input type="submit" value="Zmień hasło"/>
    </form>
</body>
</html>