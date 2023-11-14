<?php

    require_once(dirname(__DIR__)."/JSONObject.php");

    /**
     * Przedstawia dział "login" w electrocms/config/settings.json - ustawienia logowania
     */
    class JSONLogin extends JSONObject {
        function __construct() {
            parent::__construct("login","/files/settings.json");
        }

        /**
         * Zwraca czas na jaki użytkownik będzie banowany w przypadku przekroczenia limitu logowań
         */
        function getSessionTime() {
            return $this->getObject("session_time");
        }

        /**
         * Zwraca maksymalną ilość prób logowania
         */
        function getAttempts() {
            return $this->getObject("attempts");
        }
    }