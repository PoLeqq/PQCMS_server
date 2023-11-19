<?php

header("Content-Type: application/json; charset=utf-8");

session_start();
if(!isset($_SESSION["pqcms-server-token-addwebsite"]) || !isset($_SESSION["pqcms-server-token-addwebsite-expire"]))
    die("Wrong token. Reload the form.");
if(time() >= $_SESSION["pqcms-server-token-addwebsite-expire"])
    die("Token has expired. Reload the form.");
if($_SESSION["pqcms-server-token-addwebsite"] != $_POST["token"])
    die("Incorrect token.");

require_once(dirname(__DIR__, 2) . "/objects/Website.inc.php");
if(Website::getWebsiteIDByMatching("domain",$_POST["domain"]) != null)
    die("Website identified by domain \"{$_POST["domain"]}\" actually exists in database!");
if(Website::getWebsiteIDByMatching("login",$_POST["login"]) != null)
    die("Website identified by login \"{$_POST["login"]}\" actually exists in database!");

require_once(dirname(__DIR__, 2) . "/objects/website/LicenseKey.inc.php");
if(isset($_POST["perm_license"])) $expDate = null;
else if(isset($_POST["expiry_date"])) $expDate = $_POST["expiry_date"];
else die("License needs to be permanent or expire.");

Website::addWebsite($_POST["domain"],$_POST["login"],LicenseKey::getRandomLicenseKey(),$expDate,$_POST["blocked"]);
echo "Added to the database!\n";
var_dump($_POST);