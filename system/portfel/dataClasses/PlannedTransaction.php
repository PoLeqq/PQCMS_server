<?php

    class PlannedTransaction {
        protected $id;
        protected $title;
        protected $description;
        protected $value;
        protected $date;
        
        public function __construct(int $id, string $title, string $description, float $value, string $date) {
            $this->id = $id;
            $this->title = $title;
            $this->description = $description;
            $this->value = $value;
            $this->date = $date;
        }

        /**
         * Zwraca id transakcji
         *
         * @return int id transakcji
         */
        public function getId() {
            return $this->id;
        }
        
        /**
         * Zwraca tytuł transakcji
         *
         * @return string tytuł transakcji
         */
        public function getTitle() {
            return $this->title;
        }
                
        /**
         * Zwraca opis transakcji
         *
         * @return string opis transakcji
         */
        public function getDescription() {
            return $this->description;
        }
                
        /**
         * Zwraca wartość transakcji
         *
         * @return float wartość transakcji
         */
        public function getValue() {
            return $this->value;
        }

        /**
         * Zwraca datę transakcji jako napis w formie "yyyy-mm-dd"
         *
         * @return string config transakcji
         */
        public function getDate() {
            return $this->date;
        }

        public static function getTransactionById(int $id) {
            require_once "../WalletData.php";
            $wallet = new WalletData();

            foreach($wallet->getObject("plannedTransactions") as $plannedTransaction) {
                if($plannedTransaction["id"] == $id)
                    return $plannedTransaction;
            }
            return null;
        }
    }