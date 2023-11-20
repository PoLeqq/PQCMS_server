<?php
header('Content-Type: application/json; charset=utf-8');
require_once(dirname(__DIR__,2)."/utils/APIUtils.php");

$response = APIUtils::validatePost($_POST);
if($response["suc"] == 0) die(json_encode($response,JSON_UNESCAPED_UNICODE));

if(empty($_POST["username"]) || empty($_POST["password"]))
    die(json_encode(["suc" => 0, "desc" => "Uzupełnij wszystkie pola!"],JSON_UNESCAPED_UNICODE));

require_once(dirname(__DIR__,3)."/objects/Website.inc.php");
$website = APIUtils::getWebsite($_POST);

$loginUser = $website->loginUser($_POST["username"], $_POST["password"]);
$response["resp"] = $loginUser["suc"];
$response["desc"] = $loginUser["desc"];


//if($website->loginUser($_POST["username"], $_POST["password"]))
//{
//    $response["desc"] = "Pomyślnie zalogowano!";
//}
//else
//{
//    $response["suc"] = 0;
//    $response["desc"] = "Niepoprawne dane logowania!";
//}

die(json_encode($response,JSON_UNESCAPED_UNICODE));