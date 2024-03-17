<?php

header('Content-Type: application/json; charset=utf-8');

require_once(dirname(__DIR__, 2) . "/utils/APIUtils.php");
APIUtils::validatePostForAuthKey($_SERVER["REMOTE_ADDR"],basename(__FILE__, '.php'),$_POST);

$type = empty($_POST["type"]) ? null : $_POST["type"];
$skipAmount = empty($_POST["from"]) ? null : $_POST["from"];
$amount = empty($_POST["amount"]) ? null : $_POST["amount"];

$website = APIUtils::getSafeWebsite($_SERVER["REMOTE_ADDR"],$_POST);

$response = $website->getLogs($type,$skipAmount,$amount);

APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$response);