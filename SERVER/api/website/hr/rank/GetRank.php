<?php

header('Content-Type: application/json; charset=utf-8');

require_once(dirname(__DIR__, 3) . "/utils/APIUtils.php");
APIUtils::validatePostForAuthKey($_POST);

$website = APIUtils::getSafeWebsite($_POST);
die(json_encode($website->getRanks(),JSON_UNESCAPED_UNICODE));