<?php

header('Content-Type: application/json; charset=utf-8');

require_once(dirname(__DIR__, 2) . "/utils/APIUtils.php");
APIUtils::validatePostForAuthKey($_SERVER["REMOTE_ADDR"],basename(__FILE__, '.php'),$_POST);
if(empty($_POST["version"]) || !is_array($_POST["version"]))
    APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,["suc" => 0, "desc" => "Nie podano wersji!"]);

$version = $_POST["version"];
if(!is_array($version))
    APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,[
        "suc" => 0,
        "desc" => "Pole \"version\" musi być tablicą!"
    ]);

$versionTypes = empty($_POST["types"]) ? null : $_POST["types"];
if(!is_null($versionTypes) && !is_array($versionTypes))
    APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,[
        "suc" => 0,
        "desc" => "Pole \"types\" musi być tablicą!"
    ]);

require_once("Version.inc.php");
$response = [
    "suc" => 1,
    "version" => Version::getNewerVersion(true,$version,$versionTypes)
];

APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$response);