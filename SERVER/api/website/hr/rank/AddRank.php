<?php

header('Content-Type: application/json; charset=utf-8');

require_once(dirname(__DIR__, 3) . "/utils/APIUtils.php");
APIUtils::validatePostForAuthKey($_SERVER["REMOTE_ADDR"],basename(__FILE__, '.php'),$_POST);

if(empty($_POST["name"]) || empty($_POST["display_name"]) || !isset($_POST["priority"]))
    APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,["suc" => 0, "desc" => "Uzupełnij wszystkie pola!"]);

$apiFields = ["priority"];
$validatorResponse = Validator::validate([$_POST["priority"]],["i(0-65535)"]);
if($validatorResponse["suc"] == 0)
    APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,["suc" => 0, "desc" => "Walidacja nie powiodła się dla pola \"{$apiFields[$validatorResponse["element_index"]]}\""]);
//    die(json_encode(["suc" => 0, "desc" => "Walidacja nie powiodła się dla pola \"{$apiFields[$validatorResponse["element_index"]]}\"", "dev_msg" => $validatorResponse["desc"]],JSON_UNESCAPED_UNICODE));
//if($_POST["priority"] > 65535)
//    die(json_encode(["suc" => 0, "desc" => "Priorytet rangi nie może być większy niż 65535!"],JSON_UNESCAPED_UNICODE));

if(empty($_POST["perms"]))
    $_POST["perms"] = [];
if(!is_array($_POST["perms"]))
    APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,["suc" => 0, "desc" => "Podane permisje nie są poprawne!"]);

require_once(dirname(__DIR__,4)."/objects/website/WebsitePermissions.php");
$parsedPerms = WebsitePermissions::parsePostPermsArray($_POST["perms"]);
if(is_null($parsedPerms))
    APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,["suc" => 0, "desc" => "Podane permisje nie są poprawne!"]);

$safeWebsite = APIUtils::getSafeWebsite($_SERVER["REMOTE_ADDR"],$_POST);
$website = $safeWebsite->getWebsite();
foreach($_POST["perms"] as $perm)
    if(!$website->hasPermission($_POST["client_ip"],$_POST["auth_key"],$perm))
        unset($_POST["perms"][$perm]);

$parentId = (empty($_POST["parent_id"])) ? null : $_POST["parent_id"];

$response = $safeWebsite->addRank($_POST["name"], $_POST["display_name"], $_POST["perms"], $_POST["priority"], $parentId);
APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$response);