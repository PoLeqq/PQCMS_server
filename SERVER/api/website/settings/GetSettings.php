<?php
header('Content-Type: application/json; charset=utf-8');
require_once(dirname(__DIR__,2)."/utils/APIUtils.php");

APIUtils::validatePostForAuthKey($_POST,basename(__FILE__));

require_once(dirname(__DIR__,3)."/objects/Website.inc.php");
$website = APIUtils::getWebsite($_POST);
$settings = $website->getSettings();

$permsValues = [
    "logincount" => "login_attempts",
    "loginsessiontime" => "login_session_time",
    "tokenlifespan" => "token_lifespan",
];

$response["suc"] = 1;
foreach($permsValues as $perm => $value)
{
    if($website->hasPermission($_SERVER["REMOTE_ADDR"],$_POST["auth_key"],"pqcms.settings.system.$perm"))
        $response["resp"][$value] = $settings->unsafe_getField($value);
}

//$response["resp"] = [
//    "login_attempts" => $settings->getLoginAttempts(),
//    "login_session_time" => $settings->getLoginSessionTime(),
//    "token_lifespan" => $settings->getTokenLifespan()
//];

die(json_encode($response,JSON_UNESCAPED_UNICODE));