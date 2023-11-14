<?php

    require "../WalletData.php";
    require "Person.php";
    

    class Client extends Person
    {
        public function __construct(int $id, string $name, string $surname, string $description, int $reputation) {
            parent::__construct($id,$name,$surname,$description,$reputation);
        }

        public static function getEmployeeById(int $id) {
            $wallet = new WalletData();

            foreach($wallet->getObject("employees") as $employee) {
                if($employee["id"] == $id)
                    return $employee;
            }
            return null;
        }

        public static function getClients() {
            $wallet = new WalletData();
            $clientsArray = [];
            foreach($wallet->getObject("clients") as $emp)
                array_push($clientsArray,new Client((int) $emp["id"],$emp["name"],$emp["surname"],$emp["description"],(int) $emp["reputation"]));
            return $clientsArray;
        }
    }