<?php

session_start();
//session_destroy();
require_once(dirname(__DIR__, 2) . "/objects/Website.inc.php");
echo "<pre>";
var_dump(Website::getWebsiteIDByMatching("domain",$_SESSION["electrocms-client-domain"]));
echo "\n\n";
var_dump($_SESSION);
echo "\n\n";

foreach(array_keys($_SESSION) as $sessionKey) {
    if(str_starts_with($sessionKey,"electrocms-client"))
        unset($_SESSION[$sessionKey]);
}

echo "\n\n";
var_dump($_SESSION);
echo "</pre>";
//    unset($_SESSION["electrocms-client-logged"]);
//    unset($_SESSION["electrocms-client-website-id"]);
//    unset($_SESSION["electrocms-client-"]);
//    header("location: ../");
    die("Wylogowano. Niepoprawne przekierowanie.");