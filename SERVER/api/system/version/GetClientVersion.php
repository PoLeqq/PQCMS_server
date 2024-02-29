<?php

require_once("Version.inc.php");
Version::newestVesrionApiHandler(true);
//
//header('Content-Type: application/json; charset=utf-8');
//
//require_once(dirname(__DIR__, 2) . "/utils/APIUtils.php");
//APIUtils::validatePostForAuthKey($_SERVER["REMOTE_ADDR"],basename(__FILE__, '.php'),$_POST);
//
//require_once("Version.inc.php");
//$response["version"] = Version::getNewestVersion(true,!empty($_POST["complex"]));
//
//APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$response);