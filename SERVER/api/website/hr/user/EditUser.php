<?php

header('Content-Type: application/json; charset=utf-8');

require_once(dirname(__DIR__, 3) . "/utils/APIUtils.php");
APIUtils::validatePostForAuthKey($_SERVER["REMOTE_ADDR"],basename(__FILE__, '.php'),$_POST);

if(empty($_POST["username"]))
    APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,["suc" => 0, "desc" => "Nie podano \"username\"!"]);

$username = trim($_POST["username"]);

$validator = Validator::validateAssoc(["username" => $username],["s(5-30)"]);
if($validator["suc"] === 0)
    APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$validator);

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

$password = !isset($_POST["password"]) ? null : $_POST["password"];
if(!is_null($password))
{
    $validator = Validator::validateAssoc(["password" => $password],["s(5-40)"]);
    if($validator["suc"] === 0)
        APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$validator);
}

$disabled = !isset($_POST["disabled"]) ? null : $_POST["disabled"];
if(!is_null($disabled))
{
    $validator = Validator::validateAssoc(["disabled" => $disabled],["s(0-1)"]);
    if($validator["suc"] === 0)
        APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$validator);
}

if(isset($_POST["perms"]))
{
    if($_POST["perms"] === "")
        $_POST["perms"] = [];
    if(!is_array($_POST["perms"]))
        APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,["suc" => 0, "desc" => "Podane uprawnienia nie są poprawne!"]);

    require_once(dirname(__DIR__,4)."/objects/website/WebsitePermissions.php");
    $parsedPerms = WebsitePermissions::parsePostPermsArray($_POST["perms"]);
    if(is_null($parsedPerms))
        APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,["suc" => 0, "desc" => "Podane uprawnienia nie są poprawne!"]);
}
else
    $parsedPerms = null;

$website = APIUtils::getSafeWebsite($_SERVER["REMOTE_ADDR"],$_POST);
$response = $website->editUser($username, $nickname, $email, $password, $parsedPerms, $disabled);
APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$response);