<?php

/*
a - alpha
    testowane przez programistów
b - beta
    testowane przez ograniczoną grupę
c - release candidate
    poprawki błędów bety
s - stable
    wystawiona publicznie
p - patch
    poprawki błędów

major.minor.patch
major - niekompatybilne z api
minor - nowa funkcjonalność, kompatybilna z poprzednim API
patch - poprawka błędu
 */


require_once(dirname(__DIR__,3)."/database/Connection.inc.php");
class Version
{
    /**
     * Funkcja obsługuje API'ki do pobierania versji sytemu. Jest tu, żeby skrócić trochę kod aplikacji :)
     * @return void
     */
    public static function newestVesrionApiHandler($client): void
    {
        header('Content-Type: application/json; charset=utf-8');

        require_once(dirname(__DIR__, 2) . "/utils/APIUtils.php");
        APIUtils::validatePostForAuthKey($_SERVER["REMOTE_ADDR"],basename(__FILE__, '.php'),$_POST);

        $versionTypes = empty($_POST["types"]) ? null : $_POST["types"];
        if(!is_null($versionTypes) && !is_array($versionTypes))
            APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,[
                "suc" => 0,
                "desc" => "Pole \"types\" musi być tablicą!"
            ]);

        $response = [
            "suc" => 1,
            "version" => Version::getNewestVersion($client,!empty($_POST["complex"]),$versionTypes)
        ];

        APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$response);
    }

    public static function getNewestVersion(bool $client,bool $complex, ?array $versionTypes = ["r","p"]): string|array
    {
        if(is_null($versionTypes))
            $versionTypes = ["r","p"];
        $version = [];

        $conn = Connection::getConnection();

        $placeholders = implode(',', array_fill(0, count($versionTypes), '?'));
        $stmt = $conn->prepare("SELECT major, minor, patch, type, date, description FROM system_versions 
                                                             WHERE server_side = ?
                                                               AND type IN ($placeholders) 
                                                             ORDER BY id 
                                                             DESC LIMIT 1");
        $types = "i";
        $bindValues = [!$client];

        for($i=0; $i<count($versionTypes); $i++)
        {
            $types .= "s";
            $bindValues[] = $versionTypes[$i];
        }

        $stmt->bind_param($types,...$bindValues);
        $stmt->execute();


        $result = $stmt->get_result();
        if($result->num_rows != 0)
        {
            $ver = $result->fetch_assoc();

            if($complex)
            {
                $version = $ver;
                $version["date"] = Version::splitDate($ver["date"]);
                $version["type_name"] = match ($version["type"]) {
                    "a" => "alpha",
                    "b" => "beta",
                    "c" => "release candidate",
                    "r" => "release",
                    "p" => "patch",
                    default => "unknown",
                };
            }
            else
                $version = "${ver["type_name"]}-${ver["major"]}.${ver["minor"]}.${ver["patch"]} (${ver["date"]})";
        }

        $result->close();
        $stmt->close();
        $conn->close();
        return $version;
    }

    /**
     * Funkcja, która zwraca nowszą wersję systemu. Jeśli takowa nie występuje, zwraca wartość null. Wersja zwracana
     * jest w formie complex (tak jak w getNewestVersion dla complex = true)!
     * @param bool $client true - wersja klienta. false - wersja serwera
     * @param array $version aktualna wersja systemu (w formie complex)
     * @return array|null wersja w formie complex. Jeśli nie ma nowszej wersji, jest to pusta tablica, a gdy podany version jest
     * niepoprawny, zostanie zwrócony null.
     */
    static function getNewerVersion(bool $client, array $version, ?array $versionTypes = ["r","p"]): array|null
    {
        if(is_null($versionTypes))
            $versionTypes = ["r","p"];

//        var_dump($version);
        if(!isset($version["major"]) || !isset($version["minor"]) || !isset($version["patch"]) || !isset($version["type"]))
            return null;
        if(!ctype_digit($version["major"]) || !ctype_digit($version["minor"]) || !ctype_digit($version["patch"]))
            return null;

        $conn = Connection::getConnection();
        $placeholders = implode(',', array_fill(0, count($versionTypes), '?'));
//        $stmt = $conn->prepare("SELECT major, minor, patch, type, date, description, changes FROM system_versions
//             WHERE server_side = ?
//               AND major >= ?
//               AND minor >= ?
//               AND patch >= ?
//               AND NOT (type != ? AND major = ? AND minor = ? AND patch = ?)
//               AND type IN ($placeholders)
//             ORDER BY type DESC, id DESC LIMIT 1");
        $stmt = $conn->prepare("SELECT major, minor, patch, type, date, description FROM system_versions 
             WHERE server_side = ?
               AND major >= ?
               AND minor >= ? 
               AND patch >= ?
               AND NOT (type = ? AND major = ? AND minor = ? AND patch = ?)
               AND type IN ($placeholders)
             ORDER BY type, id LIMIT 1");
        
        $bindValues = [!$client,$version["major"],$version["minor"],$version["patch"],$version["type"][0],$version["major"],$version["minor"],$version["patch"]];
        $types = "iiiisiii";
        for($i=0; $i<count($versionTypes); $i++)
        {
            $types .= "s";
            $bindValues[] = $versionTypes[$i];
        }

        $stmt->bind_param($types,...$bindValues);
        $stmt->execute();

        $result = $stmt->get_result();
        if($result->num_rows != 0)
        {
            $version = $result->fetch_assoc();

            $version["type_name"] = match ($version["type"]) {
                "a" => "alpha",
                "b" => "beta",
                "c" => "release candidate",
                "r" => "release",
                "p" => "patch",
                default => "unknown",
            };

//            wielkość pliku zwracana w bajtach, kb, mb w zależności od wielkości
            $versionStringified = "${version["major"]}.${version["minor"]}.${version["patch"]}";
//            https://poleq.pl/server/updates/(type_name)/(type_name)-(version).zip
            $bytes = filesize(dirname(__DIR__,3)."/updates/${version["type_name"]}/${version["type_name"]}-$versionStringified.zip");
//            https://poleq.pl/server/updates/(type_name)/(type_name)-(version).json
            $bytes += filesize(dirname(__DIR__,3)."/updates/${version["type_name"]}/${version["type_name"]}-$versionStringified.json");
            if($bytes < 1024)
                $version["size"] = $bytes." B";
            elseif($bytes < 1048576)
                $version["size"] = round($bytes/1024,2)." KB";
            else
                $version["size"] = round($bytes/1048576,2)." MB";
        }
        else
            $version = [];

        $result->close();
        $stmt->close();
        $conn->close();

        return $version;
    }

    public static function splitDate(string $date): array {
//        rrrr-mm-dd hh-mm
        $splitted = preg_split('/(-|:| )/', $date);

        $keys = ["year","month","day","hour","minute"];

        $newDate = [];
        $i = 0;
        foreach($splitted as $value)
        {
            $newDate[$keys[$i]] = (int)$value;
            $i++;
        }

        return $newDate;
    }

    static function stringifyDate($versionDateArray): string
    {
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
}