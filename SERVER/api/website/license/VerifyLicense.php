<?php

/**
 * UWAGA!!! JEST TO WYJĄTKOWY API-k
 * Nie jest to API-k per user, tylko per serwer
 * Nie pobiera on "client_ip", tylko działa na (przynajmniej powinien XD) IP serwera klienta.
 */

//header("Access-Control-Allow-Origin: https://b.com");
header("Content-Type: application/json; charset=utf-8");

require_once("LicenseChecker.inc.php");

$httpReferer = null;
if(!empty($_SERVER["HTTP_REFERER"])) $httpReferer = $_SERVER["HTTP_REFERER"];

require_once(dirname(__DIR__,2)."/utils/APIUtils.php");
if(empty($_POST["domain"]) || empty($_POST["login"]) || empty($_POST["license_key"]))
{
    $response = ["suc" => 0, "desc" => "Sprawdź poprawność post'ów."];
    APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$response);
}

require_once(dirname(__DIR__,2)."/utils/validators/Validator.inc.php");
$validatorResponse = Validator::validate([$_POST["domain"],$_POST["login"],$_POST["license_key"]],["s","s(2-40)","s(23)"]);
if($validatorResponse["suc"] == 0)
    APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$validatorResponse);

$response = checkLicense($_SERVER["REMOTE_ADDR"], $httpReferer, $_POST["domain"], $_POST["login"], $_POST["license_key"]);
if($response["suc"] === 1)
{
    require_once(dirname(__DIR__,3)."/objects/Website.inc.php");
    require_once(dirname(__DIR__,3)."/objects/website/SecureKey.inc.php");
    $website = new Website($response["id"]);

//    to uniemożliwia zalogowanie się z kilku użytkowników na 1 ip, bo w loginie wywala "błąd API",
//    który nie jest dosłownie błędem (tylko nie ma sesji na kliencie z secure_key w momencie
//    logowania przez incognito bądź inną przeglądarkę)
//    if(SecureKey::hasValidSecureKey($website->getId(),$_SERVER["REMOTE_ADDR"]))
//        $response["secure_key"] = $website->generateSecureKey($_SERVER["REMOTE_ADDR"]);

    $secureKey = SecureKey::getSecureKeyByIP($website->getId(),$_SERVER["REMOTE_ADDR"]);
    if(is_null($secureKey))
        $response["secure_key"] = $website->generateSecureKey($_SERVER["REMOTE_ADDR"]);
    else
        $response["secure_key"] = $secureKey;
}

unset($response["id"]);
APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$response);