<?php

    require_once(dirname(__DIR__)."/JSONObject.php");

    /**
     * Przedstawia dział "electrocms" w electrocms/config/config.json - dane o systemie
     */
    class JSONPQCMS extends JSONObject {
        function __construct() {
            parent::__construct("pqcms","/files/data.json");
        }

        function getDomain() {
            return $this->getObject("domain");
        }

        /**
         * Zwraca wersję systemu
         */
        function getVersion() {
            return $this->getObject("version");
        }

        /**
         * Zwraca nazwę użytkownika (systemowego)
         */
        function getLogin() {
            return $this->getObject("login");
        }

        /**
         * Zmienia nazwę użytkownika (systemowego)
         * UWAGA!!! Aby zapisać do pliku, trzeba użyć funkcji saveData()
         */
        function setLogin($login) {
            $this->setObject("login",$login);
        }

        /**
         * Zwraca klucz licencyjny (systemowy)
         */
        function getLicenseKey() {
            return $this->getObject("license_key");
        }
                
        /**
         * Sprawdza, czy podany napis jest prawidłowy (według schematu: xxxx-xxxx-xxxx-xxxx, gdzie x - liczba lub duża litera)
         *
         * @param  string $licenseKey klucz licencyjny
         * @return bool czy poprawny
         */
        function isProperLicenseKey($licenseKey) {
            $pattern = '/[0-9A-Z]{4}\-[0-9A-Z]{4}\-[0-9A-Z]{4}\-[0-9A-Z]{4}/';
            if(preg_match($pattern, $licenseKey)) return true;
            return false;
        }
        
        /**
         * Zmienia klucz licencyjny (systemowy)
         * UWAGA!!! Aby zapisać do pliku, trzeba użyć funkcji saveData()
         *
         * @param  string $licenseKey klucz licencyjny
         * @return bool czy zmieniono
         */
        function setLicenseKey($licenseKey) {
            if(!$this->isProperLicenseKey($licenseKey))
                return false;
            return $this->setObject("license-key",$licenseKey);
        }
    }