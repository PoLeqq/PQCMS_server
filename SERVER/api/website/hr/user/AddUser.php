<?php

header('Content-Type: application/json; charset=utf-8');

require_once(dirname(__DIR__, 3) . "/utils/APIUtils.php");
$response = APIUtils::validatePost($_POST,basename(__FILE__));
if($response["suc"] == 0)
    die(json_encode($response,JSON_UNESCAPED_UNICODE));

if(empty($_POST["username"]) || empty($_POST["nickname"]) || empty($_POST["password"]) || !isset($_POST["disabled"]))
    die(json_encode(["suc" => 1, "resp" => 0, "desc" => "Uzupełnij wszystkie pola!"],JSON_UNESCAPED_UNICODE));

$website = APIUtils::getWebsite($_POST);
// TODO sprawdzenie maxa użytkowników przypisane do strony
$errno = $website->addUser($_POST["username"], $_POST["nickname"], $_POST["password"], [], $_POST["disabled"]);
echo json_encode(["suc" => 1, "resp" => $errno == 0, "desc" => $errno],JSON_UNESCAPED_UNICODE);