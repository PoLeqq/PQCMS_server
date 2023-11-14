<?php

session_start();

if(empty($_SESSION["electrocms-client-logged"]))
    die("Nie jesteś zalogowany!");

require_once(dirname(__DIR__, 2) . "/objects/Website.inc.php");
$website = new Website($_SESSION["electrocms-client-website-id"]);
if($website->getAdminId() != null)
{
    $_SESSION["electrocms-client-announce-panel"] = ["err" => "Ta domena posiada już konto administratora!"];
    die("Ta domena posiada już konto administratora! <a href=\"../\">Powrót</a>");
}

if(!isset($_POST["token"]))
    die("Nieprawidłowe przekierowanie.");

if($_SESSION["electrocms-client-token-first-account"] != $_POST["token"])
    die("Nieprawidłowy token.");

if(empty($_POST["username"]) || empty($_POST["nickname"]) || empty($_POST["password"]))
    die("Wypełnij wszystkie pola!");

if(strlen($_POST["username"]) < 5 || strlen($_POST["username"]) > 30)
    die("Login musi mieć od 5 do 30 znaków!");

if(strlen($_POST["nickname"]) < 5 || strlen($_POST["nickname"]) > 30)
    die("Nazwa użytkownika musi mieć od 5 do 30 znaków!");

if(strlen($_POST["password"]) < 8 || strlen($_POST["password"]) > 40)
    die("Hasło musi mieć od 8 do 40 znaków!");

$website->addAdmin($_POST["username"],$_POST["nickname"],$_POST["password"]);
$_SESSION["electrocms-client-announce-panel"] = ["suc" => "Dodano konto administratora!"];
header("location: ../");
die("Dodano konto administatora. Błędne przekierowanie!");