<?php

header('Content-Type: application/json; charset=utf-8');
require_once(dirname(__DIR__,2)."/utils/APIUtils.php");

$response = APIUtils::validatePost($_POST,basename(__FILE__));
if($response["suc"] == 0)
    die(json_encode($response,JSON_UNESCAPED_UNICODE));

$authKeyValidate = APIUtils::validatePostForAuthKey($_POST);
if($authKeyValidate["suc"] === 0)
    die(json_encode($authKeyValidate,JSON_UNESCAPED_UNICODE));

if(empty($_POST["auth_key"]) || empty($_POST["perms"]))
    die(json_encode(["suc" => 0, "desc" => "Sprawdź poprawność post'ów!"],JSON_UNESCAPED_UNICODE));

$validatorResponse = Validator::validate([$_POST["auth_key"]],["s(128)"]);
if($validatorResponse["suc"] === 0)
    die(json_encode(["suc" => 0, "desc" => "Walidacja danych nie powiodła się! (auth_key)"]));
if(!is_array($_POST["perms"]) || !allArrayValuesAreStrings($_POST["perms"]))
    die(json_encode(["suc" => 0, "desc" => "Walidacja danych nie powiodła się! (perms)"]));

function allArrayValuesAreStrings($array): bool
{
    return count($array) === count(array_filter($array, 'is_string'));
}
require_once(dirname(__DIR__,3)."/objects/Website.inc.php");
$website = APIUtils::getWebsite($_POST);

$hasPermission = $website->hasPermissions($_SERVER["REMOTE_ADDR"],$_POST["auth_key"],$_POST["perms"]);
$response["resp"] = $hasPermission["suc"];
if(isset($hasPermission["desc"]))
    $response["desc"] = $hasPermission["desc"];
if(isset($hasPermission["user_perms"]))
    $response["user_perms"] = $hasPermission["user_perms"];
if(isset($hasPermission["perms"]))
    $response["perms"] = $hasPermission["perms"];

die(json_encode($response,JSON_UNESCAPED_UNICODE));