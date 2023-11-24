<?php

session_start();
if(empty($_POST["token"]) || $_POST["token"] != $_SESSION["pqcms-panel-settings-system-token"])
    endScript(false, "Walidacja tokenu nie powiodła się.",false);

if(time() >= $_SESSION["pqcms-panel-settings-system-token-expire"])
    endScript(false,"Token jest przestarzały. Przeładuj stronę!",false);

require_once("updateData.php");
$response = updateSystem($_POST["user"],$_POST["license_key"]);

endScript($response["suc"],$response["desc"],!empty($response["license_key"]));

function endScript(bool $suc, string $desc, bool $warn): void
{
    $_SESSION["pqcms-panel-settings-system-suc"] = $suc;
    $_SESSION["pqcms-panel-settings-system-desc"] = $desc;
    if($warn)
        $_SESSION["pqcms-panel-settings-system-warn"] = "Niepoprawny format kluczu licencyjnego!";
    header("location: ./");
    die($_SESSION["pqcms-panel-settings-system-desc"]." Błędne przekierowanie.");
}