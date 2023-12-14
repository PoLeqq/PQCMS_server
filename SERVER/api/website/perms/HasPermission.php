<?php

header('Content-Type: application/json; charset=utf-8');
require_once(dirname(__DIR__,2)."/utils/APIUtils.php");

$response = APIUtils::validatePost($_POST,basename(__FILE__));
if($response["suc"] == 0) die(json_encode($response,JSON_UNESCAPED_UNICODE));

$validatorResponse = Validator::validate([$_POST["auth_key"],$_POST["perm"]],["s(128)","s"]);
if($validatorResponse["suc"] === 0)
    die(json_encode(["suc" => 0, "desc" => "Walidacja nie powiodła się! (auth_key | perm)"]));

require_once(dirname(__DIR__,3)."/objects/Website.inc.php");
$website = APIUtils::getWebsite($_POST);

$hasPermission = $website->hasPermission($_SERVER["REMOTE_ADDR"],$_POST["auth_key"]);
$response["resp"] = $hasPermission["suc"];
if(isset($hasPermission["desc"])) $response["desc"] = $hasPermission["desc"];

die(json_encode($response,JSON_UNESCAPED_UNICODE));