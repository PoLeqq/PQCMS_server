<?php

session_start();

if(empty($_SESSION["pqcms-client-logged"]))
{
    $_SESSION["electrocms-client-announce-panel"] = ["err" => "Najpierw musisz się zalogować, aby to zrobić!"];
    header("location: ../");
    die("Nieprawidłowe przekierowanie. ".$_SESSION["electrocms-client-announce-panel"]["err"]);
}

if(!isset($_POST["token"]))
{
    $_SESSION["pqcms-client-announce-panel"] = ["err" => "Najpierw musisz się zalogować, aby to zrobić!"];
    header("location: ../");
    die("Nieprawidłowe przekierowanie. ".$_SESSION["electrocms-client-announce-panel"]["err"]);
}

if($_SESSION["pqcms-client-token-next-account"] != $_POST["token"])
{
    $_SESSION["electrocms-client-announce-panel"] = ["err" => "Nieprawidłowy token."];
    header("location: ../");
    die("Nieprawidłowe przekierowanie. ".$_SESSION["electrocms-client-announce-panel"]["err"]);
}

if(empty($_POST["username"]) || empty($_POST["nickname"]) || empty($_POST["password"]))
{
    $_SESSION["electrocms-client-announce-panel"] = ["err" => "Wypełnij wszystkie pola!"];
    header("location: ../");
    die("Nieprawidłowe przekierowanie. ".$_SESSION["electrocms-client-announce-panel"]["err"]);
}

if(strlen($_POST["username"]) < 5 || strlen($_POST["username"]) > 30)
{
    $_SESSION["electrocms-client-announce-panel"] = ["err" => "Login musi mieć od 5 do 30 znaków!"];
    header("location: ../");
    die("Nieprawidłowe przekierowanie. ".$_SESSION["electrocms-client-announce-panel"]["err"]);
}

if(strlen($_POST["nickname"]) < 5 || strlen($_POST["nickname"]) > 30)
{
    $_SESSION["electrocms-client-announce-panel"] = ["err" => "Nazwa użytkownika musi mieć od 5 do 30 znaków!"];
    header("location: ../");
    die("Nieprawidłowe przekierowanie. ".$_SESSION["electrocms-client-announce-panel"]["err"]);
}

if(strlen($_POST["password"]) < 8 || strlen($_POST["password"]) > 40)
{
    $_SESSION["electrocms-client-announce-panel"] = ["err" => "Hasło musi mieć od 8 do 40 znaków!"];
    header("location: ../");
    die("Nieprawidłowe przekierowanie. ".$_SESSION["electrocms-client-announce-panel"]["err"]);
}

require_once(dirname(__DIR__, 2) . "/objects/Website.inc.php");
require_once(dirname(__DIR__, 2) . "/objects/website/WebsiteUser.inc.php");
$website = new Website($_SESSION["electrocms-client-website-id"]);
if(WebsiteUser::getWebsiteUserBy("username",$_POST["username"],$_SESSION["electrocms-client-website-id"]) != null)
{
    $_SESSION["electrocms-client-announce-panel"] = ["err" => "Ta domena posiada już konto o podanej nazwie użytkownika!"];
    header("location: ../");
    die("Nieprawidłowe przekierowanie. ".$_SESSION["electrocms-client-announce-panel"]["err"]);
}

$website->addUser($_POST["username"],$_POST["nickname"],$_POST["password"],[],!isset($_POST["active"]));
$_SESSION["electrocms-client-announce-panel"] = ["suc" => "Dodano konto użytkownika!"];
header("location: ../");
die("Dodano konto użytkownika. Błędne przekierowanie!");