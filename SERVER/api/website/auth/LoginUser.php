<?php
// todo dodanie sprawdzania z ustawieniem: login_attempts, auth_key_lifespan (ten do dodania)
header('Content-Type: application/json; charset=utf-8');
require_once(dirname(__DIR__,2)."/utils/APIUtils.php");

APIUtils::validatePost($_POST);

if(empty($_POST["username"]) || empty($_POST["password"]))
    die(json_encode(["suc" => 0, "desc" => "Uzupełnij wszystkie pola!"],JSON_UNESCAPED_UNICODE));

require_once(dirname(__DIR__,3)."/objects/Website.inc.php");
$website = APIUtils::getWebsite($_POST);

$loginUser = $website->loginUser($_POST["client_ip"],$_POST["username"], $_POST["password"]);
unset($loginUser["proper_data"]);

APIUtils::logAPI($_POST,$loginUser);
die(json_encode($loginUser,JSON_UNESCAPED_UNICODE));