<?php

header('Content-Type: application/json; charset=utf-8');
require_once(dirname(__DIR__, 3) . "/utils/APIUtils.php");

APIUtils::validatePost($_POST);

$website = APIUtils::getWebsite($_POST);
$response = ["suc" => 1, "resp" => $website->getAdminId() != null];

APIUtils::logAPI($_POST,$response);
echo json_encode($response,JSON_UNESCAPED_UNICODE);