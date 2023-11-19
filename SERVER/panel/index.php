<?php

    session_start();
    if(empty($_SESSION["pqcms-server-admin-logged"])) {
        header("location: login/");
        die("You need to be logged in to visit this resouce!");
    }

    $_SESSION["pqcms-server-token-addwebsite"] = bin2hex(random_bytes(32));
    $_SESSION["pqcms-server-token-addwebsite-expire"] = time() + 600;

    $_SESSION["pqcms-server-token-checklicense"] = bin2hex(random_bytes(32));
    $_SESSION["pqcms-server-token-checklicense-expire"] = time() + 600;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="main.css">

    <script src="main.js" defer></script>
</head>
<body>
    <form action="actions/addWebsite.php" method="post">
        <input type="hidden" name="token" value="<?php echo $_SESSION["pqcms-server-token-addwebsite"] ?>">

        Domena: <input type="text" name="domain" required>
        Login: <input type="text" name="login" required>
        Data wygaśnięcia: <input type="datetime-local" name="expiry_date" required id="expiry_date" style="margin-bottom: 10px;">

        <div>
            <input type="checkbox" name="perm_license" id="perm_license"> 
            <label for="perm_license">Permanentna licencja</label>
        </div>
        Zablokowany: <input type="number" name="blocked">
        <input type="submit" value="Dodaj klienta">
    </form>

    <form action="actions/checkLicense.php" method="post">
        <input type="hidden" name="token" value="<?php echo $_SESSION["pqcms-server-token-checklicense"] ?>">

        IP serwera: <input name="server">
        Domena: <input name="domain">
        Login: <input name="login">
        License Key: <input name="license_key">
        <input type="submit" value="Is Valid?">
    </form>

    <form action="actions/checkLicense.php" method="post">
        Form do sprawdzania tabel<br>
        <input type="hidden" name="token" value="<?php echo $_SESSION["pqcms-server-token-checklicense"] ?>">

        Login: <input name="login">
        License Key: <input name="license_key">
        <input type="submit" value="Is Valid?">
    </form>

    <form action="actions/logout.php" method="post">
        <input type="submit" value="Wyloguj">
    </form>
</body>
</html>