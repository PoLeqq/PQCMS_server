<?php 
    class DefaultPermission {
        private $name;
        private $value;

        public function __construct($name) {
            $this->name = $name;
        }

        public function getName() {
            return $this->name;
        }
        
        /**
         * Ustawia nową wartość permisji
         *
         * @param  mixed $value
         * @return void
         */
        public function setValue($value) {
            
        }
    }