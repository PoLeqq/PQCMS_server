<?php

    header("Content-Type: application/json; charset=utf-8");

    if(empty($_POST["login"]) || empty($_POST["license_key"])) die(json_encode(["err" => "Got wrong config. Check your posts."]));

    require_once("LicenseChecker.inc.php");
    if(isBanned($_SERVER["REMOTE_ADDR"])) die(json_encode(["err" => "This IP is banned!"]));

    $result = checkLicense($_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"],$_POST["domain"],$_POST["login"],$_POST["license_key"]);
    if(array_keys($result)[0] == "err") $result[] = ["tries_left" => getTries($_SERVER["REMOTE_ADDR"])];
    die($result);
//    if(array_keys([0] == "suc") die(json_encode(["suc" => "License valid!"]));
//    else die(json_encode(["err" => "License invalid!","triesLeft" => ));