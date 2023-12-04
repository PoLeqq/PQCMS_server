<?php

require_once(dirname(__DIR__,2)."/objects/Website.inc.php");
require_once(dirname(__DIR__)."/utils/validators/Validator.inc.php");

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

        $website = APIUtils::getWebsite($post);
        if(!$website->doesExists()) return ["suc" => 0, "desc" => "Nie znaleziono strony o podanej domenie!"];
        if(!$website->isProperSecureKey($post["secure_key"])) return ["suc" => 0, "desc" => "Niepoprawny klucz zabezpieczenia!"];
        $website->invalidateSecureKey($post["secure_key"],$apiName);
        
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
        if($id == null) return null;

        $website = new Website($id);
        if(!$website->doesExists()) return null;
        return $website;
    }
}