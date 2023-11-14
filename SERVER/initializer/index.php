<?php
header("content-type: application/json");

session_start();
unset($_SESSION["initializer-error"]);
unset($_SESSION["initializer-success"]);

if(!isset($_POST["electrocms-domain"]) || !isset($_POST["electrocms-username"]) || !isset($_POST["electrocms-license-key"]))
{
    header("location: error/");
    die("Nieprawidłowe przekierowanie.");
}

function error($errorDescription): void
{
    $_SESSION["initializer-error"] = $errorDescription;
    header("location: error/");
    die("Nieprawidłowe przekierowanie. ".$errorDescription);
}

if(empty($_POST["electrocms-domain"]) || empty($_POST["electrocms-username"]) || empty($_POST["electrocms-license-key"]))
    error("Uzupełnij wszystkie pola!");

if(strlen($_POST["electrocms-username"]) < 5 || strlen($_POST["electrocms-username"]) > 30)
    error("Nazwa użytkownika musi mieć od 5 do 30 znaków!");

if(!preg_match('/^[A-Z0-9]{5}-[A-Z0-9]{5}-[A-Z0-9]{5}-[A-Z0-9]{5}$/', $_POST["electrocms-license-key"]))
    error("Klucz licencyjny podany w nieprawidłowym formacie!");

require_once(dirname(__DIR__) . "/api/website/license/LicenseChecker.inc.php");
$response = checkLicense($_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"],$_POST["electrocms-domain"],$_POST["electrocms-username"],$_POST["electrocms-license-key"]);
if(array_keys($response)[0] == "suc")
{
    $_SESSION["electrocms-client-domain"] = $_POST["electrocms-domain"];
    $_SESSION["electrocms-client-username"] = $_POST["electrocms-username"];
    $_SESSION["electrocms-client-license-key"] = $_POST["electrocms-license-key"];
    $_SESSION["electrocms-client-logged"] = true;

    require_once(dirname(__DIR__) . "/objects/Website.inc.php");
    $_SESSION["electrocms-client-website-id"] = Website::getWebsiteIDByMatching("domain",$_POST["electrocms-domain"]);

    $_SESSION["initializer-success"] = "Pomyślnie zalogowano! Za chwilę nastąpi przekierowanie...";
    header("location: success/");
    die("Niepoprawne przekierowanie. Pomyślnie zalogowano!");
}
else
{
    $sessionError = $response["err"];
    if(array_key_exists("tries_left",$response)) $sessionError .= " Pozostało prób: ".$response["tries_left"];
//        $_SESSION["initializer-error"] = "Podano błędne dane/licencja wygasła! Pozostało Ci ".$response["tries_left"]." prób.";
    $_SESSION["initializer-error"] = $sessionError;
    header("location: error/");
    die("Niepoprawne przekierowanie. ".$_SESSION["initializer-error"]);
}