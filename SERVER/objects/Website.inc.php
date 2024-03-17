<?php

require_once(dirname(__DIR__)."/database/Connection.inc.php");

class Website
{
    public static float $licensePrice = 50;

    protected int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDomain(): string
    {
        return $this->getField("domain");
    }

    public function getLogin(): string
    {
        return $this->getField("login");
    }

    public function getLicenseKey(): string
    {
        return $this->getField("license_key");
    }

    public function getLicenseExpiration(): ?string
    {
        return $this->getField("license_expiration");
    }

    public function isBlocked(): bool
    {
        return $this->getField("blocked");
    }

    public function isExpired(): bool
    {
        $licenseExpiration = $this->getLicenseExpiration();

        if ($licenseExpiration == null)
            return false;

        $licenseTime = strtotime($licenseExpiration);

        date_default_timezone_set('Europe/Warsaw');
        $nowTime = strtotime(date("Y-m-d H:i:s"));

        return $nowTime >= $licenseTime;
//        if($nowTime >= $licenseTime)
//            return true;
//        return false;
    }

    /**
     * Funkcja zwraca, czy licencja na następny miesiąc może być opłacona
     * @return bool true, gdy licencja może być odnowiona (na nast. raz)
     */
    public function isLicenseRenewable(): bool
    {
        $conn = Connection::getConnection();
        $stmt = $conn->prepare("SELECT id, money FROM websites_admins WHERE website_id = ?");
        $stmt->bind_param("i",$this->id);
        $stmt->execute();

        $result = $stmt->get_result();
        if($result->num_rows == 0)
            $canAfford = false;
        else
        {
            $admin = $result->fetch_assoc();
            $canAfford = $admin["money"] >= 50;
        }
        $result->close();
        $stmt->close();
        $conn->close();

        return $canAfford;
    }

    /**
     * Funkcja próbuje opłacić licencję, jeśli ta wygasła.
     * @return bool true, gdy odnowi licencję. False, gdy admin nie ma wystarczająco pieniędzy (lub jeśli licencja jest ważna)
     */
    public function tryRenewLicense(): bool
    {
        $conn = Connection::getConnection();
        $stmt = $conn->prepare("SELECT id, money FROM websites_admins WHERE website_id = ?");
        $stmt->bind_param("i",$this->id);
        $stmt->execute();

        $result = $stmt->get_result();
        if($result->num_rows == 0)
            $canAfford = false;
        else
        {
            $admin = $result->fetch_assoc();
            $canAfford = $admin["money"] >= 50;
        }
        $result->close();
        $stmt->close();


        if($canAfford)
        {
//            odjęcie kaski od konta admina
            $stmt = $conn->prepare("UPDATE websites_admins SET money = ? WHERE id = ?");
            $money = $admin["money"] - Website::$licensePrice;
            $stmt->bind_param("ii",$money,$admin["id"]);
            $stmt->execute();
            $stmt->close();

//            zapisanie do logów odświeżenia licencji
            date_default_timezone_set('Europe/Warsaw');
            $date = date("Y-m-d H:i:s");
            $stmt = $conn->prepare("INSERT INTO websites_license_renews VALUES(null,?,?,?)");
            $stmt->bind_param("isi",$this->id,$date,Website::$licensePrice);
            $stmt->execute();
            $stmt->close();

            $licenseExpirationDate = date("Y-m-d H:i", strtotime("+30 days"));
            $stmt = $conn->prepare("UPDATE websites SET license_expiration = ? WHERE id = ?");
            $stmt->bind_param("si",$licenseExpirationDate,$this->id);
            $stmt->execute();
            $stmt->close();
        }

        $conn->close();

        return $canAfford;
    }

    public function doesExists(): bool
    {
        return !is_null($this->getField("id"));
    }

    public function generateSecureKey(string $remoteAddr): array
    {
        require_once("website/SecureKey.inc.php");
        return SecureKey::generateSecureKey($this->id, $remoteAddr);
    }

