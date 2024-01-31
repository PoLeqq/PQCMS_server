<?php

header('Content-Type: application/json; charset=utf-8');

require_once(dirname(__DIR__, 2) . "/utils/APIUtils.php");
APIUtils::validatePostForAuthKey($_SERVER["REMOTE_ADDR"],basename(__FILE__, '.php'),$_POST);

if(empty($_POST["auth_key"]))
    APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,["suc" => 1, "resp" => 0, "desc" => "Nie przesłano klucza uwierzytelniającego!"]);

require_once(dirname(__DIR__, 3) . "/objects/Website.inc.php");
$website = APIUtils::getWebsite($_SERVER["REMOTE_ADDR"],$_POST);

$response["resp"] = $website->logoutUser($_POST["client_ip"], $_POST["auth_key"]);
APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$response);