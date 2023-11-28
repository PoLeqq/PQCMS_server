<?php

session_start();
if(empty($_SESSION["pqcms-panel-auth_key"]))
    die(json_encode(["suc" => 0, "desc" => "Najpierw się zaloguj!"]));

require_once(dirname(__DIR__,2)."/Communicator.inc.php");

$resp = Communicator::communicate(CommunicateURL::IS_VALID_AUTH_KEY,["auth_key" => $_SESSION["pqcms-panel-auth_key"]]);
if($resp["resp"]) die(json_encode(["suc" => true, "desc" => "Sesja aktywna."]));
else die(json_encode(["suc" => false, "desc" => "Sesja wygasła."]));