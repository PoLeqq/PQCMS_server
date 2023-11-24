<?php

session_start();
if(empty($_POST["token"]) || $_POST["token"] != $_SESSION["pqcms-panel-settings-database-token"])
    endScript(false, "Walidacja tokenu nie powiodła się.",null);

if(time() >= $_SESSION["pqcms-panel-settings-database-token-expire"])
    endScript(false,"Token jest przestarzały. Przeładuj stronę!",null);

require_once("updateData.php");
$response = updateDatabase($_POST["host"],$_POST["user"],$_POST["password"]);

session_start();
endScript($response["suc"],$response["desc"],$response["conn_err"]);

function endScript(bool $suc, string $desc, ?string $warn): void
{
    $_SESSION["pqcms-panel-settings-database-suc"] = $suc;
    $_SESSION["pqcms-panel-settings-database-desc"] = $desc;
    if(!is_null($warn) && $warn != "")
        $_SESSION["pqcms-panel-settings-database-warn"] = $warn;
    header("location: ./");
    die($_SESSION["pqcms-panel-settings-database-desc"]." Błędne przekierowanie.");
}