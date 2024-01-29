<?php

header('Content-Type: application/json; charset=utf-8');

require_once(dirname(__DIR__, 3) . "/utils/APIUtils.php");
APIUtils::validatePostForAuthKey($_POST);

$website = APIUtils::getSafeWebsite($_POST);

$response = $website->getRanks();

APIUtils::logAPI($_POST,$response);
die(json_encode($response,JSON_UNESCAPED_UNICODE));