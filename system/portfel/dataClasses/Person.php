<?php

    class Person {
        protected $id;
        /**
         * Imię
         * @var string
         */
        protected $name;
        /**
         * Nazwisko
         * @var string
         */
        protected $surname;
        protected $description;
        protected $reputation;

        public function __construct(int $id, string $name, string $surname, string $description, int $reputation)
        {
            $this->id = $id;
            $this->name = $name;
            $this->surname = $surname;
            $this->description = $description;
            $this->reputation = $reputation;
        }

        public function getId() {
            return $this->id;
        }

        public function getName() {
            return $this->name;
        }

        public function getSurname() {
            return $this->surname;
        }

        public function getDescription() {
            return $this->description;
        }

        public function getReputation() {
            return $this->reputation;
        }
    }