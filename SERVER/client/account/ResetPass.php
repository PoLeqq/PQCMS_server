<?php

if(empty($_POST["token"]))
    die("Dostęp zabroniony.");

if(empty($_POST["password"]) || empty($_POST["password_confirm"]))
    die("Uzupełnij wszystkie pola!");

if($_POST["password"] !== $_POST["password_confirm"])
    die("Podane hasła różnią się!");

require_once(dirname(__DIR__,2)."/objects/website/ResetPasswordToken.php");
$userID = ResetPasswordToken::getUserIDByToken($_POST["token"]);
if(is_null($userID))
    die("Niepoprawny token!");

require_once(dirname(__DIR__,2)."/objects/website/WebsiteUser.inc.php");
$user = new WebsiteUser($userID);
if(!$user->setPassword($_POST["password"]))
    die("Hasło musi mieć 8-40 znaków!");

require_once(dirname(__DIR__,2)."/objects/Website.inc.php");
ResetPasswordToken::invalidateToken($userID);
die("Zmieniono hasło!");