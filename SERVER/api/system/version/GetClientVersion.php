<?php

header('Content-Type: application/json; charset=utf-8');

require_once(dirname(__DIR__, 2) . "/utils/APIUtils.php");
APIUtils::validatePostForAuthKey($_POST,basename(__FILE__));

require_once("Version.inc.php");
$response["version"] = Version::getVersion("client",!empty($_POST["complex"]));
echo json_encode($response,JSON_UNESCAPED_UNICODE);