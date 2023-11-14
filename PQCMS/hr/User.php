<?php

    class User {
        private $id;
        public $nickname;
        public $displayname;
        private $email;
        private $passwordHash;
        private $rankId;
        public $perms;

        public function __construct(int $id = null, string $nickname = null, string $displayname = null, string $email = null, string $passwordHash = null, int $rankId = null, string $perms = null) {
            $this->id = $id;
            $this->nickname = $nickname;
            $this->displayname = $displayname;
            $this->email = $email;
            $this->passwordHash = $passwordHash;
            $this->rankId = $rankId;
            $this->perms = $perms;
        }
        
        /**
         * Zwraca ID użytkownika. Jeżeli użytkownik nie został utworzony/zapisany, funkcja zwróci null
         *
         * @return int ID użytkownika
         */
        public function getId() {
            return $this->id;
        }
        
        /**
         * Zwraca email użytkownika
         *
         * @return string email użytkownika
         */
        public function getEmail() {
            return $this->email;
        }
        
        /**
         * Ustawia nowy email użytkownika. Jeżeli został podany błędny adres, funkcja zwróci wartość false oraz nie nastąpią żadne zmiany.
         * Inaczej funkcja zwróci true
         *
         * @param  mixed $email nowy email
         * @return bool czy zmieniono
         */
        public function setEmail($email) {
            if(!filter_var($email,FILTER_VALIDATE_EMAIL)) return false;
            $this->email = $email;
            return true;
        }
        
        /**
         * Ustawia nowe hasło (od razu zahashowane)
         *
         * @param  string $password nowe hasło
         * @return void
         */
        public function setPassword($password) {
            $this->passwordHash = password_hash($password,PASSWORD_DEFAULT);
        }
        
        /**
         * Sprawdza, czy podane hasło jest zgodne z hasłem użytkownika
         *
         * @param  string $password hasło do sprawdzenia
         * @return bool czy poprawne hasło
         */
        public function matchPassword($password) {
            return password_verify($password, $this->passwordHash);
        }

        public function getRank() {
            return $this->rankId;
        }

        public function setRank($rank) {
            $this->rankId = $rank->getId();
        }
        
        /**
         * Sprawdza, czy użytkownik istnieje w bazie danych (na podstawie podanego id)
         *
         * @return bool czy użytkownik istnieje w bazie danych
         */
        public function doesExists() {
            if($this->id == null) return false;
            $conn = getConnection();
            $result = $conn->query("SELECT id FROM users WHERE id = $this->id");
            $doesExists = $result->num_rows > 0;
            $conn->close();
            return $doesExists;
        }
        
        /**
         * Zapisuje aktualne dane użytkownika do bazy danych.
         * Najczęstsze błędy:
         * 1062 - zabrania zapisu, aby nie było duplikatów (np. username, email)
         *
         * @return int czy udało się zmienić (w przypadku błędu zwraca jego kod [SQL], a w przypadku poprawnego wykonania - 0)
         */
        public function save() {
            if($this->id == null) $this->id = getNextUserId();

            $conn = getConnection();

            if($this->doesExists())
                $conn->query("UPDATE users SET nickname = '$this->nickname', displayname = '$this->displayname', email = '$this->email', password = '$this->passwordHash', perms = '$this->perms' WHERE id = $this->id");
            else
                $conn->query("INSERT INTO users VALUES ($this->id,'$this->nickname','$this->displayname','$this->email','$this->passwordHash','$this->perms')");
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
    function getNextUserId() {
        $conn = getConnection();
        $result = $conn->query("SHOW TABLE STATUS WHERE name='users'");
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
            require_once("../utils/database/Database.inc.php");
            return Database::getConnection();
        }
    }

    /**
     * Funkcja zwracająca wszystkich użytkowników znajdujących się w bazie danych.
     *
     * @return array tablica z użytkownikami w DB
     */
    function getAllUsers() {
        $conn = getConnection();
        $result = $conn->query("SELECT * FROM users");

        $users = [];
        while($row = mysqli_fetch_array($result)) 
            array_push($users,new User($row[0],$row[1],$row[2],$row[3],$row[4],(int) $row[5]));
        
        $conn->close();
        return $users;
    }

    /**
     * Zwraca użytkownika na podstawie pola "id" (lub null, gdy nie znaleziono)
     *
     * @param  string $id id użytkownika
     * @return User|null użytkownik (lub null)
     */
    function getUserById($id) {
        $conn = getConnection();
        $user = getUserWhere($conn,"id",$id);
        $conn->close();
        return $user;
    }

    /**
     * Zwraca użytkownika na podstawie pola "username" (lub null, gdy nie znaleziono)
     *
     * @param  string $username nazwa użytkownika
     * @return User|null użytkownik (lub null)
     */
    function getUserByUsername($username) {
        $conn = getConnection();
        $user = getUserWhere($conn,"username",$username);
        return $user;
    }

    /**
     * Zwraca użytkownika na podstawie pola "displayname" (lub null, gdy nie znaleziono)
     *
     * @param  string $displayname nazwa wyświetlana użytkownika
     * @return User|null użytkownik (lub null)
     */
    function getUserByDisplayname($displayname) {
        $conn = getConnection();
        $user = getUserWhere($conn,"displayname",$displayname);
        return $user;
    }
    
    /**
     * Zwraca użytkownika na podstawie pola "email" (lub null, gdy nie znaleziono)
     *
     * @param  string $email adres email
     * @return User|null użytkownik (lub null)
     */
    function getUserByEmail($email) {
        $conn = getConnection();
        $user = getUserWhere($conn,"email",$email);
        return $user;
    }
    
    /**
     * Pobiera wszystkie dane z tabeli "users" oraz zwraca pierwszy wynik. Jeżeli nie znaleziono żadnego wiersza funkcja zwróci wartość null
     *
     * @param  mysqli $conn połączenie z bazą danych
     * @param  string $col nazwa kolumny
     * @param  string $val wartość kolumny, która ma być spełniona
     * @return User|null użytkownik (lub null)
     */
    function getUserWhere($conn,$col,$val) {
        $sql = "SELECT * FROM users WHERE $col=";
        if(is_numeric($val)) $sql .= "$val";
        else $sql .= "'$val'";

        $result = $conn->query($sql);
        if($result->num_rows <= 0) return null;

        $row = $result->fetch_row();
        return new User($row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6]);
    }

    // $user = getUserById(1);
    // var_dump($user);
    // $user->nickname = "username";
    // $user->displayname = "dname";
    // $user->setPassword("pass");
    // $user->save();

    // var_dump($user->save());

    // var_dump(getAllUsers());
    // var_dump(getUserById(1));