<?php

header('Content-Type: application/json; charset=utf-8');
require_once(dirname(__DIR__,2)."/utils/APIUtils.php");

$postValidatorResponse = APIUtils::validatePostForAuthKey($_POST,basename(__FILE__));
if($postValidatorResponse["suc"] === 0)
    die(json_encode($postValidatorResponse,JSON_UNESCAPED_UNICODE));

if(empty($_POST["auth_key"]) || empty($_POST["perms"]))
    die(json_encode(["suc" => 0, "desc" => "Sprawdź poprawność post'ów!"],JSON_UNESCAPED_UNICODE));

if(!is_array($_POST["perms"]) || !allArrayValuesAreStrings($_POST["perms"]))
    die(json_encode(["suc" => 0, "desc" => "Walidacja danych nie powiodła się! (perms)"]));

function allArrayValuesAreStrings($array): bool
{
    return count($array) === count(array_filter($array, 'is_string'));
}
require_once(dirname(__DIR__,3)."/objects/Website.inc.php");
$website = APIUtils::getWebsite($_POST);

$issetPermissions = $website->issetPermissions($_SERVER["REMOTE_ADDR"],$_POST["auth_key"],$_POST["perms"]);
//$response["resp"] = $issetPermissions["suc"];
//if(isset($issetPermissions["desc"]))
//    $response["desc"] = $issetPermissions["desc"];
//if(isset($issetPermissions["perms"]))
//    $response["perms"] = $issetPermissions["perms"];

die(json_encode($issetPermissions,JSON_UNESCAPED_UNICODE));