<?php

header('Content-Type: application/json; charset=utf-8');

require_once(dirname(__DIR__, 3) . "/utils/APIUtils.php");
APIUtils::validatePostForAuthKey($_POST);

$safeWebsite = APIUtils::getSafeWebsite($_POST);
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
        if(isset($_POST["admin"]) && $_POST["admin"] === "0" && !isset($user["disabled"]))
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

APIUtils::logAPI($_POST,$response);
echo json_encode($response,JSON_UNESCAPED_UNICODE);