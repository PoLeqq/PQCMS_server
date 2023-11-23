<?php

header("Content-Type: application/json; charset=utf-8");

require_once(dirname(__DIR__, 3) . "/objects/Website.inc.php");
function getTries($ip): int
{
    $conn = Connection::getConnection();

    date_default_timezone_set("Europe/Warsaw");
    $date = date("Y-m-d");

    $result = $conn->query("SELECT date FROM check_license_history WHERE ip='$ip' AND date LIKE '$date%' AND successful=0");
    $res = 5 - $result->num_rows;
    $conn->close();
    return $res;
}

function isBanned($ip): bool
{
    if(getTries($ip) <= 0) return true;

    $conn = Connection::getConnection();
    $result = $conn->query("SELECT * FROM check_license_banned_ips WHERE ip='$ip'");
    $banned = $result->num_rows >= 1;

    $conn->close();

    if(!$banned) $banned = getTries($ip) <= 0;
    return $banned;
}

function addCheckLicenseHistory($ip, $requestDomain, $domain, $login, $licenseKey, $successful, $description): void
{
    $conn = Connection::getConnection();
    if(!$successful) $successful = "0";
    date_default_timezone_set('Europe/Warsaw');
    $now = date("Y-m-d H:i:s");
    $conn->query("INSERT INTO check_license_history (ip,request_domain,domain,login,license_key,date,successful,description) VALUES ('$ip','$requestDomain','$domain','$login','$licenseKey','$now',$successful,'$description')");
    $conn->close();
}

function checkLicense($remoteAddr, $httpReferer, $domain, $login, $license_key): array
{
    if (isBanned($remoteAddr)) return ["suc" => 0, "desc" => "To IP jest zablokowane!"];

    $requestDomain = null;
    $referer = parse_url($httpReferer);
    if (isset($referer["host"]))
        $requestDomain = $referer["host"];

    $clientServerIps = gethostbynamel($domain);

//        Tutaj jest jak podana domena (przez klienta i u nas) nie istnieje 😲 Jak to możliwe? Może się nigdy nie zdarzy :p
    if ($clientServerIps === false) {
        addCheckLicenseHistory($remoteAddr, $requestDomain, $domain, $login, $license_key, false, "Nieprawidłowa nazwa hosta.");
        return ["suc" => 0, "desc" => "Nieprawidłowa nazwa hosta."];
    }

//        TODO do wywalenia
    $clientServerIps[] = "::1";

    if (!in_array($remoteAddr, $clientServerIps)) {
//        do logów sk..syna XD
//        nie ma nic za darmo, niech płaci
        addCheckLicenseHistory($remoteAddr, $requestDomain, $domain, $login, $license_key, false, "SCAM? Nieprawidłowa nazwa hosta. Czy na pewno masz pliki na odpowiednim serwerze?");
        return ["suc" => 0, "desc" => "Nieprawidłowa nazwa hosta. Czy na pewno masz pliki na odpowiednim serwerze?"];
//        return["suc" => 0, "desc" => "Nieprawidłowa nazwa hosta. Czy na pewno masz pliki na odpowiednim serwerze? DEBUG: TwojeIP:".$remoteAddr.";ZnalezioneIP:".join(",",$clientServerIps)];
    }

    $websiteID = Website::getWebsiteIDByMatching("domain", $domain);
    if ($websiteID == null) {
        addCheckLicenseHistory($remoteAddr, $requestDomain, $domain, $login, $license_key, false, "Nie ma takiej domeny");
        return (["suc" => 0, "desc" => "Autoryzacja nie powiodła się.", "tries_left" => getTries($remoteAddr)]);
    }

    $website = new Website($websiteID);
    if ($website->getLogin() != $login) {
        addCheckLicenseHistory($remoteAddr, $requestDomain, $domain, $login, $license_key, false, "Niepoprawny login");
        return (["suc" => 0, "desc" => "Autoryzacja nie powiodła się.", "tries_left" => getTries($remoteAddr)]);
    }

    if ($website->getLicenseKey() != $license_key) {
        addCheckLicenseHistory($remoteAddr, $requestDomain, $domain, $login, $license_key, false, "Niepoprawny klucz");
        return (["suc" => 0, "desc" => "Autoryzacja nie powiodła się.", "tries_left" => getTries($remoteAddr)]);
    }

    $licenseExpiration = $website->getLicenseExpiration();

    if ($licenseExpiration != null) {
        $licenseDatetime = new DateTime($licenseExpiration);

        date_default_timezone_set('Europe/Warsaw');
        $nowDatetime = new DateTime(date("Y-m-d H:i:s"));

        $expired = false;
        if ($licenseDatetime->format("Y") < $nowDatetime->format("Y"))
            $expired = true;
        else if ($licenseDatetime->format("m") < $nowDatetime->format("m"))
            $expired = true;
        else if ($licenseDatetime->format("d") < $nowDatetime->format("d"))
            $expired = true;


        if ($expired) {
            addCheckLicenseHistory($remoteAddr, $requestDomain, $domain, $login, $license_key, false, "Licencja wygasła");
            return (["suc" => 0, "desc" => "Autoryzacja nie powiodła się. Licencja wygasła!", "tries_left" => getTries($remoteAddr)]);
        }

        //    $year = $dateTime->format('Y');
        //    $month = $dateTime->format('m');
        //    $day = $dateTime->format('d');
        //    $hour = $dateTime->format('H');
        //    $minute = $dateTime->format('i');
        //    $second = $dateTime->format('s');


    }

    addCheckLicenseHistory($remoteAddr, $requestDomain, $domain, $login, $license_key, true, "");
    return ["suc" => 1, "desc" => "Autoryzacja powiodła się!", "expiry_date" => $licenseExpiration];
}