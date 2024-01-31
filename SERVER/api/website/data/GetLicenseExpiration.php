<?php
header('Content-Type: application/json; charset=utf-8');
require_once(dirname(__DIR__,2)."/utils/APIUtils.php");

APIUtils::validatePostForAuthKey($_SERVER["REMOTE_ADDR"],basename(__FILE__, '.php'),$_POST);

require_once(dirname(__DIR__,3)."/objects/Website.inc.php");
$website = APIUtils::getSafeWebsite($_SERVER["REMOTE_ADDR"],$_POST);

//pqcms.?.licenseexpiration
//if()

$response = $website->getLicenseExpiration();
APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$response);