<?php
// todo dodanie sprawdzania z ustawieniem: login_attempts, auth_key_lifespan (ten do dodania)
header('Content-Type: application/json; charset=utf-8');
require_once(dirname(__DIR__,2)."/utils/APIUtils.php");

APIUtils::validatePost($_SERVER["REMOTE_ADDR"],basename(__FILE__, '.php'),$_POST);

if(empty($_POST["username"]) || empty($_POST["password"]))
    APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,["suc" => 0, "desc" => "Uzupełnij wszystkie pola!"]);

require_once(dirname(__DIR__,3)."/objects/Website.inc.php");
$website = APIUtils::getWebsite($_SERVER["REMOTE_ADDR"],$_POST);

$loginUser = $website->loginUser($_POST["client_ip"],$_POST["username"], $_POST["password"]);
unset($loginUser["proper_data"]);

$_POST["username"] = "(hidden)";
$_POST["password"] = "(hidden)";
APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$loginUser);