<?php

header('Content-Type: application/json; charset=utf-8');

require_once(dirname(__DIR__, 2) . "/utils/APIUtils.php");
APIUtils::validatePostForAuthKey($_SERVER["REMOTE_ADDR"],basename(__FILE__, '.php'),$_POST);

if(empty($_POST["username"]))
    APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,["suc" => 0, "desc" => "Nie podano \"username\"!"]);

$nickname = !isset($_POST["nickname"]) ? null : trim($_POST["nickname"]);
if(!is_null($nickname))
{
    $validator = Validator::validateAssoc(["nickname" => $nickname],["s(2-30)"]);
    if($validator["suc"] === 0)
        APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$validator);
}

$emailReset = isset($_POST["email"]) && empty($_POST["email"]);
if(!$emailReset)
{
    $email = !isset($_POST["email"]) ? null : trim($_POST["email"]);
    if(!is_null($email))
    {
        $validator = Validator::validateAssoc(["email" => $email],["s(5-255)"]);
        if($validator["suc"] === 0)
            APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$validator);
    }
}
if($emailReset)
    $email = "";

$website = APIUtils::getSafeWebsite($_SERVER["REMOTE_ADDR"],$_POST);
$authKeyOwner = AuthKey::getAuthKeyOwner($website->getId(),$_POST["auth_key"]);
if($authKeyOwner["suc"] == 0)
    APIUtils::endAPIscript(basename(__FILE__,".php"),$_POST,["suc" => 0, "Nie odnaleziono właściciela klucza \"auth_key\"!"]);

//$response = $website->editSelf($authKeyOwner["resp"], $nickname, $email);

if($authKeyOwner["resp"]["type"] === "a")
{
    $response = ["suc" => 0, "desc" => "Nie można edytować danych administratora!"];
    APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$response);
}

require_once(dirname(__DIR__,3)."/objects/website/WebsiteUser.inc.php");
$user = new WebsiteUser($authKeyOwner["resp"]["id"]);

$response = $website->editUser($user->getUsername(), $nickname, $email, null, null, null);
APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$response);