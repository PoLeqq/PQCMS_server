<?php

    class Rank {
        private $id;
        public $name;
        public $perms;
        private $priority;
        private $parent;

        public function __construct(int $id = null, string $name = null, string $perms = null, int $priority = null, int $parent = null) {
            // echo "$id<br>$name<br>$perms<br>$priority<br>";
            if($id == null) $this->id = getNextRankId();
            else $this->id = $id;
            $this->name = $name;
            $this->perms = $perms;
            $this->setPriority($priority);
            $this->setParent($parent);
        }
        
        /**
         * Zwraca ID rangi. Jeżeli ranga nie została utworzony/zapisany, funkcja zwróci null
         *
         * @return int ID użytkownika
         */
        public function getId() {
            return $this->id;
        }
        
        /**
         * Zwraca priorytet rangi
         *
         * @return int priorytet rangi
         */
        public function getPriority() {
            return $this->priority;
        }
        
        /**
         * Ustawia nowy priorytet rangi. Jeżeli jest < 0, nie zostanie zmieniony
         *
         * @param  mixed $priority priorytet rangi
         * @return void
         */
        public function setPriority($priority) {
            if($priority >= 0) $this->priority = $priority;
        }
        
        /**
         * Zwraca ID rangi rodzica
         *
         * @return int ID rangi rodzica
         */
        public function getParent() {
            return $this->priority;
        }
        
        /**
         * Ustawia nowego rodzica rangi. Jeżeli ranga o podanym ID jest nieprawidłowa, funckja zwróci wartość false i nic nie zmieni.
         * ID rangi jest nieprawidłowe, gdy:
         *  - jest takie samo jak id obecnej rangi
         *  - podanego ID nie posiada żadna ranga
         *
         * @param  mixed $parentID ID rangi rodzica
         * @return bool czy zmieniono
         */        
        public function setParent($parentID) {
            $rank = getRankById($parentID);
            if($rank == null) return false;
            $this->parent = $parentID;
            return true;
        }
        
        /**
         * Sprawdza, czy użytkownik istnieje w bazie danych (na podstawie podanego id)
         *
         * @return bool czy użytkownik istnieje w bazie danych
         */
        public function doesExists() {
            if($this->id == null) return false;
            $conn = getConnection();
            $result = $conn->query("SELECT id FROM ranks WHERE id = $this->id");
            $doesExists = $result->num_rows > 0;
            $conn->close();
            return $doesExists;
        }
        
        /**
         * Zapisuje aktualne dane użytkownika do bazy danych.
         * Najczęstsze błędy:
         * 1062 - zabrania zapisu, aby nie było duplikatów (nazwa)
         *
         * @return int czy udało się zmienić (w przypadku błędu zwraca jego kod [SQL], a w przypadku poprawnego wykonania - 0)
         */
        public function save() {
            if($this->id == null) $this->id = getNextRankId();
            if($this->parent == 0) $parent = "null";
            else $parent = $this->parent;

            $conn = getConnection();
            if($this->doesExists()) $conn->query("UPDATE ranks SET name = '$this->name', perms = '$this->perms', priority = '$this->priority', parent = $parent WHERE id = $this->id");
            else $conn->query("INSERT INTO ranks VALUES ($this->id,'$this->name','$this->perms','$this->priority',$parent)");
            $errno = mysqli_errno($conn);
            $conn->close();
            return $errno;
        }
    }
    
    /**
     * Funkcja oblicza następny dostępny nr ID użytkownika, który może zostać dodany
     *
     * @return int następny ID użytkownika
     */
    function getNextRankId() {
        $conn = getConnection();
        $result = $conn->query("SHOW TABLE STATUS WHERE name='ranks'");
        $row = $result->fetch_assoc();
        $conn->close();
        return $row['Auto_increment'];
    }
        
    /**
     * Tworzy połączenie z bazą danych (mysqli) oraz zwraca jego referencję
     *
     * @return mysqli połączenie w formie mysqli
     */
    if (!function_exists('getConnection')) {
        function getConnection() {
            require_once(realpath(dirname(dirname(__FILE__))."/utils/database/Database.php"));
            return Database::getConnection();
        }
    }

    /**
     * Funkcja zwracająca wszystkie rangi znajdujących się w bazie danych.
     *
     * @return array tablica z użytkownikami w DB
     */
    function getAllRanks() {
        $conn = getConnection();
        $result = $conn->query("SELECT * FROM ranks");

        $ranks = [];
        while($row = mysqli_fetch_array($result)) 
            array_push($ranks,new Rank($row[0],$row[1],$row[2],$row[3]));
        
        $conn->close();
        return $ranks;
    }
    
    /**
     * Funkcja zwracająca wszystkich użytkowników znajdujących się w bazie danych. Można 
     *
     * @param  mixed $orderBy kolumna według której należy sortować listę
     * @param  mixed $desc czy malejąca
     * @return array rangi
     */
    function getAllRanksOrder(string $orderBy,bool $desc) {
        $conn = getConnection();
        $sql = "SELECT * FROM ranks ORDER BY $orderBy";
        if($desc) $sql .= " DESC";
        $result = $conn->query($sql);

        $ranks = [];
        while($row = mysqli_fetch_array($result))
            array_push($ranks,new Rank($row[0],$row[1],$row[2],$row[3]));
        
        $conn->close();
        return $ranks;
    }

    /**
     * Zwraca rangę na podstawie pola "id" (lub null, gdy nie znaleziono)
     *
     * @param  string $id id rangi
     * @return Rank|null użytkownik (lub null)
     */
    function getRankById($id) {
        $conn = getConnection();
        $rank = getRankWhere($conn,"id",$id);
        return $rank;
    }

    /**
     * Zwraca rangę na podstawie pola "name" (lub null, gdy nie znaleziono)
     *
     * @param  string $name nazwa rangi
     * @return Rank|null ranga (lub null)
     */
    function getRankByName($name) {
        $conn = getConnection();
        $rank = getRankWhere($conn,"name",$name);
        return $rank;
    }
    
    /**
     * Pobiera wszystkie dane z tabeli "ranks" oraz zwraca pierwszy wynik. Jeżeli nie znaleziono żadnego wiersza funkcja zwróci wartość null
     *
     * @param  mysqli $conn połączenie z bazą danych
     * @param  string $col nazwa kolumny
     * @param  string $val wartość kolumny, która ma być spełniona
     * @return Rank|null ranga (lub null)
     */
    function getRankWhere($conn,$col,$val) {
        $sql = "SELECT * FROM ranks WHERE $col=";
        if(is_numeric($val)) $sql .= "$val";
        else $sql .= "'$val'";

        $result = $conn->query($sql);
        if($result->num_rows <= 0) return null;
        
        $row = $result->fetch_row();
        return new Rank($row[0],$row[1],$row[2],(int) $row[3]);
    }

    // $rank = new Rank(null,"admin",null,0,1);
    // var_dump($rank->save());
    // $rank = getRankById(1);
    // var_dump($rank->doesExists());
    // $rank->name = "admin";
    // var_dump($rank->save());
    $conn = getConnection();
    $conn->close();