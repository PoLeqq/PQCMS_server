<?php

header('Content-Type: application/json');

require_once(dirname(__DIR__, 2) . "/utils/APIUtils.php");
$response = APIUtils::validatePost($_POST,basename(__FILE__));
if($response["suc"] == 0)
    die(json_encode($response,JSON_UNESCAPED_UNICODE));

require_once("Version.inc.php");
$response["version"] = Version::getVersion("client",!empty($_POST["complex"]));
echo json_encode($response,JSON_UNESCAPED_UNICODE);