<?php

    /**
     * Zwraca błąd w formie JSON
     * $name - nazwa (opis) błędu
     */
    function getError($name) {
        return json_encode(["err" => $name]);
    }

    /**
     * Zwraca wiadomość o sukcesie w formie JSON
     * $name - nazwa (opis) czynności
     */
    function getSuccess($name) {
        return json_encode(["succ" => $name]);
    }

    /**
     * Funkcja aktualizująca dane o bazie danych
     * @param string $host
     * @param $user
     * @param $password
     * @return false|string
     */
    function updateDatabase(string $host, $user, $password)
    {
        if(!isset($host))
            return getError("Nie podano hosta");
        if(!isset($user))
            return getError("Nie podano użytkownika");
        if(!isset($password))
            return getError("Nie podano hasła");

        require_once("../../config/JSONDatabase.php");
        $database = new JSONDatabase();

        $database->setHost($host);
        $database->setUser($user);
        $database->setPassword($password);
        $database->saveData();

        return getSuccess("Zmieniono dane do bazy danych");
    }
    
    /**
     * Funkcja aktualizująca dane o systemie
     * @param user użytownik
     * @param licenseKey klucz licencyjny
     */
    function updateElectroCMS($user, $licenseKey)
    {
        if(!isset($user))
            return getError("Nie podano użytkownika");
        if(!isset($licenseKey))
            return getError("Nie podano klucza licencyjnego");

        require_once("../../config/JSONPQCMS.php");
        $electrocms = new JSONPQCMS();

        if(!$electrocms->setUser($user))
            return getError("Nie udało się zmienić użytkownika");
        if(!$electrocms->setLicenseKey($licenseKey))
            return getError("Nie udało się zmienić klucza licencyjnego. Sprawdź jego poprawność");
        $electrocms->saveData();

        return getSuccess("Zmieniono dane systemowe");
    }