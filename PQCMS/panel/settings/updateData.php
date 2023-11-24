<?php

/**
 * Funkcja aktualizująca dane o bazie danych
 * @param string $host host
 * @param string $user użytkownik
 * @param string $password hasło
 */
function updateDatabase(string $host, string $user, string $password): array
{
    if(empty($host)) return ["suc" => 0, "desc" => "Nie podano hosta!"];
    if(empty($user)) return ["suc" => 0, "desc" => "Nie podano użytkownika!"];

    require_once(dirname(__DIR__, 2) . "/config/data/JSONDatabase.php");
    $database = new JSONDatabase();

    if($database->getHost() === $host && $database->getUser() === $user && $database->getPassword() === $password)
        return ["suc" => 0, "desc" => "Podano takie same dane!"];
    
    $database->setHost($host);
    $database->setUser($user);
    $database->setPassword($password);
    $database->saveData();

    $error = false;
    try {
        mysqli_connect($database->getHost(),$database->getUser(),$database->getPassword());
    } catch(Exception) {
        $error = true;
    }

    return ["suc" => 1, "desc" => "Zmieniono dane do bazy danych", "conn_err" => $error];
}

/**
 * Funkcja aktualizująca dane systemowe
 * @param string $login użytkownik
 * @param string $licenseKey klucz licencyjny
 * @return array
 */
function updateSystem(string $login, string $licenseKey): array
{
    if(empty($login)) return ["suc" => 0, "desc" => "Nie podano użytkownika!"];
    if(empty($licenseKey)) return ["suc" => 0, "desc" => "Nie podano klucza licencyjnego!"];

    require_once(dirname(__DIR__, 2) . "/config/data/JSONPQCMS.php");
    $pqcms = new JSONPQCMS();

    if($pqcms->getLogin() === $login && $pqcms->getLicenseKey() === $licenseKey)
        return ["suc" => 0, "desc" => "Podano takie same dane!"];

    if($pqcms->setLicenseKey($licenseKey))
    {
        $pqcms->setLogin($login);
        $pqcms->saveData();
        return ["suc" => 1, "desc" => "Zmieniono dane systemowe."];
    }

    return ["suc" => 0, "desc" => "Błędny format klucza licencyjnego!."];
}

function updateSettings(int $loginCount, int $tokenLifespan, bool $resetLoginCount, bool $resetTokenLifespan): array
{
    require_once(dirname(__DIR__,2)."/Communicator.inc.php");
    if($resetLoginCount) $posts["login_count_reset"] = true;
    else $posts["login_count"] = $loginCount;
    if($resetTokenLifespan) $posts["token_lifespan_reset"] = true;
    else $posts["token_lifespan"] = $tokenLifespan;

    return Communicator::communicate(CommunicateURL::UPDATE_SETTINGS,$posts);
}