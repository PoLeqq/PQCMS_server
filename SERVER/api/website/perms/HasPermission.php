<?php

require_once "PermsCommonCodeAPI.inc.php";
verifyPosts();

require_once(dirname(__DIR__, 3) . "/objects/Website.inc.php");
$website = APIUtils::getWebsite($_POST);

if(!empty($_POST["username"]) && is_string($_POST["username"]))
{
    $safeWebsite = APIUtils::getSafeWebsite($_POST);
    // todo zrobić to przez safe website
    die(json_encode($website->hasPermissionsByUsername($_POST["username"],$_POST["perms"]),JSON_UNESCAPED_UNICODE));
}
die(json_encode($website->hasPermissions($_SERVER["REMOTE_ADDR"],$_POST["auth_key"],$_POST["perms"]),JSON_UNESCAPED_UNICODE));