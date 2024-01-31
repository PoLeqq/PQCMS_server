<?php

header('Content-Type: application/json; charset=utf-8');

require_once(dirname(__DIR__, 3) . "/utils/APIUtils.php");
APIUtils::validatePostForAuthKey($_SERVER["REMOTE_ADDR"],basename(__FILE__, '.php'),$_POST);

if(empty($_POST["username"]) || empty($_POST["nickname"]) || !isset($_POST["email"]) || empty($_POST["password"]) || !isset($_POST["disabled"]))
    APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,["suc" => 0, "desc" => "Uzupełnij wszystkie pola!"]);

if(empty($_POST["perms"]))
    $_POST["perms"] = [];
if(!is_array($_POST["perms"]))
    APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,["suc" => 0, "desc" => "Podane permisje nie są poprawne!"]);

require_once(dirname(__DIR__,4)."/objects/website/WebsitePermissions.php");
$parsedPerms = WebsitePermissions::parsePostPermsArray($_POST["perms"]);
if(is_null($parsedPerms))
    APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,["suc" => 0, "desc" => "Podane permisje nie są poprawne!"]);

$website = APIUtils::getSafeWebsite($_SERVER["REMOTE_ADDR"],$_POST);
$response = $website->addUser(trim($_POST["username"]), trim($_POST["nickname"]), trim($_POST["email"]), trim($_POST["password"]), $parsedPerms, trim($_POST["disabled"]));

APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$response);