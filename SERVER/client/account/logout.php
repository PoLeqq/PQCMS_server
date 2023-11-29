<?php

session_start();

if(empty($_SESSION["pqcms-client-logged"]))
{
    header("location: ../");
    die("Nie jesteś jeszcze zalogowany! Błędne przekierowanie.");
}

//session_destroy();
require_once(dirname(__DIR__, 2) . "/objects/Website.inc.php");
echo "<pre>";
var_dump(Website::getWebsiteIDByMatching("domain",$_SESSION["pqcms-client-domain"]));
echo "\n\n";
var_dump($_SESSION);
echo "\n\n";

foreach(array_keys($_SESSION) as $sessionKey) {
    if(str_starts_with($sessionKey,"pqcms-client"))
        unset($_SESSION[$sessionKey]);
}

echo "\n\n";
var_dump($_SESSION);
echo "</pre>";

header("location: ../");
die("Wylogowano. Niepoprawne przekierowanie.");