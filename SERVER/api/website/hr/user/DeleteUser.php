<?php

header('Content-Type: application/json; charset=utf-8');

require_once(dirname(__DIR__, 3) . "/utils/APIUtils.php");
APIUtils::validatePostForAuthKey($_POST);

if(empty($_POST["username"]))
    die(json_encode(["suc" => 0, "desc" => "Nie podano pola \"username\"!"],JSON_UNESCAPED_UNICODE));

$website = APIUtils::getSafeWebsite($_POST);
echo json_encode($website->deleteUser($_POST["username"]),JSON_UNESCAPED_UNICODE);