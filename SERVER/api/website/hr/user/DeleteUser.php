<?php

header('Content-Type: application/json; charset=utf-8');

require_once(dirname(__DIR__, 3) . "/utils/APIUtils.php");
APIUtils::validatePostForAuthKey($_POST);

if(empty($_POST["username"]))
    die(json_encode(["suc" => 0, "desc" => "Nie podano pola \"username\"!"],JSON_UNESCAPED_UNICODE));

$website = APIUtils::getSafeWebsite($_POST);

require_once(dirname(__DIR__,4)."/objects/website/WebsiteUser.inc.php");
$websiteUser = WebsiteUser::getWebsiteUserByUsername($_POST["username"],$website->getId());
if(is_null($websiteUser))
    die(json_encode(["suc" => 0, "desc" => "Nie znaleziono użytkownika o podanym loginie!"],JSON_UNESCAPED_UNICODE));
AuthKey::invalidateAuthKeyByUsername($website->getId(),$websiteUser[0],false,true);

$response = $website->deleteUser($_POST["username"]);
APIUtils::logAPI($_POST,$response);
echo json_encode($response,JSON_UNESCAPED_UNICODE);