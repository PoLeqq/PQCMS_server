<?php
// todo cały apik do zrobienia
header('Content-Type: application/json; charset=utf-8');

require_once(dirname(__DIR__, 3) . "/utils/APIUtils.php");
APIUtils::validatePostForAuthKey($_POST,basename(__FILE__));

if(empty($_POST["name"]) || !isset($_POST["perms"]) || empty($_POST["priority"]))
    die(json_encode(["suc" => 0, "desc" => "Uzupełnij wszystkie pola!"],JSON_UNESCAPED_UNICODE));

if($_POST["priority"] > 65535)
    die(json_encode(["suc" => 0, "desc" => "Priorytet rangi nie może być większy niż 65535!"],JSON_UNESCAPED_UNICODE));

if(!is_array($_POST["perms"]))
    die(json_encode(["suc" => 0, "desc" => "Niepoprawny format permisji!"],JSON_UNESCAPED_UNICODE));

$website = APIUtils::getWebsite($_POST);
// TODO sprawdzenie maxa użytkowników przypisane do strony
$errno = $website->addUser($_POST["username"], $_POST["nickname"], $_POST["password"], [], $_POST["disabled"]);
echo json_encode(["suc" => 1, "resp" => $errno == 0, "desc" => $errno],JSON_UNESCAPED_UNICODE);