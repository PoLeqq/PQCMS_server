<?php

header('Content-Type: application/json; charset=utf-8');
require_once(dirname(__DIR__, 3) . "/utils/APIUtils.php");

APIUtils::validatePost($_SERVER["REMOTE_ADDR"],basename(__FILE__, '.php'),$_POST);

$website = APIUtils::getWebsite($_SERVER["REMOTE_ADDR"],$_POST);
$response = ["suc" => 1, "resp" => $website->getAdminId() != null];

APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$response);