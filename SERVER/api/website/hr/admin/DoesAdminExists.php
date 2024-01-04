<?php

header('Content-Type: application/json; charset=utf-8');
require_once(dirname(__DIR__, 3) . "/utils/APIUtils.php");

APIUtils::validatePost($_POST,basename(__FILE__));

$website = APIUtils::getWebsite($_POST);
echo json_encode(["suc" => 1, "resp" => $website->getAdminId() != null],JSON_UNESCAPED_UNICODE);