    /**
     * @param string $secure_key klucz bezpieczeństwa
     * @param string $apiName nazwa API (dla nie-APIkowych plików po prostu pusty napis)
     */
    public function invalidateSecureKey(string $secure_key, string $apiName): void
    {
        require_once("website/SecureKey.inc.php");
        SecureKey::invalidateSecureKey($this->id, $secure_key, $apiName);
    }

//    Gettery, settery opierające się na obiekcie, wymagające bardziej złożonych operacji
//    (działają na kilku tabelach, nie tylko na "websites"

    public function getAdminId(): ?int
    {
        $conn = Connection::getConnection();
        $stmt = $conn->prepare("SELECT id FROM websites_admins WHERE website_id = ?");
        $stmt->bind_param("i",$this->id);
        $stmt->execute();

        $result = $stmt->get_result();
        if($result->num_rows == 0)
            $result = null;
        else
            $result = $result->fetch_row()[0];

        $stmt->close();
        $conn->close();
        return $result;
    }

    public function addAdmin(string $username, string $nickname, string $email, string $password): array
    {
        require_once(dirname(__DIR__) . "/objects/website/WebsiteAdmin.inc.php");
        return WebsiteAdmin::addWebsiteAdmin($this->id, $username, $nickname, $email, $password);
    }

    public function addUser(string $username, string $nickname, ?string $email, string $password, array $perms, bool $disabled): array
    {
        if(count($this->getUsersIds()) >= 20)
            return ["suc" => 0, "desc" => "Strona osiągnęła limit użytkowników (20)!"];
        require_once(dirname(__DIR__) . "/objects/website/WebsiteUser.inc.php");
        return WebsiteUser::addUser($this->id, $username, $nickname, $email, $password, $perms, $disabled);
    }

    public function editUser(string $username, ?string $nickname, ?string $email, ?string $password, ?array $perms, ?bool $disabled): array
    {
        require_once(dirname(__DIR__) . "/objects/website/WebsiteUser.inc.php");
        return WebsiteUser::editUser($this->id, $username, $nickname, $email, $password, $perms, $disabled);
    }

    public function editRank(string $name, ?string $displayName, ?int $priority, ?int $parentId, ?array $perms): array
    {
        require_once(dirname(__DIR__) . "/objects/website/WebsiteRank.inc.php");
        return WebsiteRank::editRank($this->id, $name, $displayName, $priority, $parentId, $perms);
    }


    public function resetUserPassword(string $username): array
    {
        require_once(dirname(__DIR__) . "/objects/website/WebsiteUser.inc.php");
        return WebsiteUser::resetPassword($this->id, $username);
    }

    public function deleteUser(string $name): void
    {
        require_once(dirname(__DIR__) . "/objects/website/WebsiteUser.inc.php");
        WebsiteUser::deleteUser($this->id, $name);
    }

    public function deleteRank(string $name): array
    {
        require_once(dirname(__DIR__) . "/objects/website/WebsiteRank.inc.php");
        return WebsiteRank::deleteRank($this, $name);
    }

    public function addRank(string $name, string $displayName, array $perms, int $priority, ?int $parentId): array
    {
        if(count($this->getRanksIds()) >= 10)
            return ["suc" => 0, "desc" => "Strona osiągnęła limit rang (10)!"];
        require_once(dirname(__DIR__) . "/objects/website/WebsiteRank.inc.php");
        return WebsiteRank::addRank($this->id, $name, $displayName, $perms, $priority, $parentId);
    }

    public function getUsersIds(): array
    {
        $conn = Connection::getConnection();
        $stmt = $conn->prepare("SELECT id FROM websites_users WHERE website_id = ? AND deleted = 0");
        $stmt->bind_param("i",$this->id);
        $stmt->execute();

        $result = $stmt->get_result();
        $response = [];
        while($row = $result->fetch_row())
            $response[] = $row[0];

        $stmt->close();
        $conn->close();
        return $response;
    }

    public function getRanksIds(): array
    {
        $conn = Connection::getConnection();
        $stmt = $conn->prepare("SELECT id FROM websites_ranks WHERE website_id = ? AND deleted = 0");
        $stmt->bind_param("i",$this->id);
        $stmt->execute();
        $result = $stmt->get_result();

        $response = [];
        while($row = $result->fetch_row())
            $response[] = $row[0];

        $stmt->close();
        $conn->close();
        return $response;
    }

