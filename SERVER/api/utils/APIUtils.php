<?php

use JetBrains\PhpStorm\NoReturn;

require_once(dirname(__DIR__)."/utils/validators/Validator.inc.php");
require_once(dirname(__DIR__,2)."/objects/Website.inc.php");
require_once(dirname(__DIR__,2)."/objects/SafeWebsite.inc.php");
require_once(dirname(__DIR__,2)."/utils/SQLSecurity.php");

class APIUtils
{
//    dodać może pola post, apiname fields (z 22) i validatorData (22, zamiast ["s","s(128)"])?
    /**
     * Funkcja sprawdza, czy podana tablica posiada klucze: domain, secure_key (podstawowe dane API).
     * Jeżeli test przejdzie pomyślnie, klucz licencyjny zostaje unieważniony.
     * @param array $post tablica $_POST
     */
    public static function validatePost(string $remoteAddr, string $apiName, array $post): void
    {
        if(empty($post["domain"]) || empty($post["secure_key"]) || empty($post["client_ip"]))
            APIUtils::endAPIscript($apiName, $_POST, ["suc" => 0, "desc" => "Sprawdź poprawność post'ów!"]);

        $fields = ["domain","secure_key"];
        $validatorResponse = Validator::validate([$post["domain"],$post["secure_key"]],["s(2-253)","s(128)"]);
        if($validatorResponse["suc"] == 0)
        {
            $response = ["suc" => 0, "desc" => "Walidacja nie powiodła się dla pola \"{$fields[$validatorResponse["element_index"]]}\""];
            APIUtils::endAPIscript($apiName, $_POST, $response);
        }

//        Różne "sprawdzacze"
        $website = self::getWebsite($remoteAddr,$post);
        if(is_null($website) || !$website->doesExists())
        {
            $response = ["suc" => 0, "desc" => "Nie znaleziono strony o podanej domenie!".var_export($post["domain"],true)];
            APIUtils::endAPIscript($apiName, $_POST, $response);
        }

//        REMOTE_ADDR z powodu takiego, że secure_key generowany jest per server, nie per client
        if(!$website->isProperSecureKey($_SERVER["REMOTE_ADDR"], $post["secure_key"]))
        {
            $response = ["suc" => 0, "desc" => "Niepoprawny klucz zabezpieczenia!","ip" => $_SERVER["REMOTE_ADDR"],"key"=>$post["secure_key"],"id" => $website->getId()];
            APIUtils::endAPIscript($apiName, $_POST, $response);
        }
    }

    /**
     * Funkcja WYWOŁUJE APIUtils::validatePost, jednak posiada również sprawdzenie poprawności auth_key
     * Funkcja sprawdza, czy podana tablica posiada klucze: domain, secure_key (podstawowe dane API).
     * Jeżeli test przejdzie pomyślnie, klucz licencyjny zostaje unieważniony.
     * @param array $post tablica $_POST
     */
    public static function validatePostForAuthKey(string $remoteAddr, string $apiName, array $post): void
    {
//        Wywołaj "domyślną" funkcję, jeśli jest error to zakończ już tutaj
        APIUtils::validatePost($remoteAddr,$apiName,$post);

//        Jeżeli nie ma auth_key to GG
        if(empty($post["auth_key"]))
        {
            $response = ["suc" => 0, "desc" => "Akcja niemożliwa. Nie podano \"auth_key\"!"];
            APIUtils::endAPIscript($apiName, $_POST, $response);
        }

//        Sprawdzenie, czy auth_key to string(128)
        $fields = ["auth_key"];
        $validatorResponse = Validator::validate([$post["auth_key"]],["s(128)"]);
        if($validatorResponse["suc"] == 0)
        {
            $response = ["suc" => 0, "desc" => "Walidacja nie powiodła się dla pola \"{$fields[$validatorResponse["element_index"]]}\""];
            APIUtils::endAPIscript($apiName, $_POST, $response);
        }
//            return ["suc" => 0, "desc" => "Walidacja nie powiodła się dla pola \"{$fields[$validatorResponse["element_index"]]}\" (podano: ${post["auth_key"]}"];

//        Sprawdzenie, czy sesja jest dalej aktywna na serwerach PQCMS
        $website = self::getSafeWebsite($remoteAddr,$post);
        require_once(dirname(__DIR__,2)."/objects/website/AuthKey.inc.php");
        if(!AuthKey::isValidAuthKeyForIp($website->getId(),$post["client_ip"],$post["auth_key"])["valid"])
        {
            $response = ["suc" => 0, "desc" => "Sesja konta jest nieaktywna!"];
            APIUtils::endAPIscript($apiName, $_POST, $response);
        }
    }

