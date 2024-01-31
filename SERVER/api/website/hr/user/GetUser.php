<?php

header('Content-Type: application/json; charset=utf-8');

require_once(dirname(__DIR__, 3) . "/utils/APIUtils.php");
APIUtils::validatePostForAuthKey($_SERVER["REMOTE_ADDR"],basename(__FILE__, '.php'),$_POST);

if(!empty($_POST["admin"]))
{
    $validator = Validator::validate([$_POST["admin"]],["s(0-1)"]);
    if($validator["suc"] == 0)
        APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$validator);
}
if(!empty($_POST["username"]))
{
    $validator = Validator::validate([$_POST["username"]],["s(5-30)"]);
    if($validator["suc"] == 0)
        APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$validator);
}
if(!empty($_POST["nickname"]))
{
    $validator = Validator::validate([$_POST["nickname"]],["s(2-30)"]);
    if($validator["suc"] == 0)
        APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$validator);
}
if(!empty($_POST["disabled"]))
{
    $validator = Validator::validate([$_POST["disabled"]],["s(0-1)"]);
    if($validator["suc"] == 0)
        APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$validator);
}

$safeWebsite = APIUtils::getSafeWebsite($_SERVER["REMOTE_ADDR"],$_POST);
$users = $safeWebsite->getUsers();

$responseUsers = [];
if(isset($_POST["admin"]) && $_POST["admin"] === "1")
{
    foreach($users as $user)
    {
        if(!isset($user["disabled"]))
        {
            $responseUsers[] = $user;
            break;
        }
    }
}
else
{
    foreach($users as $user)
    {
        if((isset($_POST["admin"]) && $_POST["admin"] === "0") && !isset($user["disabled"]))
            continue;
        if(isset($_POST["username"]) && $_POST["username"] !== $user["username"])
            continue;
        if(isset($_POST["nickname"]) && $_POST["nickname"] !== $user["nickname"])
            continue;
        if(isset($_POST["disabled"]) && $_POST["disabled"] !== $user["disabled"])
            continue;
        $responseUsers[] = $user;
    }
}

$response = ["suc" => 1, "resp" => $responseUsers];

APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$response);