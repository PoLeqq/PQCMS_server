<?php

    const WALLET_FILEPATH = __DIR__."/data.json";

    /**
     * Klasa umożliwiająca operację na obiekcie o podanej nazwie z pliku pqcms/config/files/config.json
     */
    class WalletData {
        /**
         * Dane pobrane z pliku JSON
         */
        protected $data;

        /**
         * Pobiera plik system/$name.json i zwraca obiekt o podanej nazwie
         * $dir - nazwa pliku
         */
        function __construct() {
            $data = file_get_contents(WALLET_FILEPATH);
            $this->data = json_decode($data, true);
        }

        /**
         * Zwraca obiekt (dziecko)
         */
        public function getObject($name) {
            return $this->data[$name];
        }
        
        /**
         * Zwraca wszystkie informacje o obiekcie w formie JSON
         * @return object dane w JSON
         */
        public function getData() {
            return $this->data;
        }

        /**
         * Zmienia dane w obiekcie
         * @param $key klucz
         * @param $value nowa wartość
         * @return bool czy zmieniono
         */
        public function setObject($key,$value) {
            // TODO Save do logów?
            $this->data[$key] = $value;
            return true;
        }

        /**
         * Zapisuje dane aktualnie znajdujące się w config.js
         */
        public function saveData() {
            // $config = json_decode(file_get_contents(WALLET_FILEPATH),true);
            file_put_contents(WALLET_FILEPATH,json_encode($this->data,JSON_PRETTY_PRINT,JSON_UNESCAPED_UNICODE));
        }
    }