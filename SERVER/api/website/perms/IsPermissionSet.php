<?php

require_once "PermsCommonCodeAPI.inc.php";
verifyPosts(basename(__FILE__, '.php'));

require_once(dirname(__DIR__, 3) . "/objects/Website.inc.php");
$website = APIUtils::getWebsite($_SERVER["REMOTE_ADDR"],$_POST);

$response = $website->issetPermissions($_POST["client_ip"],$_POST["auth_key"],$_POST["perms"]);
APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$response);