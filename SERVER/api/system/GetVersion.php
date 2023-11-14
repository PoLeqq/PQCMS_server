<?php

 header('Content-Type: application/json');

// RODZAJE WERSJI:
// alpha - niedokończona, wiele błędów. Testowana przez ogr. grupę
// beta - po alfie, bardziej stabilna, nadal błędy. Testowana publicznie
// release candidate (rc) - potencjalnie ostateczna. Jeżeli nie ma błędów -> sable
// stable - ostateczna wersja

// RODZAJE WERSJI (numerki):
// major.minor.patch
// major - znaczne zmiany (nowe funkcje, interfejsy)
// minor - mniejsze aktual., poprawki i drobne ulepszenia
// patch - małe akt., naprawiają konkretne bugi/lepsze zabezpiecznia

// "dev-1.0.0"

$file = json_decode(file_get_contents(dirname(__DIR__, 2) ."/version.json"),true);

if(isset($_POST["complex"]) && $_POST["complex"])
    die(json_encode(["ver" => $file["version"]]));

$ver = $file["version"];
$version = $ver["type"]."-".$ver["major"].".".$ver["minor"].".".$ver["patch"]." (".stringifyDate($ver["date"]).")";
die(json_encode($version));

function stringifyDate($versionDateArray) {
    $year = $versionDateArray["year"];
    $month = $versionDateArray["month"];
    $day = $versionDateArray["day"];

    $date = $year."-";

    if($month < 10) $month = "0".$month;
    if($day < 10) $day = "0".$day;

    $date .= $month."-";
    $date .= $day;

    return $date;
}