<?php
header('Content-Type: application/json; charset=utf-8');
require_once(dirname(__DIR__,2)."/utils/APIUtils.php");

APIUtils::validatePostForAuthKey($_SERVER["REMOTE_ADDR"],basename(__FILE__, '.php'),$_POST);

require_once(dirname(__DIR__,3)."/objects/Website.inc.php");
$website = APIUtils::getWebsite($_SERVER["REMOTE_ADDR"],$_POST);
$settings = $website->getSettings();

$permsValues = [
    "logincount" => "login_attempts",
    "loginsessiontime" => "login_session_time"
];

$response["suc"] = 1;
foreach($permsValues as $perm => $value)
{
    if($website->hasPermission($_POST["client_ip"],$_POST["auth_key"],"pqcms.settings.system.$perm"))
        $response["resp"][$value] = $settings->unsafe_getField($value);
}

//$response["resp"] = [
//    "login_attempts" => $settings->getLoginAttempts(),
//    "login_session_time" => $settings->getLoginSessionTime(),
//    "token_lifespan" => $settings->getTokenLifespan()
//];

APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$response);