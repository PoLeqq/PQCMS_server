<?php

    session_start();
    if(!empty($_SESSION["electrocms-admin-logged"])) {
        header("location: ../");
        die("<pre>You are already logged in! If not redirected, try refreshing the page.</pre>");
    }

    $_SESSION["token-server-login"] = bin2hex(random_bytes(32));
    $_SESSION["token-server-login-expire"] = time() + 600;

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ElectroCMSServer - Login</title>

    <link rel="stylesheet" href="main.css">

    <script src="main.js" defer></script>
</head>
<body>
    <form action="login.php" method="post">
        <input type="hidden" name="token" value="<?php echo $_SESSION['token-server-login'] ?>">

        Username: <input type="text" name="username" required>
        Password: <input type="password" name="password" id="password" required>
        <div>
            <input type="checkbox" id="show_password"> Pokaż hasło
        </div>
        <input type="submit" value="Zaloguj">
    </form>
</body>
</html>