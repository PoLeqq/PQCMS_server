<?php

require_once(dirname(__DIR__)."/utils/validators/Validator.inc.php");
require_once(dirname(__DIR__,2)."/objects/Website.inc.php");
require_once(dirname(__DIR__,2)."/utils/SQLSecurity.php");

class APIUtils
{
//    dodać może pola post, apiname fields (z 22) i validatorData (22, zamiast ["s","s(128)"])?
    /**
     * Funkcja sprawdza, czy podana tablica posiada klucze: domain, secure_key (podstawowe dane API).
     * Jeżeli test przejdzie pomyślnie, klucz licencyjny zostaje unieważniony.
     * @param array $post tablica $_POST
     * @param string $apiName nazwa pliku API, na który weryfikuje dane (potrzebny do zużywania klucza licencyjnego, do opisu)
     * @return array odpowiedź: "suc": (0/1), dla 0 również "desc": "string: opis błędu"
     */
    public static function validatePost(array $post, string $apiName): array
    {
        if(empty($post["domain"]) || empty($post["secure_key"]))
            return ["suc" => 0, "desc" => "Sprawdź poprawność post'ów!"];

        $fields = ["domain","secure_key"];
        $validatorResponse = Validator::validate([$post["domain"],$post["secure_key"]],["s","s(128)"]);
        if($validatorResponse["suc"] == 0)
            return ["suc" => 0, "desc" => "Walidacja nie powiodła się dla pola \"{$fields[$validatorResponse["element_index"]]}\""];

//        Różne "sprawdzacze"
        $website = APIUtils::getWebsite($post);
        if(is_null($website) || !$website->doesExists())
            return ["suc" => 0, "desc" => "Nie znaleziono strony o podanej domenie!"];

//        Sprawdzenie, czy klucz ma wartości tylko 0-9,a-f
        $insecureCharsResponse = SQLSecurity::generateResponseForAPI(SQLSecurity::doesStringContains($post["secure_key"],SQLSecurity::getKeyCharacters(),true),"secure_key");
        if(sizeof($insecureCharsResponse) !== 0)
            return $insecureCharsResponse;

        if(!$website->isProperSecureKey($post["secure_key"]))
            return ["suc" => 0, "desc" => "Niepoprawny klucz zabezpieczenia!"];
//        Unieważnienie klucza
        $website->invalidateSecureKey($post["secure_key"],$apiName);
        
        return ["suc" => 1];
    }

    /**
     * Funkcja podobna do APIUtils::validatePost, jednak posiada również sprawdzenie poprawności auth_key
     * Funkcja sprawdza, czy podana tablica posiada klucze: domain, secure_key (podstawowe dane API).
     * Jeżeli test przejdzie pomyślnie, klucz licencyjny zostaje unieważniony.
     * @param array $post tablica $_POST
     * @return array odpowiedź: "suc": (0/1), dla 0 również "desc": "string: opis błędu"
     */
    public static function validatePostForAuthKey(array $post): array
    {
//        Jeżeli nie ma auth_key to GG
        if(empty($post["auth_key"]))
            return ["suc" => 0, "desc" => "Akcja niemożliwa. Nie podano \"auth_key\"!"];

//        Sprawdzenie, czy auth_key to string(128)
        $fields = ["auth_key"];
        $validatorResponse = Validator::validate([$post["auth_key"]],["s(128)"]);
        if($validatorResponse["suc"] == 0)
            return ["suc" => 0, "desc" => "Walidacja nie powiodła się dla pola \"{$fields[$validatorResponse["element_index"]]}\""];
//            return ["suc" => 0, "desc" => "Walidacja nie powiodła się dla pola \"{$fields[$validatorResponse["element_index"]]}\" (podano: ${post["auth_key"]}"];

//        Sprawdzenie, czy klucz ma wartości tylko 0-9,a-f
        $insecureCharsResponse = SQLSecurity::generateResponseForAPI(SQLSecurity::doesStringContains($post["auth_key"],SQLSecurity::getKeyCharacters(),true),"auth_key");
        if(sizeof($insecureCharsResponse) !== 0)
            return $insecureCharsResponse;

//        Sprawdzenie, czy sesja jest dalej aktywna na serwerach PQCMS
        $website = self::getWebsite($post);

        require_once(dirname(__DIR__,2)."/objects/website/AuthKey.inc.php");
        if(!AuthKey::isValidAuthKeyForIp($website->getId(),$_SERVER["REMOTE_ADDR"],$post["auth_key"])["valid"])
            return ["suc" => 0, "desc" => "Sesja konta jest nieaktywna!"];

//        Jest wszystko super :)
        return ["suc" => 1];
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
        $id = Website::getWebsiteIDByMatching("domain",$post["domain"]);
        if(is_null($id)) return null;

        $website = new Website($id);
        if(!$website->doesExists()) return null;
        return $website;
    }
}