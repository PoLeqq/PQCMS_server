<?php
//header("Access-Control-Allow-Origin: https://b.com");
header("Content-Type: application/json; charset=utf-8");

require_once("LicenseChecker.inc.php");

$httpReferer = null;
if(!empty($_SERVER["HTTP_REFERER"])) $httpReferer = $_SERVER["HTTP_REFERER"];

if(empty($_POST["domain"]) || empty($_POST["login"]) || empty($_POST["license_key"]))
    die(json_encode(["suc" => 0, "desc" => "Sprawdź poprawność post'ów."],JSON_UNESCAPED_UNICODE));

require_once(dirname(__DIR__,2)."/utils/validators/Validator.inc.php");
$validatorResponse = Validator::validate([$_POST["domain"],$_POST["login"],$_POST["license_key"]],["s","s","s(23)"]);
if($validatorResponse["suc"] == 0)
    die(json_encode($validatorResponse));

$response = checkLicense($_SERVER["REMOTE_ADDR"], $httpReferer, $_POST["domain"], $_POST["login"], $_POST["license_key"]);
if($response["suc"] === 1)
{
    require_once(dirname(__DIR__,3)."/objects/Website.inc.php");
    require_once(dirname(__DIR__,3)."/objects/website/SecureKey.inc.php");
    $website = new Website(Website::getWebsiteIDByMatching("domain",$_POST["domain"]));

//    to uniemożliwia zalogowanie się z kilku użytkowników na 1 ip, bo w loginie wywala "błąd API",
//    który nie jest dosłownie błędem (tylko nie ma sesji na kliencie z secure_key w momencie
//    logowania przez incognito bądź inną przeglądarkę)
//    if(!SecureKey::hasValidSecureKey($website->getId(),$_SERVER["REMOTE_ADDR"]))
        $response["secure_key"] = $website->generateSecureKey($_SERVER["REMOTE_ADDR"]);
}

die(json_encode($response,JSON_UNESCAPED_UNICODE));