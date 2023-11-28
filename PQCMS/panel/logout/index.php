<?php

session_start();
if(empty($_SESSION["pqcms-panel-username"]) || empty($_SESSION["pqcms-panel-auth_key"]))
{
    header("location: ../");
    die("Najpierw musisz się zalogować! Błędne przekierowanie.");
}

require_once(dirname(__DIR__,2)."/Communicator.inc.php");
$logoutResponse = Communicator::communicate(CommunicateURL::LOGOUT_USER,["auth_key" => $_SESSION["pqcms-panel-auth_key"]]);

foreach(array_keys($_SESSION) as $sessionKey)
    if(str_starts_with($sessionKey,"pqcms-"))
        unset($_SESSION[$sessionKey]);

if($logoutResponse["resp"])
{
    $_SESSION["pqcms-logged_out"] = ["suc" => 1, "desc" => "Pomyślnie wylogowano."];
    header("location: result.php");
    die($_SESSION["pqcms-logged_out"]["desc"]." Błędne przekierowanie.");
}

$_SESSION["pqcms-logged_out"] = ["suc" => 0, "desc" => "Niepoprawny klucz uwierzytelniający. Twoja sesja wygasła."];
header("location: result.php");
echo $_SESSION["pqcms-logged_out"]["desc"]." Błędne przekierowanie.";