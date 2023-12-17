<?php

require_once(dirname(__DIR__)."/database/Connection.inc.php");

class Website
{
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

        if($licenseExpiration == null)
            return false;

        $licenseTime = strtotime($licenseExpiration);

        date_default_timezone_set('Europe/Warsaw');
        $nowTime = strtotime(date("Y-m-d H:i:s"));

        return $nowTime >= $licenseTime;
//        if($nowTime >= $licenseTime)
//            return true;
//        return false;
    }

    public function doesExists(): bool
    {
        return !is_null($this->getField("id"));
    }

    public function generateSecureKey(): string
    {
        require_once("website/SecureKey.inc.php");
        return SecureKey::generateSecureKey($this->id);
    }

    /**
     * @param string $secure_key klucz bezpieczeństwa
     * @param string $apiName nazwa API (dla nie-APIkowych plików po prostu pusty napis)
     */
    public function invalidateSecureKey(string $secure_key, string $apiName): void
    {
        require_once("website/SecureKey.inc.php");
        SecureKey::invalidateSecureKey($this->id,$secure_key,$apiName);
    }

//    Gettery, settery opierające się na obiekcie, wymagające bardziej złożonych operacji
//    (działają na kilku tabelach, nie tylko na "websites"

    public function getAdminId(): ?int
    {
        $conn = Connection::getConnection();
        $query = $conn->query("SELECT id FROM websites_admins WHERE website_id = $this->id");

        if($query->num_rows == 0)
            $result = null;
        else
            $result = $query->fetch_row()[0];

        $query->close();
        $conn->close();
        return $result;
    }

    public function addAdmin(string $username, string $nickname, string $password): int
    {
        require_once(dirname(__DIR__)."/objects/website/WebsiteAdmin.inc.php");
        return WebsiteAdmin::unsafe_addWebsiteAdmin($this->id,$username,$nickname,$password);
    }

    public function addUser(string $username, string $nickname, string $password, array $perms, bool $disabled): array
    {
        if(sizeof($this->getUsersIds()) >= 20)
            return ["suc" => 0, "desc" => "Strona osiągnęła limit użytkowników (20)!"];
        require_once(dirname(__DIR__)."/objects/website/WebsiteUser.inc.php");
        return WebsiteUser::unsafe_addWebsiteUser($this->id, $username, $nickname, $password, $perms, $disabled);
    }

    public function getUsersIds(): array
    {
        $conn = Connection::getConnection();
        $query = $conn->query("SELECT id FROM websites_users WHERE website_id = $this->id");

        $result = [];
        foreach($query->fetch_row() as $row)
            $result[] = $row[0];

        $query->close();
        $conn->close();
        return $result;
    }

    public function getUsers(): array
    {
        $conn = Connection::getConnection();
        $query = $conn->query("SELECT username, nickname, perms, disabled FROM websites_users WHERE website_id = $this->id");

        $result = [];
        while($row = $query->fetch_row())
            $result[] = ["username" => $row[0], "nickname" => $row[1], "perms" => json_decode($row[2]), "disabled" => $row[3]];

        $query = $conn->query("SELECT username, nickname FROM websites_admins WHERE website_id = $this->id");
        if($row = $query->fetch_row())
            $result[] = ["username" => $row[0], "nickname" => $row[1]];

        $query->close();
        $conn->close();
        return $result;
    }

    public function hasPermission(string $remoteAddr, string $authKey): array
    {
        require_once(dirname(__DIR__)."/objects/website/AuthKey.inc.php");
        if(!AuthKey::isValidAuthKeyByIp($this->id,$remoteAddr,$authKey))
            return ["suc" => 0, "desc" => "Klucz bezpieczeństwa jest niepoprawny!"];

        $conn = Connection::getConnection();
        $query = $conn->query("SELECT admin_id, user_id FROM websites_auth_keys 
                         WHERE auth_key = '$authKey' 
                           AND website_id = $this->id");
//        raczej się nie wydarzy, ale na wszelki wypadek
        if($query->num_rows === 0)
            $resp = ["suc" => 0, "desc" => "Klucz bezpieczeństwa jest niepoprawny!"];
        else
        {
            $row = $query->fetch_row();
            if(!is_null($row[0]))
                $resp = ["suc" => 1];
            else
//                jakieś sprawdzenie fajne permisji, na razie user nie ma do niczego dostępu, admin ma do wszystkiego
                $resp = ["suc" => 0, "desc" => "Nie masz permisji!"];
        }

        $query->close();
        $conn->close();
        return $resp;
    }

    /**
     * Funkcja do wewnętrznego logowania użytkownika (używana do logowania na serwerach PQCMS). Nie działa na sesjach
     * auth_key, przez co jest jedynie dozwolone na wspomnianych wcześnej serwerach PQCMS.
     * @param string $ip ip
     * @param string $username nazwa użytkownika
     * @param string $password hasło
     * @return array response
     */
    public function internalLoginUser(string $ip, string $username, string $password): array
    {
        $conn = Connection::getConnection();
        $query = $conn->query("SELECT id, password FROM websites_admins 
                    WHERE website_id = $this->id 
                      AND username = '$username'");
        if($query->num_rows == 0)
        {
            $query = $conn->query("SELECT id, password FROM websites_users 
                    WHERE website_id = $this->id 
                      AND username = '$username' 
                      AND disabled = 0");

            if($query->num_rows != 0)
            {
                $row = $query->fetch_assoc();
                $result = $this->internalLoginUserGetResponse($row,$password);
                if($result["suc"] == 1)
                {
                    $result["admin"] = 0;
                    $result["id"] = (int) $row["id"];
                }
                $this->logUserLogin($ip,$username,$password,$result["proper_data"],$result["suc"]);
            }
            else
            {
                $result = ["suc" => 0, "desc" => "Niepoprawne dane logowania."];
                $this->logUserLogin($ip,$username,$password,false,false);
            }
        }
        else
        {
            $row = $query->fetch_assoc();
            $result = $this->internalLoginUserGetResponse($row, $password);
            if($result["suc"] == 1)
            {
                $result["admin"] = 1;
                $result["id"] = (int) $row["id"];
            }
            $this->logUserLogin($ip, $username, $password, $result["proper_data"], $result["suc"]);
        }

        $query->close();
        $conn->close();

        return $result;
    }

    public function loginUser(string $ip, string $username, string $password): array
    {
        $conn = Connection::getConnection();
        $query = $conn->query("SELECT id, password FROM websites_admins 
                    WHERE website_id = $this->id 
                      AND username = '$username'");
        if($query->num_rows == 0)
        {
            $query = $conn->query("SELECT id, password FROM websites_users 
                    WHERE website_id = $this->id 
                      AND username = '$username' 
                      AND disabled = 0");

            if($query->num_rows != 0)
            {
                $result = $this->loginUserGetResponse($query->fetch_assoc(),$ip,$password,false);
                $this->logUserLogin($ip,$username,$password,$result["proper_data"],$result["suc"]);
            }
            else
            {
                $result = ["suc" => 0, "desc" => "Niepoprawne dane logowania."];
                $this->logUserLogin($ip,$username,$password,false,false);
            }
        }
        else
        {
            $result = $this->loginUserGetResponse($query->fetch_assoc(), $ip, $password, true);
            $this->logUserLogin($ip, $username, $password, $result["proper_data"], $result["suc"]);
        }

        $query->close();
        $conn->close();
        return $result;
    }

    public function logoutUser(string $ip, string $authKey): bool
    {
        require_once("website/AuthKey.inc.php");
        if(!AuthKey::isValidAuthKeyByIp($this->id, $ip, $authKey)) return false;

        $conn = Connection::getConnection();
        $now = date("Y-m-d H:i:s");
        $conn->query("UPDATE websites_auth_keys
                    SET expired_time = '$now', 
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

    private function loginUserGetResponse($row,$ip,$password,$adminAccount): array
    {
        if(password_verify($password, $row["password"]))
        {
            require_once(dirname(__DIR__)."/objects/website/AuthKey.inc.php");
            $authKey = AuthKey::generateAuthKey($this->id, $ip, $row["id"], $adminAccount);
            if($authKey === "") return ["suc" => 0, "proper_data" => 1, "desc" => "Sesja tego konta jest już aktywna! (jeżeli uważasz, że to błąd, jak najszybciej skontaktuj się z administratorem!)", "auth_key" => ""];
            return ["suc" => 1, "proper_data" => 1, "desc" => "Pomyślnie zalogowano!", "auth_key" => $authKey];
        }
        else return ["suc" => 0, "proper_data" => 0, "desc" => "Niepoprawne dane logowania."];
    }

    private function logUserLogin(string $ip, string $username, string $password, bool $properData, bool $logged): void
    {
        $conn = Connection::getConnection();

//      Zamiana danych, aby pasowały do zapytania SQL
        $properData = $properData ? 1 : 0;
        $logged = $logged ? 1 : 0;
        $now = date("Y-m-d H:i:s");
        if($properData) $password = "";

        $conn->query("INSERT INTO websites_login_history 
                    (website_id, ip, username, password, date, proper_data, logged) 
                    VALUES
                    ($this->id, '$ip', '$username', '$password', '$now', $properData, $logged)");
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

    public function isProperSecureKey(string $secureKey): bool
    {
        $conn = Connection::getConnection();
        $result = $conn->query("SELECT secure_key FROM websites_secure_keys 
                  WHERE website_id = $this->id 
                    AND used_time IS NULL");

        $isProper = false;
        while($row = mysqli_fetch_row($result))
        {
            if($row[0] == $secureKey)
            {
                $isProper = true;
                break;
            }
        }

        $conn->close();
        return $isProper;
    }

    public function getSettings(): WebsiteSettings
    {
        require_once(__DIR__."/website/WebsiteSettings.php");
        return new WebsiteSettings($this->id);
    }

    public static function getWebsiteIDByMatching($column,$value): ?int
    {
//        TODO unsafe
        $conn = Connection::getConnection();
        $result = $conn->query("SELECT id FROM websites WHERE $column = '$value'");

        $fetchArray = mysqli_fetch_array($result);
        if($fetchArray == null || count($fetchArray) == 0) return null;
        $resp = $fetchArray[0];

        $conn->close();
        return $resp;
    }

    public static function addWebsite(string $domain, string $login, string $license_key, ?string $license_expiration, bool $blocked): void
    {
        $conn = Connection::getConnection();

        // Data transformation for SQL
        if ($blocked) $blocked = 1; else $blocked = 0;
        if ($license_expiration != null) $license_expiration = "'$license_expiration'";
        else $license_expiration = 'null';

        $sql = "INSERT INTO websites VALUES (null,'$domain','$login','$license_key',$license_expiration,$blocked)";
        $conn->query($sql);

        $websiteId = mysqli_insert_id($conn);
        $sql = "INSERT INTO websites_settings (website_id) VALUES ($websiteId)";
        $conn->query($sql);
        $conn->close();
    }
}