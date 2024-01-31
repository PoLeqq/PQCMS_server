<?php

header('Content-Type: application/json; charset=utf-8');

require_once(dirname(__DIR__, 2) . "/utils/APIUtils.php");
APIUtils::validatePost($_SERVER["REMOTE_ADDR"],basename(__FILE__, '.php'),$_POST);

require_once(dirname(__DIR__,3)."/objects/website/WebsitePermissions.php");

$response = ["suc" => 1, "resp" => WebsitePermissions::getPermissionsDescriptions(APIUtils::getWebsite($_SERVER["REMOTE_ADDR"],$_POST)->getId())];
APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$response);