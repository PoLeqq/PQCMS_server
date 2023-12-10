<?php

header("content-type: application/json");

session_start();
if(!empty($_SESSION["pqcms-client-system-user_id"]))
{
    header("location: ../");
    die("Sesja jest już aktywna. Niepoprawne przekierowanie!");
}

if(empty($_POST["domain"]) || empty($_POST["login"]) || empty($_POST["password"]))
    die("Przesłano nieprawidłowe dane (posts). Jeśli uważasz, że to błąd, skontaktuj się z administratorem PQCMS!");

require_once(dirname(__DIR__,3)."/objects/Website.inc.php");
$websiteId = Website::getWebsiteIDByMatching("domain",$_POST["domain"]);
if(is_null($websiteId))
    die("Nie odnaleziono strony o podanej domenie");

$website = new Website($websiteId);
if(!$website->doesExists())
    die("Strona o podanej domenie nie istnieje.");

if($website->isBlocked())
    die("Strona o podanej domenie jest zablokowana!");

if($website->isExpired())
    die("Strona o podanej domenie straciła licencję!");

$result = $website->internalLoginUser($_SERVER["REMOTE_ADDR"],$_POST["login"],$_POST["password"]);

if($result["suc"] == 1)
{
    $_SESSION["pqcms-client-system-user_id"] = $result["id"];
    $_SESSION["pqcms-client-system-is_admin"] = $result["admin"];

    if(!empty($_SESSION["pqcms-client-system-login_redirect"]))
        header("location: ../report/".$_SESSION["pqcms-client-system-login_redirect"].".php");
    else
        header("location: ./");

    die("Zalogowano. Nieprawidłowe przekierowanie.");
}
else die("Niepoprawne dane logowania.");