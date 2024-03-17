<?php

header('Content-Type: application/json; charset=utf-8');

require_once(dirname(__DIR__, 3) . "/utils/APIUtils.php");
APIUtils::validatePostForAuthKey($_SERVER["REMOTE_ADDR"],basename(__FILE__, '.php'),$_POST);
//
//if(empty($_POST["name"]) || empty($_POST["display_name"]) || !isset($_POST["priority"]))
//    die(json_encode(["suc" => 0, "desc" => "Uzupełnij wszystkie pola!"],JSON_UNESCAPED_UNICODE));

$name = !isset($_POST["name"]) ? null : $_POST["name"];
if(!is_null($name))
{
    $validator = Validator::validateAssoc(["name" => $name],["s(2-30)"]);
    if($validator["suc"] === 0)
        die(json_encode($validator,JSON_UNESCAPED_UNICODE));
}
$displayName = !isset($_POST["display_name"]) ? null : $_POST["display_name"];
if(!is_null($displayName))
{
    $validator = Validator::validateAssoc(["display_name" => $displayName],["s(2-30)"]);
    if($validator["suc"] === 0)
        die(json_encode($validator,JSON_UNESCAPED_UNICODE));
}
//$parentId = !isset($_POST["parent_id"]) ? null : $_POST["parent_id"];
//if(!is_null($parentId))
//{
//    $validator = Validator::validateAssoc(["parent_id" => $parentId],["i"]);
//    if($validator["suc"] === 0)
//        die(json_encode($validator,JSON_UNESCAPED_UNICODE));
//}
$priority = !isset($_POST["priority"]) ? null : $_POST["priority"];
if(!is_null($priority))
{
    $validator = Validator::validateAssoc(["priority" => $priority],["i(0-65535)"]);
    if($validator["suc"] === 0)
        APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$validator);
}

if(isset($_POST["perms"]))
{
    if($_POST["perms"] === "")
        $_POST["perms"] = [];
    if(!is_array($_POST["perms"]))
        APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,["suc" => 0, "desc" => "Podane permisje nie są poprawne!"]);

    require_once(dirname(__DIR__,4)."/objects/website/WebsitePermissions.php");
    $parsedPerms = WebsitePermissions::parsePostPermsArray($_POST["perms"]);
    if(is_null($parsedPerms))
        APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,["suc" => 0, "desc" => "Podane permisje nie są poprawne!"]);
}
else
    $parsedPerms = null;

$safeWebsite = APIUtils::getSafeWebsite($_SERVER["REMOTE_ADDR"],$_POST);
$parentId = (empty($_POST["parent_id"])) ? null : $_POST["parent_id"];

$response = $safeWebsite->editRank($name, $displayName, $priority, $parentId, $parsedPerms);

APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$response);