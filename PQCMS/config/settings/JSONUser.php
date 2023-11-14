<?php

    require_once(dirname(__DIR__)."/JSONObject.php");

    /**
     * Przedstawia dział "user" w electrocms/config/settings.json - ustawienia użytkownika
     */
    class JSONUser extends JSONObject {
        function __construct() {
            parent::__construct("user","/files/settings.json");
        }

        /**
         * Zwraca długość sesji użytkownika
         */
        function getSessionTime() {
            return $this->getObject("session_time");
        }
    }