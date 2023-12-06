<?php

var_dump($_POST);

session_start();
if(!empty($_SESSION["pqcms-client-system-user_id"]))
{
    header("location: ../");
    die("Sesja jest już aktywna. Niepoprawne przekierowanie!");
}

var_dump(empty($_POST["domain"]));
var_dump(empty($_POST["login"]));
var_dump(empty($_POST["password"]));
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

$website->isExpired();
