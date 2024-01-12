<?php
header('Content-Type: application/json; charset=utf-8');
require_once(dirname(__DIR__,2)."/utils/APIUtils.php");

APIUtils::validatePostForAuthKey($_POST);

require_once(dirname(__DIR__,3)."/objects/Website.inc.php");
$website = APIUtils::getSafeWebsite($_POST);

if()
die(json_encode($website->getLicenseExpiration(),JSON_UNESCAPED_UNICODE));