<?php

header('Content-Type: application/json; charset=utf-8');

require_once(dirname(__DIR__, 3) . "/utils/APIUtils.php");
APIUtils::validatePostForAuthKey($_SERVER["REMOTE_ADDR"],basename(__FILE__, '.php'),$_POST);

if(empty($_POST["username"]))
    APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,["suc" => 0, "desc" => "Musisz podać nazwę użytkownika!"]);
$validator = Validator::validateAssoc(["username" => $_POST["username"]],["s(2-30)"]);
if($validator["suc"] === 0)
    APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$validator);

$password = empty($_POST["password"]) ? null : $_POST["password"];
if(!is_null($password))
{
    $validator = Validator::validateAssoc(["password" => $_POST["password"]],["s(5-40)"]);
    if($validator["suc"] === 0)
        APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$validator);
}

$website = APIUtils::getSafeWebsite($_SERVER["REMOTE_ADDR"],$_POST);

$response = $website->resetUserPassword($_POST["username"]);
APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$response);