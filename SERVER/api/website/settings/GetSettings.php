<?php
header('Content-Type: application/json; charset=utf-8');
require_once(dirname(__DIR__,2)."/utils/APIUtils.php");

$response = APIUtils::validatePost($_POST,basename(__FILE__));
if($response["suc"] == 0)
    die(json_encode($response,JSON_UNESCAPED_UNICODE));

$authKeyValidate = APIUtils::validatePostForAuthKey($_POST);
if($authKeyValidate["suc"] === 0)
    die(json_encode($authKeyValidate,JSON_UNESCAPED_UNICODE));

// todo czy ma permisje
//if($)

require_once(dirname(__DIR__,3)."/objects/Website.inc.php");
$website = APIUtils::getWebsite($_POST);
$settings = $website->getSettings();

$response["resp"] = [
    "login_attempts" => $settings->getLoginAttempts(),
    "login_session_time" => $settings->getLoginSessionTime(),
    "token_lifespan" => $settings->getTokenLifespan()
];

die(json_encode($response,JSON_UNESCAPED_UNICODE));