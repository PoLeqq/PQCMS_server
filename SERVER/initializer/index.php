<?php
header("content-type: application/json");

session_start();
unset($_SESSION["pqcms-initializer-error"]);
unset($_SESSION["pqcms-initializer-success"]);

if(!isset($_POST["pqcms-domain"]) || !isset($_POST["pqcms-username"]) || !isset($_POST["pqcms-license-key"]))
    error("Przesłano niepoprawne wartości. Skontaktuj się z administratorem PQCMS!");

if(empty($_POST["pqcms-domain"]) || empty($_POST["pqcms-username"]) || empty($_POST["pqcms-license-key"]))
    error("Uzupełnij wszystkie pola!");

if(strlen($_POST["pqcms-username"]) < 5 || strlen($_POST["pqcms-username"]) > 30)
    error("Nazwa użytkownika musi mieć od 5 do 30 znaków!");

if(!preg_match('/^[A-Z0-9]{5}-[A-Z0-9]{5}-[A-Z0-9]{5}-[A-Z0-9]{5}$/', $_POST["pqcms-license-key"]))
    error("Klucz licencyjny podany w nieprawidłowym formacie!");

require_once(dirname(__DIR__) . "/api/website/license/LicenseChecker.inc.php");
$response = checkLicense($_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"],$_POST["pqcms-domain"],$_POST["pqcms-username"],$_POST["pqcms-license-key"]);
if($response["suc"] == 1)
{
    $_SESSION["pqcms-client-domain"] = $_POST["pqcms-domain"];
    $_SESSION["pqcms-client-username"] = $_POST["pqcms-username"];
    $_SESSION["pqcms-client-license-key"] = $_POST["pqcms-license-key"];
    $_SESSION["pqcms-client-logged"] = true;

    require_once(dirname(__DIR__) . "/objects/Website.inc.php");
    $_SESSION["pqcms-client-website-id"] = Website::getWebsiteIDByMatching("domain",$_POST["pqcms-domain"]);

    $_SESSION["pqcms-initializer-success"] = "Pomyślnie zalogowano! Za chwilę nastąpi przekierowanie...";
    header("location: success/");
    die("Niepoprawne przekierowanie. Pomyślnie zalogowano!");
}
else
{
    $sessionError = $response["desc"];
    if(array_key_exists("tries_left",$response)) $sessionError .= " Pozostało prób: ".$response["tries_left"];
//        $_SESSION["initializer-error"] = "Podano błędne dane/licencja wygasła! Pozostało Ci ".$response["tries_left"]." prób.";
    $_SESSION["pqcms-initializer-error"] = $sessionError;
    header("location: error/");
    die("Niepoprawne przekierowanie. ".$_SESSION["initializer-error"]);
}

function error($errorDescription): void
{
    $_SESSION["pqcms-initializer-error"] = $errorDescription;
    header("location: error/");
    die("Nieprawidłowe przekierowanie. ".$errorDescription);
}