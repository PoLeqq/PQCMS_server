<?php
// todo dodanie sprawdzania z ustawieniem: login_attempts, auth_key_lifespan (ten do dodania)
header('Content-Type: application/json; charset=utf-8');
require_once(dirname(__DIR__,2)."/utils/APIUtils.php");

$response = APIUtils::validatePost($_POST,basename(__FILE__));
if($response["suc"] == 0) die(json_encode($response,JSON_UNESCAPED_UNICODE));

if(empty($_POST["username"]) || empty($_POST["password"]))
    die(json_encode(["suc" => 0, "desc" => "Uzupełnij wszystkie pola!"],JSON_UNESCAPED_UNICODE));

require_once(dirname(__DIR__,3)."/objects/Website.inc.php");
$website = APIUtils::getWebsite($_POST);

$loginUser = $website->loginUser($_SERVER["REMOTE_ADDR"],$_POST["username"], $_POST["password"]);
$response["resp"] = $loginUser["suc"];
$response["desc"] = $loginUser["desc"];
if(isset($loginUser["auth_key"])) $response["auth_key"] = $loginUser["auth_key"];

die(json_encode($response,JSON_UNESCAPED_UNICODE));