<?php

header('Content-Type: application/json; charset=utf-8');

require_once(dirname(__DIR__, 3) . "/utils/APIUtils.php");
APIUtils::validatePostForAuthKey($_POST);

if(empty($_POST["username"]) || empty($_POST["nickname"]) || !isset($_POST["email"]) || empty($_POST["password"]) || !isset($_POST["disabled"]))
    die(json_encode(["suc" => 0, "desc" => "Uzupełnij wszystkie pola!"],JSON_UNESCAPED_UNICODE));

if(empty($_POST["perms"]))
    $_POST["perms"] = [];
if(!is_array($_POST["perms"]))
    die(json_encode(["suc" => 0, "desc" => "Podane permisje nie są poprawne!"],JSON_UNESCAPED_UNICODE));

require_once(dirname(__DIR__,4)."/objects/website/WebsitePermissions.php");
$parsedPerms = WebsitePermissions::parsePostPermsArray($_POST["perms"]);
if(is_null($parsedPerms))
    die(json_encode(["suc" => 0, "desc" => "Podane permisje nie są poprawne!"]));

$website = APIUtils::getSafeWebsite($_POST);
$response = $website->addUser(trim($_POST["username"]), trim($_POST["nickname"]), trim($_POST["email"]), trim($_POST["password"]), $parsedPerms, trim($_POST["disabled"]));

APIUtils::logAPI($_POST,$response);
echo json_encode($response,JSON_UNESCAPED_UNICODE);