<?php
//header("Access-Control-Allow-Origin: https://b.com");
header("Content-Type: application/json; charset=utf-8");

require_once("LicenseChecker.inc.php");
//require_once(dirname(__DIR__,3)."/panel/login/login.php");

$httpReferer = null;
if(isset($_SERVER["HTTP_REFERER"])) $httpReferer = $_SERVER["HTTP_REFERER"];

if(empty($_POST["domain"]) || empty($_POST["login"]) || empty($_POST["license_key"]))
{
    echo json_encode(["err" => "Got wrong data. Check your posts."]);
    die();
}

$result = checkLicense($_SERVER["REMOTE_ADDR"], $httpReferer, $_POST["domain"], $_POST["login"], $_POST["license_key"]);
if(array_keys($result)[0] == "suc" && isset($_POST["generate_secure_key"]) && $_POST["generate_secure_key"])
{
    require_once(dirname(__DIR__,3)."/objects/website/SecureKey.inc.php");
    require_once(dirname(__DIR__,3)."/objects/Website.inc.php");
    $result["secure_key"] = SecureKey::generateSecureKey(Website::getWebsiteIDByMatching("domain",$_POST["domain"]));
}

echo json_encode($result);

//    if(array_keys([0] == "suc") die(json_encode(["suc" => "License valid!"]));
//    else die(json_encode(["err" => "License invalid!","triesLeft" => ));

