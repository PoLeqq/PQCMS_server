<?php

header("Content-Type: application/json; charset=utf-8");

session_start();
if(empty($_SESSION["electrocms-admin-logged"])) die("You must be logged in.");
if(empty($_POST["server"]) || empty($_POST["domain"]) || empty($_POST["login"]) || empty($_POST["license_key"])) die("Got wrong config. Check your posts.");

if(!isset($_SESSION["token-server-checklicense"]) || !isset($_SESSION["token-server-checklicense-expire"])) die("Wrong token. Reload the form.");
if(time() >= $_SESSION["token-server-checklicense-expire"]) die("Token has expired. Reload the form.");
if($_SESSION["token-server-checklicense"] != $_POST["token"]) die("Incorrect token.");

require_once(dirname(__DIR__, 2) . "/api/website/license/LicenseChecker.inc.php");
die(json_encode(checkLicense($_POST["server"],$_SERVER["HTTP_REFERER"],$_POST["domain"],$_POST["login"],$_POST["license_key"])));