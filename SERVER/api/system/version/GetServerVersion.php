<?php

header('Content-Type: application/json; charset=utf-8');

// RODZAJE WERSJI:
// alpha - niedokończona, wiele błędów. Testowana przez ogr. grupę
// beta - po alphie, bardziej stabilna, nadal błędy. Testowana publicznie

// release candidate (rc) - potencjalnie ostateczna. Jeżeli nie ma błędów -> sable
// stable - ostateczna wersja
// LUB
// pre-release
// release

// RODZAJE WERSJI (numerki):
// major.minor.patch
// major - znaczne zmiany (nowe funkcje, interfejsy)
// minor - mniejsze aktual., poprawki i drobne ulepszenia
// patch - małe akt., naprawiają konkretne bugi/lepsze zabezpiecznia

// "dev-1.0.0"

require_once(dirname(__DIR__, 2) . "/utils/APIUtils.php");
APIUtils::validatePostForAuthKey($_POST);

require_once("Version.inc.php");
$response["version"] = Version::getVersion("server",!empty($_POST["complex"]));
echo json_encode($response,JSON_UNESCAPED_UNICODE);