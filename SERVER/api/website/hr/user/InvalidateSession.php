<?php

header('Content-Type: application/json; charset=utf-8');

require_once(dirname(__DIR__, 3) . "/utils/APIUtils.php");
APIUtils::validatePostForAuthKey($_POST);

if(empty($_POST["username"]))
    die(json_encode(["suc" => 0, "desc" => "Nie podano nazwy użytkownika!"],JSON_UNESCAPED_UNICODE));

$safeWebsite = APIUtils::getSafeWebsite($_POST);

$resp = $safeWebsite->invalidateSession($_POST["username"]);
echo json_encode($resp,JSON_UNESCAPED_UNICODE);