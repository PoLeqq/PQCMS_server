<?php

header('Content-Type: application/json; charset=utf-8');

require_once(dirname(__DIR__, 3) . "/utils/APIUtils.php");
$response = APIUtils::validatePost($_POST,basename(__FILE__));
if($response["suc"] == 0)
    die(json_encode($response,JSON_UNESCAPED_UNICODE));

$website = APIUtils::getWebsite($_POST);
$users = $website->getUsers();

$responseUsers = [];

if(isset($_POST["admin"]) && $_POST["admin"] === 1)
{
    $admin = null;
    foreach($users as $user)
    {
        if(!isset($user["disabled"]))
        {
            $admin = $user;
            break;
        }
    }
    $responseUsers[] = $admin;
}
else
{
    foreach($users as $user)
    {
        if(isset($_POST["admin"]) && $_POST["admin"] == 0 && !isset($user["disabled"]))
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

//if(isset($_POST["admin"]) && $_POST["admin"] === 0)

echo json_encode(["suc" => 1, "resp" => $responseUsers],JSON_UNESCAPED_UNICODE);