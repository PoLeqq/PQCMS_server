<?php

if(empty($_POST["name"]))
    die("Uzupełnij pole imię!");

if(empty($_POST["surname"]))
    die("Uzupełnij pole nazwisko!");

if(empty($_POST["phone"]))
    die("Uzupełnij pole numer telefonu!");

if(empty($_POST["pqcms"]))
    die("Uzupełnij pole PQCMS!");

if(empty($_POST["state"]))
    die("Uzupełnij pole status!");

$message = empty($_POST["message"]) ? "" : $_POST["message"];

if($_POST["state"] > 5 || $_POST["state"] < 0)
    die("Niepoprawny status strony!");

$url = 'https://poleq.pl/server/temp/Mail.php';

// use key 'http' even if you send the request to https://...
$options = [
    'http' => [
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($_POST),
    ],
];

$context = stream_context_create($options);
@file_get_contents($url, false, $context);
//if ($result === false) {
//    /* Handle error */
//}

//dirname 4
//require_once(dirname(__DIR__,2)."/server/database/Connection.inc.php");
//$conn = Connection::getConnection();
//$conn->prepare("INSERT INTO ")

die("Wysłano zgłoszenie!");