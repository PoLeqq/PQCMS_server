<?php

header('Content-Type: application/json; charset=utf-8');
require_once(dirname(__DIR__, 2) . "/utils/APIUtils.php");

$response = APIUtils::validatePost($_POST, basename(__FILE__));
if($response["suc"] == 0) die(json_encode($response, JSON_UNESCAPED_UNICODE));

if(empty($_POST["auth_key"]))
    die(json_encode(["suc" => 0, "desc" => "Nie przesłano klucza uwierzytelniającego!", "post" => $_POST], JSON_UNESCAPED_UNICODE));

require_once(dirname(__DIR__, 3) . "/objects/Website.inc.php");
$website = APIUtils::getWebsite($_POST);

$response["resp"] = $website->logoutUser($_SERVER["REMOTE_ADDR"], $_POST["auth_key"]);
die(json_encode($response, JSON_UNESCAPED_UNICODE));