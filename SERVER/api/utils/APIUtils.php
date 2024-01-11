<?php

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
    public static function validatePost(array $post): void
    {
        if(empty($post["domain"]) || empty($post["secure_key"]))
            die(json_encode(["suc" => 0, "desc" => "Sprawdź poprawność post'ów!"],JSON_UNESCAPED_UNICODE));

        $fields = ["domain","secure_key"];
        $validatorResponse = Validator::validate([$post["domain"],$post["secure_key"]],["s(2-253)","s(128)"]);
        if($validatorResponse["suc"] == 0)
            die(json_encode( ["suc" => 0, "desc" => "Walidacja nie powiodła się dla pola \"{$fields[$validatorResponse["element_index"]]}\""],JSON_UNESCAPED_UNICODE));

//        Różne "sprawdzacze"
        $website = APIUtils::getWebsite($post);
        if(is_null($website) || !$website->doesExists())
            die(json_encode(["suc" => 0, "desc" => "Nie znaleziono strony o podanej domenie!"],JSON_UNESCAPED_UNICODE));

        if(!$website->isProperSecureKey($_SERVER["REMOTE_ADDR"], $post["secure_key"]))
            die(json_encode(["suc" => 0, "desc" => "Niepoprawny klucz zabezpieczenia!"],JSON_UNESCAPED_UNICODE));
    }

    /**
     * Funkcja WYWOŁUJE APIUtils::validatePost, jednak posiada również sprawdzenie poprawności auth_key
     * Funkcja sprawdza, czy podana tablica posiada klucze: domain, secure_key (podstawowe dane API).
     * Jeżeli test przejdzie pomyślnie, klucz licencyjny zostaje unieważniony.
     * @param array $post tablica $_POST
     * DEPRECATED~~return array odpowiedź: "suc": (0/1), dla 0 również "desc": "string: opis błędu"
     */
    public static function validatePostForAuthKey(array $post, string $apiName): void
    {
//        Wywołaj "domyślną" funkcję, jeśli jest error to zakończ już tutaj
        self::validatePost($post,$apiName);

//        Jeżeli nie ma auth_key to GG
        if(empty($post["auth_key"]))
            die(json_encode(["suc" => 0, "desc" => "Akcja niemożliwa. Nie podano \"auth_key\"!"],JSON_UNESCAPED_UNICODE));

//        Sprawdzenie, czy auth_key to string(128)
        $fields = ["auth_key"];
        $validatorResponse = Validator::validate([$post["auth_key"]],["s(128)"]);
        if($validatorResponse["suc"] == 0)
            die(json_encode(["suc" => 0, "desc" => "Walidacja nie powiodła się dla pola \"{$fields[$validatorResponse["element_index"]]}\""],JSON_UNESCAPED_UNICODE));
//            return ["suc" => 0, "desc" => "Walidacja nie powiodła się dla pola \"{$fields[$validatorResponse["element_index"]]}\" (podano: ${post["auth_key"]}"];

//        Sprawdzenie, czy sesja jest dalej aktywna na serwerach PQCMS
        $website = self::getWebsite($post);
        require_once(dirname(__DIR__,2)."/objects/website/AuthKey.inc.php");
        if(!AuthKey::isValidAuthKeyForIp($website->getId(),$_SERVER["REMOTE_ADDR"],$post["auth_key"])["valid"])
            die(json_encode(["suc" => 0, "desc" => "Sesja konta jest nieaktywna!"],JSON_UNESCAPED_UNICODE));
    }

    /**
     * Funkcja zwraca Website. Jeżeli validatePost zwróci wynik pozytywny, funkcja ta na pewno zwróci wartość, która nie
     * jest nullem. (Wyjątkiem może być sytuacja, gdy między sprawdzeniem a wywołaniem tej funkcji website zostanie
     * usunięty z bazy danych, co jest naprawdę mało prawdopodobne.
     * @param $post $_POST
     * @return Website|null website
     */
    public static function getWebsite($post): ?Website
    {
        if($post["domain"] === "localhost")
            $domain = "localhost.localhost";
        else
            $domain = $post["domain"];
        $id = Website::getWebsiteIDByMatching("domain",$domain);
        if(is_null($id)) return null;

        $website = new Website($id);
        if(!$website->doesExists()) return null;
        return $website;
    }

    public static function getSafeWebsite($post): ?SafeWebsite
    {
        if(empty($post["auth_key"]))
            return null;

        if($post["domain"] === "localhost")
            $domain = "localhost.localhost";
        else
            $domain = $post["domain"];

        $id = Website::getWebsiteIDByMatching("domain",$domain);
        if(is_null($id))
            return null;

        return new SafeWebsite($id,$_SERVER["REMOTE_ADDR"],$post["auth_key"]);
    }
}