<?php

// NAPISZE TUTAJ, BO JUŻ RAZ DOSTAŁEM MINDFUCKA...
// zwraca wszystko true/false:
// valid - czy poprawne (inny klucz, niż obecny || (outdated || validated) == 1)
// outdated - czy przestarzały
// invalidated - czy admin (klient/pqcms) zamknął sesję

header('Content-Type: application/json; charset=utf-8');
require_once(dirname(__DIR__, 2) . "/utils/APIUtils.php");

APIUtils::validatePost($_POST);

if(empty($_POST["auth_key"]))
    die(json_encode(["suc" => 0, "desc" => "Uzupełnij wszystkie pola!"], JSON_UNESCAPED_UNICODE));

$validator = Validator::validateAssoc(["auth_key" => $_POST["auth_key"]],["s(128)"]);
if($validator["suc"] === 0)
    die(json_encode($validator, JSON_UNESCAPED_UNICODE));

require_once(dirname(__DIR__, 3) . "/objects/website/AuthKey.inc.php");
$website = APIUtils::getWebsite($_POST);

$isValidAuthKeyForIP = AuthKey::isValidAuthKeyForIp($website->getId(),$_SERVER["REMOTE_ADDR"],$_POST["auth_key"]);
//if(isset($isValidAuthKeyForIP["not_secure"]) && $isValidAuthKeyForIP["not_secure"] === 1)
$response["resp"] = $isValidAuthKeyForIP;
die(json_encode($response, JSON_UNESCAPED_UNICODE));