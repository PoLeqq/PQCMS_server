<?php

// NAPISZE TUTAJ, BO JUŻ RAZ DOSTAŁEM MINDFUCKA...
// zwraca wszystko true/false:
// valid - czy poprawne (inny klucz, niż obecny || (outdated || validated) == 1)
// outdated - czy przestarzały
// invalidated - czy admin (klient/pqcms) zamknął sesję

header('Content-Type: application/json; charset=utf-8');
require_once(dirname(__DIR__, 2) . "/utils/APIUtils.php");

$response = APIUtils::validatePost($_POST, basename(__FILE__));
if($response["suc"] == 0) die(json_encode($response, JSON_UNESCAPED_UNICODE));

if(empty($_POST["auth_key"]))
    die(json_encode(["suc" => 0, "desc" => "Uzupełnij wszystkie pola!"], JSON_UNESCAPED_UNICODE));

require_once(dirname(__DIR__, 3) . "/objects/website/AuthKey.inc.php");
$website = APIUtils::getWebsite($_POST);

$response["resp"] = AuthKey::isValidAuthKeyByIp($website->getId(),$_SERVER["REMOTE_ADDR"],$_POST["auth_key"]);
die(json_encode($response, JSON_UNESCAPED_UNICODE));