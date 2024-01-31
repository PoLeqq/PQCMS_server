<?php

function verifyPosts(): void
{
    header('Content-Type: application/json; charset=utf-8');
    require_once(dirname(__DIR__, 2) . "/utils/APIUtils.php");

    APIUtils::validatePostForAuthKey($_SERVER["REMOTE_ADDR"],basename(__FILE__, '.php'),$_POST);

    if(empty($_POST["perms"]))
        APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,["suc" => 0, "desc" => "Sprawdź poprawność post'ów!"]);

    if(!is_array($_POST["perms"]) || !allArrayValuesAreStrings($_POST["perms"]))
        APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,["suc" => 0, "desc" => "Walidacja danych nie powiodła się! (perms)"]);
}

function allArrayValuesAreStrings($array): bool
{
    return count($array) === count(array_filter($array, 'is_string'));
}