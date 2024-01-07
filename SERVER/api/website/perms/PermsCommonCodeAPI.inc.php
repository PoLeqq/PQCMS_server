<?php

header('Content-Type: application/json; charset=utf-8');
require_once(dirname(__DIR__, 2) . "/utils/APIUtils.php");

APIUtils::validatePostForAuthKey($_POST, basename(__FILE__));

if(empty($_POST["perms"]))
    die(json_encode(["suc" => 0, "desc" => "Sprawdź poprawność post'ów!"], JSON_UNESCAPED_UNICODE));

if(!is_array($_POST["perms"]) || !allArrayValuesAreStrings($_POST["perms"]))
    die(json_encode(["suc" => 0, "desc" => "Walidacja danych nie powiodła się! (perms)"]));

function allArrayValuesAreStrings($array): bool
{
    return count($array) === count(array_filter($array, 'is_string'));
}