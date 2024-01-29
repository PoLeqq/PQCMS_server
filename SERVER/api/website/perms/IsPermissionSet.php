<?php

require_once "PermsCommonCodeAPI.inc.php";
verifyPosts();

require_once(dirname(__DIR__, 3) . "/objects/Website.inc.php");
$website = APIUtils::getWebsite($_POST);

$response = $website->issetPermissions($_POST["client_ip"],$_POST["auth_key"],$_POST["perms"]);
APIUtils::logAPI($_POST,$response);
die(json_encode($response,JSON_UNESCAPED_UNICODE));