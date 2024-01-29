<?php
header('Content-Type: application/json; charset=utf-8');
require_once(dirname(__DIR__,2)."/utils/APIUtils.php");

APIUtils::validatePostForAuthKey($_POST);

require_once(dirname(__DIR__,3)."/objects/Website.inc.php");
$website = APIUtils::getSafeWebsite($_POST);

//pqcms.?.licenseexpiration
//if()

$response = $website->getLicenseExpiration();
APIUtils::logAPI($_POST,$response);
die(json_encode($response,JSON_UNESCAPED_UNICODE));