    /**
     * Funkcja zwraca Website. Jeżeli validatePost zwróci wynik pozytywny, funkcja ta na pewno zwróci wartość, która nie
     * jest nullem. (Wyjątkiem może być sytuacja, gdy między sprawdzeniem a wywołaniem tej funkcji website zostanie
     * usunięty z bazy danych, co jest naprawdę mało prawdopodobne.
     * @param $post $_POST
     * @return Website|null website
     */
    public static function getWebsite(string $remoteAddr, array $post): ?Website
    {
        if($post["domain"] === "localhost")
            $domain = "localhost.localhost";
        else
            $domain = $post["domain"];

        $domain = (str_starts_with($domain, "www.")) ? substr($domain, 4) : $domain;
        $idArray = Website::getWebsitesIDArrayByMatchingDomain($domain);
        if(empty($idArray))
            return null;

        $website = null;
        foreach($idArray as $id)
        {
            $web = new Website($id);
            if($web->isProperSecureKey($remoteAddr, $post["secure_key"]))
            {
                $website = $web;
                break;
            }
        }

        return $website;
    }

    public static function getSafeWebsite(string $remoteAddr, array $post): ?SafeWebsite
    {
        if($post["domain"] === "localhost")
            $domain = "localhost.localhost";
        else
            $domain = $post["domain"];

        $domain = (str_starts_with($domain, "www.")) ? substr($domain, 4) : $domain;
        $idArray = Website::getWebsitesIDArrayByMatchingDomain($domain);
        if(empty($idArray))
            return null;

        $website = null;
        foreach($idArray as $id)
        {
            $web = new Website($id);
            if($web->isProperSecureKey($remoteAddr, $post["secure_key"]))
            {
                $website = $web;
                break;
            }
        }

        if(is_null($website))
            return null;
        return new SafeWebsite($website->getId(),$_POST["client_ip"],$post["auth_key"]);
    }

    /**
     * Funkcja kończący skrypt API
     * Zapisuje efekt oraz zwraca response
     * @param string $apiName
     * @param array $post
     * @param array $response
     * @return void
     */
    #[NoReturn] public static function endAPIscript(string $apiName, array $post, array $response): void
    {
        date_default_timezone_set('Europe/Warsaw');
        $now = DateTime::createFromFormat('U.u', microtime(true));
        $date = $now->format("Y-m-d H:i:s.u");
        $jsonPost = json_encode($post,JSON_UNESCAPED_UNICODE);
        $jsonResponse = json_encode($response,JSON_UNESCAPED_UNICODE);

        require_once(dirname(__DIR__,2)."/database/Connection.inc.php");
        $conn = Connection::getConnection();
        $stmt = $conn->prepare("INSERT INTO websites_api_logs VALUES (null,?,?,?,?)");
        $stmt->bind_param("ssss",$apiName,$date, $jsonPost, $jsonResponse);
        $stmt->execute();
        $stmt->close();
        $conn->close();

        die(json_encode($response,JSON_UNESCAPED_UNICODE));
    }

    public static function isAPIenabled(string $apiName): bool
    {
        require_once(dirname(__DIR__,2)."/database/Connection.inc.php");
        $conn = Connection::getConnection();
        $stmt = $conn->prepare("SELECT enabled FROM websites_api_description WHERE name = ?");
        $stmt->bind_param("s",$apiName);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows == 0)
            $response = false;
        else
            $response = $result->fetch_row()[0];

        $result->close();
        $stmt->close();
        $conn->close();
        return $response;
    }
}