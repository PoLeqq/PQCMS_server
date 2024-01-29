<?php

header('Content-Type: application/json; charset=utf-8');

require_once(dirname(__DIR__, 2) . "/utils/APIUtils.php");
APIUtils::validatePost($_POST);

require_once(dirname(__DIR__,3)."/objects/website/WebsitePermissions.php");

$response = ["suc" => 1, "resp" => WebsitePermissions::getPermissionsDescriptions(APIUtils::getWebsite($_POST)->getId())];
APIUtils::logAPI($_POST,$response);
echo json_encode($response,JSON_UNESCAPED_UNICODE);