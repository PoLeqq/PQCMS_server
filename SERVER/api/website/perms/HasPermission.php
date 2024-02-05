<?php

require_once "PermsCommonCodeAPI.inc.php";
verifyPosts(basename(__FILE__, '.php'));

require_once(dirname(__DIR__, 3) . "/objects/Website.inc.php");
$website = APIUtils::getWebsite($_SERVER["REMOTE_ADDR"],$_POST);

// NA RAZIE NIEDOZWOLONE PRZEGLĄDANIE PERMISJI PRZEZ USERNAME, MOŻE KIEDYŚ SIĘ DODA
//if(!empty($_POST["username"]) && is_string($_POST["username"]))
//{
//    $safeWebsite = APIUtils::getSafeWebsite($_SERVER["REMOTE_ADDR"],$_POST);
////    $safeWebsite->hasPermissionsByUsername();
//    APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$website->hasPermissionsByUsername($_POST["username"],$_POST["perms"]));
//}

$response = $website->hasPermissions($_POST["client_ip"],$_POST["auth_key"],$_POST["perms"]);
APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$response);