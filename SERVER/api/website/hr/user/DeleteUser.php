<?php

header('Content-Type: application/json; charset=utf-8');

require_once(dirname(__DIR__, 3) . "/utils/APIUtils.php");
APIUtils::validatePostForAuthKey($_SERVER["REMOTE_ADDR"],basename(__FILE__, '.php'),$_POST);

if(empty($_POST["username"]))
    APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,["suc" => 0, "desc" => "Nie podano pola \"username\"!"]);

$website = APIUtils::getSafeWebsite($_SERVER["REMOTE_ADDR"],$_POST);

require_once(dirname(__DIR__,4)."/objects/website/WebsiteUser.inc.php");
$websiteUser = WebsiteUser::getWebsiteUserByUsername($_POST["username"],$website->getId());
if(is_null($websiteUser))
    APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,["suc" => 0, "desc" => "Nie znaleziono użytkownika o podanym loginie!"]);
AuthKey::invalidateAuthKeyByUsername($website->getId(),$websiteUser[0],false,true);

$response = $website->deleteUser($_POST["username"]);
APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$response);