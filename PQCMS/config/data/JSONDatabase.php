<?php

require_once(dirname(__DIR__)."/JSONObject.php");

/**
 * Przedstawia dział "database" w electrocms/config/config.json - dane o bazie danych
 */
class JSONDatabase extends JSONObject
{
    function __construct()
    {
        parent::__construct("database","/files/data.json");
    }

    /**
     * Zwraca host DB
     */
    function getHost(): string
    {
        return $this->getObject("host");
    }

    /**
     * Zmienia host DB
     * UWAGA!!! Aby zapisać do pliku, trzeba użyć funkcji saveData()
     */
    function setHost($host): void
    {
        $this->setObject("host",$host);
    }

    /**
     * Zwraca użytkownika DB
     */
    function getUser(): string
    {
        return $this->getObject("user");
    }

    /**
     * Zmienia użytkownika DB
     * UWAGA!!! Aby zapisać do pliku, trzeba użyć funkcji saveData()
     */
    function setUser($user): void
     {
        $this->setObject("user",$user);
    }

    /**
     * Zwraca hasło DB
     */
    function getPassword(): string 
    {
        return $this->getObject("password");
    }

    /**
     * Zmienia hasło DB
     * UWAGA!!! Aby zapisać do pliku, trzeba użyć funkcji saveData()
     */
    function setPassword($password): void
    {
        $this->setObject("password",$password);
    }

    /**
     * Zwraca nazwę DB
     */
    function getName(): string
    {
        return "pqcms";
    }
}