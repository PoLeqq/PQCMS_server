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
if($response["suc"] == 1 && !empty($_POST["generate_secure_key"]))
{
    require_once(dirname(__DIR__,3)."/objects/Website.inc.php");
    $website = new Website(Website::getWebsiteIDByMatching("domain",$_POST["domain"]));
    $response["secure_key"] = $website->generateSecureKey();
}

die(json_encode($response,JSON_UNESCAPED_UNICODE));