<?php
// WAŻNA NOTKA DO API

header('Content-Type: application/json; charset=utf-8');
require_once(dirname(__DIR__,2)."/utils/APIUtils.php");

$response = APIUtils::validatePost($_POST,basename(__FILE__));
if($response["suc"] == 0) die(json_encode($response,JSON_UNESCAPED_UNICODE));

if((!isset($_POST["login_count"]) && empty($_POST["login_count_reset"])) || (!isset($_POST["token_lifespan"]) && empty($_POST["token_lifespan_reset"])))
    die(json_encode(["suc" => 0, "desc" => "Uzupełnij wszystkie pola!"],JSON_UNESCAPED_UNICODE));

require_once(dirname(__DIR__,3)."/objects/Website.inc.php");
$website = APIUtils::getWebsite($_POST);

//if(!Validator::validate($_POST["auth_key"],["s(128)"])["suc"])
//    die(json_encode(["suc" =>]))

require_once(dirname(__DIR__,3)."/objects/website/AuthKey.inc.php");
AuthKey::isAdminAuthKey($website->getId(),$_POST["auth_key"]);
$settings = $website->getSettings();
if(!empty($_POST["login_count_reset"])) $settings->resetLoginAttempts();
else $settings->setLoginAttempts($_POST["login_count"]);

if(!empty($_POST["token_lifespan_reset"])) $settings->resetTokenLifespan();
else $settings->setTokenLifespan($_POST["token_lifespan"]);

$response["resp"] = 1;
$response["desc"] = "Zmieniono ustawienia strony!";

die(json_encode($response,JSON_UNESCAPED_UNICODE));