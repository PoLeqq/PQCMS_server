<?php
require_once("Version.inc.php");

if(isset($_POST["complex"]) && $_POST["complex"]) $complex = true;
else $complex = false;
die(Version::getVersion("server",$complex));