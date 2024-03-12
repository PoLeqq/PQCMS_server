<?php

require_once(dirname(__DIR__, 3) . "/objects/Website.inc.php");
function getTries($ip): int
{
    $conn = Connection::getConnection();

    date_default_timezone_set("Europe/Warsaw");
    $date = date("Y-m-d")."%";

    $stmt = $conn->prepare("SELECT date FROM check_license_history WHERE ip= ? AND date LIKE ? AND successful=0 AND description != 'To IP jest zablokowane!'");
    $stmt->bind_param("ss",$ip,$date);
    $stmt->execute();

//    ilość dozwolonych prób do weryfikacji licensji na dzień (aktualnie 10)
    $res = 10 - $stmt->get_result()->num_rows;
    $conn->close();
    return $res;
}

function isBanned($ip): bool
{
    if(getTries($ip) <= 0) return true;

    $conn = Connection::getConnection();
    $stmt = $conn->prepare("SELECT * FROM check_license_banned_ips WHERE ip = ?");
    $stmt->bind_param("s",$ip);
    $stmt->execute();

    $banned = $stmt->get_result()->num_rows >= 1;

    $conn->close();

    if(!$banned) $banned = getTries($ip) <= 0;
    return $banned;
}

function addCheckLicenseHistory(string $ip, ?string $requestDomain, string $domain, string $login, string $licenseKey, ?bool $successful, ?string $description): void
{
    $conn = Connection::getConnection();
    date_default_timezone_set('Europe/Warsaw');
    $now = date("Y-m-d H:i:s");
    $stmt = $conn->prepare("INSERT INTO check_license_history 
        (ip,request_domain,domain,login,license_key,date,successful,description) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->bind_param("ssssssis",$ip,$requestDomain,$domain,$login,$licenseKey,$now,$successful,$description);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

function checkLicense($remoteAddr, $httpReferer, $domain, $login, $license_key): array
{
    $requestDomain = null;
    $referer = parse_url($httpReferer);
    if (isset($referer["host"]))
        $requestDomain = $referer["host"];

    if (isBanned($remoteAddr)) {
        addCheckLicenseHistory($remoteAddr, $requestDomain, $domain, $login, $license_key, false, "To IP jest zablokowane!");

//        TODO usunięcie sesji logowania (auth key)
//        $conn = Connection::getConnection();
//        $conn->query("UPDATE");


        return ["suc" => 0, "desc" => "To IP jest zablokowane!"];
    }

    if($domain !== "localhost.localhost")
    {
//        $clientServerIps = gethostbynamel($domain);
        $clientServerIps = dns_get_record($domain, DNS_AAAA) + dns_get_record($domain, DNS_A);
    //        Tutaj jest jak podana domena (przez klienta i u nas) nie istnieje 😲 Jak to możliwe? Może się nigdy nie zdarzy :p

//        if($clientServerIps === false)
//        {
//            addCheckLicenseHistory($remoteAddr, $requestDomain, $domain, $login, $license_key, false, "Nieprawidłowa nazwa hosta.");
//            return ["suc" => 0, "desc" => "Nieprawidłowa nazwa hosta."];
//        }

        if(empty($clientServerIps))
        {
            addCheckLicenseHistory($remoteAddr, $requestDomain, $domain, $login, $license_key, false, "Nieprawidłowa nazwa hosta.");
            return ["suc" => 0, "desc" => "Nieprawidłowa nazwa hosta."];
        }

        $goodIP = false;
        foreach($clientServerIps as $record)
        {
            if((!empty($record["ipv6"]) && $record["ipv6"] === $remoteAddr) || (!empty($record["ipv4"]) && $record["ipv4"] === $remoteAddr))
            {
                $goodIP = true;
                break;
            }
        }

        if(!$goodIP)
        {
//        do logów sk..syna XD
//        nie ma nic za darmo, niech płaci
            addCheckLicenseHistory($remoteAddr, $requestDomain, $domain, $login, $license_key, false, "SCAM? Nieprawidłowa nazwa hosta. Czy na pewno masz pliki na odpowiednim serwerze?");
            return ["suc" => 0, "desc" => "Nieprawidłowa nazwa hosta. Czy na pewno masz pliki na odpowiednim serwerze?"];
//        return["suc" => 0, "desc" => "Nieprawidłowa nazwa hosta. Czy na pewno masz pliki na odpowiednim serwerze?",
//            "yip" => $remoteAddr,
//            "sip" => $clientServerIps];
        }
    }

    $websitesIDsArray = Website::getWebsitesIDArrayByMatchingDomain($domain);

//    PRZY ERROR: klucz "s" oznacza "show", czyli czy można pokazać userowi błąd, a "d" do "description"
    $error = [];
    $website = null;
    $finalError = true;
    foreach($websitesIDsArray as $websiteID)
    {
        $website = new Website($websiteID);

        if($website->getLogin() != $login ||
            $website->getLicenseKey() != $license_key ||
            $website->isBlocked() ||
            $website->isExpired())
        {
            if($website->getLogin() != $login)                $error = ["s" => false, "d" => "Niepoprawny login"];
            elseif($website->getLicenseKey() != $license_key) $error = ["s" => false, "d" => "Niepoprawny klucz"];
            elseif($website->isBlocked())                     $error = ["s" => true, "d" => "Strona zablokowana"];
            else
            {
                if($website->isExpired())
                    $website->tryRenewLicense();
                if($website->isExpired())                     $error = ["s" => true, "d" => "Licencja wygasła"];
            }

//            addCheckLicenseHistory($remoteAddr, $requestDomain, $domain, $login, $license_key, false, $error["d"]);
        }
        else
        {
            $finalError = false;
            break;
        }
    }

    if(is_null($website))
        return (["suc" => 0, "desc" => "Autoryzacja nie powiodła się.", "tries_left" => getTries($remoteAddr)]);

    if($finalError && !empty($error))
    {
        addCheckLicenseHistory($remoteAddr, $requestDomain, $domain, $login, $license_key, false, $error["d"]);
//        for($i = 1; $i < count($error); $i++)
//            addCheckLicenseHistory($remoteAddr, $requestDomain, $domain, $login, $license_key, NULL, $error[$i]["d"]);


//        var_dump($error);
        if($error["s"])
            return (["suc" => 0, "desc" => "Autoryzacja nie powiodła się. ${error["d"]}.", "tries_left" => getTries($remoteAddr)]);
        else
            return (["suc" => 0, "desc" => "Autoryzacja nie powiodła się.", "tries_left" => getTries($remoteAddr)]);
//        return ["suc" => 0, "desc" => "Autoryzacja nie powiodła się.", "id" => $website->getId()];
    }

// Jeżeli żadne z ID nie spowodowało przerwania autoryzacji, dodaj historię i zwróć sukces.
    addCheckLicenseHistory($remoteAddr, $requestDomain, $domain, $login, $license_key, 1, NULL);

    return ["suc" => 1, "desc" => "Autoryzacja powiodła się!", "id" => $website->getId()];
//    $websitesIDsArray = Website::getWebsitesIDArrayByMatchingDomain($domain);
//
//    if ($websiteID == null) {
//        addCheckLicenseHistory($remoteAddr, $requestDomain, $domain, $login, $license_key, false, "Nie ma takiej domeny");
//        return (["suc" => 0, "desc" => "Autoryzacja nie powiodła się.", "tries_left" => getTries($remoteAddr)]);
//    }
//
//    $website = new Website($websiteID);
//    if ($website->getLogin() != $login) {
//        addCheckLicenseHistory($remoteAddr, $requestDomain, $domain, $login, $license_key, false, "Niepoprawny login");
//        return (["suc" => 0, "desc" => "Autoryzacja nie powiodła się.", "tries_left" => getTries($remoteAddr)]);
//    }
//
//    if ($website->getLicenseKey() != $license_key) {
//        addCheckLicenseHistory($remoteAddr, $requestDomain, $domain, $login, $license_key, false, "Niepoprawny klucz");
//        return (["suc" => 0, "desc" => "Autoryzacja nie powiodła się.", "tries_left" => getTries($remoteAddr)]);
//    }
//
//    if($website->isBlocked()) {
//        addCheckLicenseHistory($remoteAddr, $requestDomain, $domain, $login, $license_key, false, "Strona zablokowana");
//        return (["suc" => 0, "desc" => "Strona jest zablokowana.", "tries_left" => getTries($remoteAddr)]);
//    }
//
//    if($website->isExpired()) {
//        addCheckLicenseHistory($remoteAddr, $requestDomain, $domain, $login, $license_key, false, "Licencja wygasła");
//        return (["suc" => 0, "desc" => "Autoryzacja nie powiodła się. Licencja wygasła!", "tries_left" => getTries($remoteAddr)]);
//    }
//
//    addCheckLicenseHistory($remoteAddr, $requestDomain, $domain, $login, $license_key, true, "");
//    return ["suc" => 1, "desc" => "Autoryzacja powiodła się!"];
}