<?php

header('Content-Type: application/json; charset=utf-8');

require_once(dirname(__DIR__, 3) . "/utils/APIUtils.php");
APIUtils::validatePostForAuthKey($_SERVER["REMOTE_ADDR"],basename(__FILE__, '.php'),$_POST);

if(empty($_POST["name"]))
    APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,["suc" => 0, "desc" => "Nie podano pola \"name\"!"]);

$website = APIUtils::getSafeWebsite($_SERVER["REMOTE_ADDR"],$_POST);

$response = $website->deleteRank($_POST["name"]);
APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$response);