<?php

require_once "PermsCommonCodeAPI.inc.php";
require_once(dirname(__DIR__, 3) . "/objects/Website.inc.php");
$website = APIUtils::getWebsite($_POST);
die(json_encode($website->hasPermissions($_SERVER["REMOTE_ADDR"],$_POST["auth_key"],$_POST["perms"]),JSON_UNESCAPED_UNICODE));