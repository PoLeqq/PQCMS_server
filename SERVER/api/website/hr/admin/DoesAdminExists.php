<?php

require_once(dirname(__DIR__, 3) . "/utils/APIUtils.php");
$response = APIUtils::validatePost($_POST);
if($response["suc"] == 0)
    die(json_encode($response,JSON_UNESCAPED_UNICODE));

$website = APIUtils::getWebsite($_POST);
echo json_encode(["suc" => 1, "resp" => $website->getAdminId() != null],JSON_UNESCAPED_UNICODE);