    public function getUsers(): array
    {
        $conn = Connection::getConnection();
        $stmtUsers = $conn->prepare("SELECT id, username, nickname, email, perms, disabled FROM websites_users WHERE website_id = ? AND deleted = 0");
        $stmtUsers->bind_param("i",$this->id);
        $stmtUsers->execute();
        $stmtUsersResult = $stmtUsers->get_result();
        $stmtUsers->close();

        date_default_timezone_set("Europe/Warsaw");
        $date = date("Y-m-d H:i:s");
        $querySessions = $conn->query("SELECT user_id, expired_time FROM websites_auth_keys 
                    WHERE website_id = $this->id
                      AND logout = 0 
                      AND invalid = 0
                      AND admin_id IS NULL
                      AND expired_time > '$date'
                      ORDER BY id DESC;");
        $usersSessions = [];
        while($row = $querySessions->fetch_row())
            $usersSessions[$row[0]] = $row[1];

        $result = [];
        while ($row = $stmtUsersResult->fetch_row()) {
            $userRow = ["username" => $row[1], "nickname" => $row[2], "email" => $row[3], "perms" => json_decode($row[4]), "disabled" => $row[5]];
            if(array_key_exists($row[0],$usersSessions))
                $userRow["active_session"] = $usersSessions[$row[0]];

            $result[] = $userRow;
        }

        $query = $conn->query("SELECT username, email, nickname FROM websites_admins WHERE website_id = $this->id");
        if ($row = $query->fetch_row())
            $result[] = ["username" => $row[0], "email" => $row[1], "nickname" => $row[2]];

        $query->close();
        $conn->close();
        return $result;
    }

    public function getRanks(): array
    {
        $conn = Connection::getConnection();
        $stmt = $conn->prepare("SELECT name, display_name, perms, priority, parent_id FROM websites_ranks WHERE website_id = ? AND deleted = 0");
        $stmt->bind_param("i",$this->id);
        $stmt->execute();

        $result = $stmt->get_result();
        $response = [];
        while($row = $result->fetch_row())
            $response[] = ["name" => $row[0], "display_name" => $row[1], "perms" => json_decode($row[2]), "priority" => $row[3], "parent_id" => $row[4]];

        $stmt->close();
        $conn->close();
        return $response;
    }

    public function hasPermission(string $remoteAddr, string $authKey, string $perm): bool
    {
        $hasPermissions = $this->hasPermissions($remoteAddr,$authKey,[$perm]);
        if($hasPermissions["suc"] == 0)
            return false;
        return $hasPermissions["perms"][$perm];
    }

    /**
     * Funkcja sprawdzająca, czy użytkownik posiada podane permisje
     * "suc" w returnie oznacza, czy nie ma żadnych błędów (0 - błąd; nie, czy user ma permisje!)
     * @param string $remoteAddr
     * @param string $authKey
     * @param array $perms
     * @return array|int[]
     */
    public function hasPermissions(string $remoteAddr, string $authKey, array $perms): array
    {
//        Sprawdzenie, czy w ogóle istnieje wygenerowany auth_key dla tego IP
        require_once(dirname(__DIR__)."/objects/website/AuthKey.inc.php");
        if(!AuthKey::isValidAuthKeyForIp($this->id,$remoteAddr,$authKey))
            return ["suc" => 0, "desc" => "Klucz bezpieczeństwa jest niepoprawny!"];

        $conn = Connection::getConnection();
        $stmt = $conn->prepare("SELECT admin_id, user_id FROM websites_auth_keys 
                         WHERE auth_key = ? 
                           AND website_id = ?
                           AND invalid = 0
                           AND logout = 0
                           ORDER BY id DESC
                           LIMIT 1");
        $stmt->bind_param("si",$authKey,$this->id);
        $stmt->execute();
//        raczej się nie wydarzy, ale na wszelki wypadek

        $result = $stmt->get_result();
        if($result->num_rows === 0)
            $resp = ["suc" => 0, "desc" => "Nie odnaleziono użytkownika powiązanego z podanym \"auth_key\"!"];
        else
        {
            $row = $result->fetch_row();
            if(!is_null($row[0]))
            {
                $permsResponse = [];
                foreach($perms as $perm)
                    $permsResponse[$perm] = 1;
                $resp = ["suc" => 1, "perms" => $permsResponse];
            }
            else
            {
                $getUserPermsQuery = $conn->query("SELECT perms FROM websites_users 
                                            WHERE website_id = $this->id 
                                              AND id = ${row[1]}");
                if($getUserPermsQuery->num_rows === 0)
                    return ["suc" => 0, "desc" => "Nie odnaleziono użytkownika! Czy został on usunięty?"];

                $userPerms = json_decode($getUserPermsQuery->fetch_row()[0],true);

                require_once("website/WebsitePermissions.php");
                $permsResponse = WebsitePermissions::hasPermissions($userPerms,$perms,$this->id);

                $resp = ["suc" => 1, "perms" => $permsResponse];
            }
        }

        $stmt->close();
        $conn->close();

        return $resp;
    }

    /**
     * Funkcja sprawdzająca, czy użytkownik posiada podane permisje (o podanym username, więc jest przeznaczona np. do
     * pokazywania danych (nie należy na nich polegać jak na zwykłych permisjach, szczególnie w API))
     * "suc" w returnie oznacza, czy nie ma żadnych błędów (0 - błąd; nie, czy user ma permisje!)
     * @param string $username
     * @param array $perms
     * @return array|int[]
     */
    public function hasPermissionsByUsername(string $username, array $perms): array
    {
//        powalone zapytanie, trzeba sprawdzić jego poprawnośc (jednak tu useless, choć może przydać się w hasPermissions,
//        aby zwiększyć wydajność kodu
//        $query = $conn->query("SELECT u.perms FROM websites_auth_keys k
//            LEFT JOIN websites_users u ON k.user_id = u.id
//            LEFT JOIN websites_admins a ON k.admin_id = a.id
//               WHERE (admin_id IS NOT NULL OR user_id IS NOT NULL)
//                 AND invalid = 0
//                 AND logout = 0
//            ORDER BY k.id
//            DESC LIMIT 1;");

        $conn = Connection::getConnection();
        $adminStmt = $conn->prepare("SELECT id FROM websites_admins 
          WHERE website_id = ? 
            AND BINARY username = ?");
        $adminStmt->bind_param("is", $this->id,$username);
        $adminStmt->execute();
        $adminResult = $adminStmt->get_result();
//        while ($row = $result->fetch_assoc()) {
//            var_dump($row);
//        }
        if($adminResult->num_rows === 1)
        {
            $permsResponse = [];
            foreach($perms as $perm)
                $permsResponse[$perm] = 1;
            $resp = ["suc" => 1, "username" => $username, "perms" => $permsResponse];
        }
        else
        {
            $userStmt = $conn->prepare("SELECT perms FROM websites_users 
                                        WHERE website_id = $this->id 
                                          AND BINARY username = ?
                                          AND deleted = 0");

            $userStmt->bind_param("s",$username);
            $userStmt->execute();

            $userResult = $userStmt->get_result();
            if($userResult->num_rows === 0)
                return ["suc" => 0, "username" => $username, "desc" => "Nie odnaleziono użytkownika o podanym loginie! Czy został on usunięty?"];

//            $userPerms = json_decode($getUserPermsQuery->fetch_row()[0],true);
            $userPerms = json_decode($userResult->fetch_row()[0],true);

            require_once("website/WebsitePermissions.php");
            $permsResponse = WebsitePermissions::hasPermissions($userPerms,$perms,$this->id);

            $resp = ["suc" => 1, "username" => $username, "perms" => $permsResponse];
        }

//        $adminResult->close();
//        $userStmt->close();
        $conn->close();

        return $resp;
    }

    /**
     * Funkcja sprawdzająca, czy użytkownik posiada ustawione podane permisje.
     * Ustawione oznacza, że liczy się KONKRETNA wartość. Rodzice permisji NIE SĄ brane pod uwagę.
     * UWAGA! Administrator ma zawsze ustawione wszystkie permisje (nawet, gdy nie ma :p).
     * "suc" w returnie oznacza, czy nie ma żadnych błędów (0 - błąd; nie, czy user ma permisje!)
     * @param string $remoteAddr
     * @param string $authKey
     * @param array $perms
     * @return array|int[]
     */
    public function issetPermissions(string $remoteAddr, string $authKey, array $perms): array
    {
//        Sprawdzenie, czy w ogóle istnieje wygenerowany auth_key dla tego IP
        require_once(dirname(__DIR__)."/objects/website/AuthKey.inc.php");
        if(!AuthKey::isValidAuthKeyForIp($this->id,$remoteAddr,$authKey))
            return ["suc" => 0, "desc" => "Klucz bezpieczeństwa jest niepoprawny!"];

        $conn = Connection::getConnection();
        $stmt = $conn->prepare("SELECT admin_id, user_id FROM websites_auth_keys 
                         WHERE auth_key = ? 
                           AND website_id = ?
                           AND invalid = 0
                           AND logout = 0
                           ORDER BY id DESC
                           LIMIT 1");
        $stmt->bind_param("si",$authKey,$this->id);
        $stmt->execute();

        $result = $stmt->get_result();
//        raczej się nie wydarzy, ale na wszelki wypadek
        if($result->num_rows === 0)
            $resp = ["suc" => 0, "desc" => "Nie odnaleziono użytkownika powiązanego z podanym \"auth_key\"!"];
        else
        {
            $row = $result->fetch_row();
            $permsResponse = [];
            if(!is_null($row[0]))
            {
//                Administrator ma zawsze ustawione permisje (nawet, gdy nie ma 😝)
                foreach($perms as $perm)
                    $permsResponse[$perm] = 1;
            }
            else
            {
                $getUserPermsQuery = $conn->query("SELECT perms FROM websites_users 
                                            WHERE website_id = $this->id 
                                              AND id = ${row[1]}");
                $userPerms = json_decode($getUserPermsQuery->fetch_row()[0],true);

                require_once("website/WebsitePermissions.php");

                foreach($perms as $perm)
                {
                    if(array_key_exists($perm, $userPerms))
                        $permsResponse[$perm] = 1;
                    else
                        $permsResponse[$perm] = 0;
                }

            }
            $resp = ["suc" => 1, "perms" => $permsResponse];
        }

        $stmt->close();
        $conn->close();

        return $resp;
    }


    function getLoginTries(string $ip): int
    {
        $conn = Connection::getConnection();

        date_default_timezone_set("Europe/Warsaw");
        $date = date("Y-m-d")."%";


        $stmt = $conn->prepare("SELECT date FROM websites_login_history 
            WHERE ip= ? 
              AND date LIKE ? 
              AND proper_data = 0
              AND website_id = ?");
        $stmt->bind_param("ssi",$ip, $date,$this->id);
        $stmt->execute();
        $result = $stmt->get_result();

        require_once("website/WebsiteSettings.php");
        $settings = new WebsiteSettings($this->id);

//    ilość dozwolonych prób do weryfikacji licensji na dzień (aktualnie 10)
        $res = $settings->getLoginAttempts() - $result->num_rows;
        $conn->close();
        return $res;
    }

//    function isBanned(string $ip): bool
//    {
//        if($this->getLoginTries($ip) <= 0) return true;
//
//        $conn = Connection::getConnection();
//        $result = $conn->query("SELECT * FROM check_license_banned_ips WHERE ip='$ip'");
//        $banned = $result->num_rows >= 1;
//
//        $conn->close();
//
//        if(!$banned) $banned = getTries($ip) <= 0;
//        return $banned;
//    }

    public function loginUser(string $ip, string $username, string $password): array
    {
        require_once(dirname(__DIR__)."/utils/SQLSecurity.php");
        $insecureCharsResponse = SQLSecurity::generateResponseForAPI(SQLSecurity::doesStringContains($username),"username");
        if(sizeof($insecureCharsResponse) !== 0)
            return $insecureCharsResponse;

        $loginTries = $this->getLoginTries($ip);
        if($loginTries <= 0)
            return ["suc" => 0, "desc" => "To IP zostało zablokowane!", "tries_left" => 0];

        $conn = Connection::getConnection();
        $stmtAdmins = $conn->prepare("SELECT id, nickname, password FROM websites_admins 
                    WHERE website_id = ? 
                      AND BINARY username = ?");
        $stmtAdmins->bind_param("is",$this->id,$username);
        $stmtAdmins->execute();
        $stmtAdminsResult = $stmtAdmins->get_result();
        $stmtAdmins->close();

        if($stmtAdminsResult->num_rows == 0)
        {
            $stmtUsers = $conn->prepare("SELECT id, nickname, password FROM websites_users 
                    WHERE website_id = ? 
                      AND BINARY username = ? 
                      AND disabled = 0
                      AND deleted = 0");
            $stmtUsers->bind_param("is",$this->id,$username);
            $stmtUsers->execute();
            $resultUsers = $stmtUsers->get_result();
            $stmtUsers->close();

            if($resultUsers->num_rows != 0)
            {
                $result = $this->loginUserGetResponse($resultUsers->fetch_assoc(),$ip,$password,false,$loginTries);
                if(!is_null($result["proper_data"]))
                    $this->logUserLogin($ip,$username,$password,$result["proper_data"],$result["suc"],$result["desc"]);
            }
            else
            {
                $result = ["suc" => 0, "desc" => "Niepoprawne dane logowania!", "tries_left" => $loginTries];
                $this->logUserLogin($ip,$username,$password,false,false,$result["desc"]);
            }
        }
        else
        {
            $result = $this->loginUserGetResponse($stmtAdminsResult->fetch_assoc(), $ip, $password, true,$loginTries);
            $this->logUserLogin($ip, $username, $password, $result["proper_data"], $result["suc"],$result["desc"]);
        }

        $conn->close();
        return $result;
    }

    public function logoutUser(string $ip, string $authKey): bool
    {
        require_once("website/AuthKey.inc.php");
        if(!AuthKey::isValidAuthKeyForIp($this->id, $ip, $authKey)) return false;

        $conn = Connection::getConnection();
        $now = date("Y-m-d H:i:s");
        $conn->query("UPDATE websites_auth_keys
                    SET 
                        expired_time = '$now', 
                        logout = 1
                    WHERE website_id = $this->id
                    AND auth_key = '$authKey'");
        $conn->close();
        return true;
    }

    private function internalLoginUserGetResponse(array $row, string $password): array
    {
        if(password_verify($password, $row["password"])) return ["suc" => 1, "proper_data" => 1];
        else return ["suc" => 0, "proper_data" => 0];
    }

    private function loginUserGetResponse($row,$ip,$password,$adminAccount, int $triesLeft): array
    {
        if(is_null($row["password"]))
            return ["suc" => 0, "proper_data" => null, "desc" => "Ten użytkownik posiada zresetowane hasło!"];
        if(password_verify($password, $row["password"]))
        {
            require_once(dirname(__DIR__)."/objects/website/AuthKey.inc.php");
            require_once(dirname(__DIR__)."/objects/website/PQCMSToken.inc.php");
            try {
                $authKey = AuthKey::generateAuthKey($this->id, $ip, $row["id"], $adminAccount);
                if(empty($authKey))
                    if($adminAccount)
                        return ["suc" => 0, "proper_data" => 1, "desc" => "Sesja tego konta jest już aktywna! (jeżeli uważasz, że ktoś ma dostęp do Twojego konta, jak najszybciej skontaktuj się z administratorem PQCMS!)", "tries_left" => $triesLeft];
                    else
                        return ["suc" => 0, "proper_data" => 1, "desc" => "Sesja tego konta jest już aktywna! (jeżeli uważasz, że ktoś ma dostęp do Twojego konta, jak najszybciej skontaktuj się z administratorem!)", "tries_left" => $triesLeft];
                $pqcmsToken = PQCMSToken::generateToken($authKey["id"]);
                unset($authKey["id"]);
            } catch (Exception) {
                return ["suc" => 0, "proper_data" => null, "desc" => "Blok zwrócił błąd! Skontaktuj się z administratorem PQCMS! (".__FILE__.": ".__LINE__.")", "tries_left" => $triesLeft];
            }
            if(is_null($authKey))
                return ["suc" => 0, "proper_data" => 1, "desc" => "Nie można odczytać pola \"login_session_time\"! Skontaktuj się z administratorem PQCMS!", "tries_left" => $triesLeft];
            else if(empty($authKey))
                if($adminAccount)
                    return ["suc" => 0, "proper_data" => 1, "desc" => "Sesja tego konta jest już aktywna! (jeżeli uważasz, że ktoś ma dostęp do Twojego konta, jak najszybciej skontaktuj się z administratorem PQCMS!)", "tries_left" => $triesLeft];
                else
                    return ["suc" => 0, "proper_data" => 1, "desc" => "Sesja tego konta jest już aktywna! (jeżeli uważasz, że ktoś ma dostęp do Twojego konta, jak najszybciej skontaktuj się z administratorem!)", "tries_left" => $triesLeft];
            return ["suc" => 1, "proper_data" => 1, "desc" => "Pomyślnie zalogowano!", "auth_key" => $authKey, "pqcms_token" => $pqcmsToken, "nickname" => $row["nickname"]];
        }
        else return ["suc" => 0, "proper_data" => 0, "desc" => "Niepoprawne dane logowania!", "tries_left" => $triesLeft];
    }

    private function logUserLogin(string $ip, string $username, string $password, ?bool $properData, bool $logged, string $description): void
    {
        $conn = Connection::getConnection();

//      Zamiana danych, aby pasowały do zapytania SQL
        $properData = $properData ? 1 : 0;
        $logged = $logged ? 1 : 0;
        $now = date("Y-m-d H:i:s");
        if($properData) $password = "";

        $stmt = $conn->prepare("INSERT INTO websites_login_history 
                    (website_id, ip, username, password, date, proper_data, logged, description) 
                    VALUES
                    (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssiis",$this->id,$ip,$username,$password,$now,$properData,$logged,$description);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }

    protected function getField($column): mixed
    {
        $conn = Connection::getConnection();
        $result = $conn->query("SELECT $column FROM websites WHERE id = $this->id");

        $fetchArray = mysqli_fetch_array($result);
        if($fetchArray == null || count($fetchArray) == 0) return null;
        $resp = $fetchArray[0];

        $conn->close();
        return $resp;
    }

    public function isProperSecureKey(string $remoteAddr, string $secureKey): bool
    {
        require_once("website/SecureKey.inc.php");
        return SecureKey::isValidSecureKey($this->id, $remoteAddr, $secureKey);
    }

    public function getSettings(): WebsiteSettings
    {
        require_once(__DIR__."/website/WebsiteSettings.php");
        return new WebsiteSettings($this->id);
    }

    public static function getWebsiteIDByMatchingDomain(string $value): ?int
    {
        $conn = Connection::getConnection();
        $stmt = $conn->prepare("SELECT id FROM websites WHERE domain = ?");
        $stmt->bind_param("s",$value);
        $stmt->execute();

        $result = $stmt->get_result();
        $fetchArray = mysqli_fetch_array($result);
        if($fetchArray == null || count($fetchArray) == 0) return null;
        $resp = $fetchArray[0];

        $conn->close();
        return $resp;
    }

    public static function getWebsitesIDArrayByMatchingDomain(string $domain): array
    {
        $conn = Connection::getConnection();
        $stmt = $conn->prepare("SELECT id FROM websites WHERE domain = ?");
        $stmt->bind_param("s",$domain);
        $stmt->execute();

        $result = $stmt->get_result();
        $websites = [];
        while($row = $result->fetch_row())
            $websites[] = $row[0];

        $conn->close();
        return $websites;
    }

    public static function addWebsite(string $domain, string $login, string $license_key, ?string $license_expiration, bool $blocked): void
    {
        $conn = Connection::getConnection();

        $stmtWebsites = $conn->prepare("INSERT INTO websites VALUES (null,?,?,?,?,?)");
        $stmtWebsites->bind_param("ssssi",$domain,$login,$license_key,$license_expiration,$blocked);
        $stmtWebsites->execute();
        $websiteId = $stmtWebsites->insert_id;
        $stmtWebsites->close();

        $sql = "INSERT INTO websites_settings (website_id) VALUES ($websiteId)";
        $conn->query($sql);

        $conn->close();
    }
}