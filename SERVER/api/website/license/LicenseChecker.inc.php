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

    function addCheckLicenseHistory($ip, $requestDomain, $domain, $login, $licenseKey, $successful): void
    {
        $conn = Connection::getConnection();
        if(!$successful) $successful = "0";
        $conn->query("INSERT INTO check_license_history (ip,request_domain,domain,login,license_key,successful) VALUES ('$ip','$requestDomain','$domain','$login','$licenseKey',$successful)");
        $conn->close();
    }

    function checkLicense($remoteAddr, $httpReferer, $domain, $login, $license_key): array
    {
        if(isBanned($remoteAddr)) return ["err" => "To IP jest zablokowane!"];

        $requestDomain = null;
        $referer = parse_url($httpReferer);
        if(isset($referer["host"]))
            $requestDomain = $referer["host"];

        $clientServerIps = gethostbynamel($domain);

//        Tutaj jest jak podana domena (przez klienta i u nas) nie istnieje 😲 Jak to możliwe? Może się nigdy nie zdarzy :p
        if($clientServerIps === false)
        {
            addCheckLicenseHistory($remoteAddr,$requestDomain,$domain,$login,$license_key,false);
            return["err" => "Nieprawidłowa nazwa hosta."];
        }

//        TODO do wywalenia
        $clientServerIps[] = "::1";

        if(!in_array($remoteAddr, $clientServerIps))
        {
//            TODO do logów sk..syna XD
//            nie ma nic za darmo, niech płaci
            addCheckLicenseHistory($remoteAddr,$requestDomain,$domain,$login,$license_key,false);
//            return["err" => "Nieprawidłowa nazwa hosta. Czy na pewno masz pliki na odpowiednim serwerze?"];
            return["err" => "Nieprawidłowa nazwa hosta. Czy na pewno masz pliki na odpowiednim serwerze? DEBUG: TwojeIP:".$remoteAddr.";ZnalezioneIP:".join(",",$clientServerIps)];
        }

        $websiteID = Website::getWebsiteIDByMatching("domain",$domain);
        if($websiteID == null) {
            addCheckLicenseHistory($remoteAddr,$requestDomain,$domain,$login,$license_key,false);
            return(["err" => "Authorization failed.","tries_left" => getTries($remoteAddr)]);
        }

        $website = new Website($websiteID);
        if($website->getLogin() != $login) {
            addCheckLicenseHistory($remoteAddr,$requestDomain,$domain,$login,$license_key,false);
            return(["err" => "Authorization failed.","tries_left" => getTries($remoteAddr)]);
        }

        if($website->getLicenseKey() != $license_key) {
            addCheckLicenseHistory($remoteAddr,$requestDomain,$domain,$login,$license_key,false);
            return(["err" => "Authorization failed.","tries_left" => getTries($remoteAddr)]);
        }

        addCheckLicenseHistory($remoteAddr,$requestDomain,$domain,$login,$license_key,true);
        return ["suc" => "Authorization succeeded!", "expiry_date" => $website->getLicenseExpiration()];
    }