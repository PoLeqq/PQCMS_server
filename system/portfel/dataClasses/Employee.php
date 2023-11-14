<!-- TOTEST -->
<?php

    require "../WalletData.php";
    require "Person.php";
    

    class Employee extends Person
    {
        protected $hours;
        protected $salary;

        public function __construct(int $id, string $name, string $surname, string $description, int $reputation, int $hours, float $salary) {
            parent::__construct($id,$name,$surname,$description,$reputation);
            $this->hours = $hours;
            $this->salary = $salary;
        }

        public function getHours() {
            return $this->hours;
        }

        public function getSalary() {
            return $this->salary;
        }

        public function save() {
            require_once "../WalletData.php";
            $wallet = new WalletData();

            $employeesJSON = array(
                "id" => $this->id,
                "name" => $this->name,
                "surname" => $this->surname,
                "description" => $this->description,
                "reputation" => $this->reputation,
                "hours" => $this->hours,
                "salary" => $this->salary
            );

            $employees = $wallet->getObject("employees");
            array_push($employees,$employeesJSON);

            $wallet->setObject("employees",$employees);
            $wallet->saveData();
        }

        public static function getEmployeeById(int $id) {
            $wallet = new WalletData();

            foreach($wallet->getObject("employees") as $employee) {
                if($employee["id"] == $id)
                    return $employee;
            }
            return null;
        }

        public static function getEmployees() {
            $wallet = new WalletData();
            $employeesArray = [];
            foreach($wallet->getObject("employees") as $emp)
                array_push($employeesArray,new Employee((int) $emp["id"],$emp["name"],$emp["surname"],$emp["description"],(int) $emp["reputation"],(int) $emp["hours"],(float) $emp["salary"]));
            return $employeesArray;
        }
    }

?>