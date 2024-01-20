<?php

require_once("WebsiteAdmin.inc.php");
class WebsiteUser
{
    private int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getWebsiteId(): int
    {
        return $this->unsafe_getField("website_id");
    }

    public function getUsername(): string
    {
        return $this->unsafe_getField("username");
    }

    public function getNickname(): string
    {
        return $this->unsafe_getField("nickname");
    }

    public function getPerms(): array
    {
        return $this->unsafe_getField("perms");
    }

    public function isDisabled(): bool
    {
        return $this->unsafe_getField("disabled");
    }

    public function doesPasswordMatch($password): bool
    {
        return password_verify($password,$this->unsafe_getField("password"));
    }

    private function unsafe_getField($column): mixed
    {
        $conn = Connection::getConnection();
        $query = $conn->query("SELECT $column FROM websites_users WHERE id = $this->id");

        $fetchArray = mysqli_fetch_array($query);
        if($fetchArray == null || count($fetchArray) == 0) return null;
        $result = $fetchArray[0];

        $query->close();
        $conn->close();
        return $result;
    }

    public static function validateUsername($username): array
    {
        require_once(dirname(__DIR__,2)."/api/utils/validators/Validator.inc.php");
        if(Validator::validate($username,["s(5-30)"])["suc"] === 0)
            return ["suc" => 0, "desc" => "Login musi być napisem o długości 5-30 znaków!"];

        $betterUsername = preg_replace('/\s+/', '', $username);
        return ($betterUsername === $username) ? ["suc" => 1] : ["suc" => 0, "Login nie może posiadać białych znaków (np. spacji)!"];
    }

    public static function validateNickname($nickname): array
    {
        require_once(dirname(__DIR__,2)."/api/utils/validators/Validator.inc.php");
        return Validator::validate($nickname,["s(2-30)"])["suc"] ? ["suc" => 1] : ["suc" => 0, "desc" => "Nazwa użytkownika musi być napisem o długości 2-30 znaków!"];
    }

    public static function validatePassword($password): array
    {
        require_once(dirname(__DIR__,2)."/api/utils/validators/Validator.inc.php");
        $betterPassword = preg_replace('/\s+/', '', $password);
        if($betterPassword !== $password)
            return ["suc" => 0, "desc" => "Hasło nie może posiadać białych znaków (np. spacji)!"];
        return Validator::validate($password,["s(8-50)"])["suc"] ? ["suc" => 1] : ["suc" => 0, "desc" => "Hasło musi być napisem o długości 8-50 znaków!"];
    }

    public static function validateParamsForUser(string $username, string $nickname, string $password, array $perms): array
    {
        $betterUsername = preg_replace('/\s+/', '', $username);
        if($betterUsername !== $username)
            return ["suc" => 0, "desc" => "Nazwa użytkownika nie może posiadać białych znaków (np. spacji)!"];

        $betterNickname = trim($nickname);


        if(strlen($betterUsername) < 5 || strlen($betterUsername) > 30)
            return ["suc" => 0, "desc" => "Login musi mieć od 5 do 30 znaków!"];
        if(strlen($betterNickname) < 2 || strlen($betterNickname) > 30)
            return ["suc" => 0, "desc" => "Nazwa użytkownika musi mieć od 2 do 30 znaków!"];
        if(strlen($betterPassword) < 8 || strlen($betterPassword) > 50)
            return ["suc" => 0, "desc" => "Hasło musi mieć od 8 do 30 znaków!"];
        require_once "WebsitePermissions.php";
        if(!WebsitePermissions::isProperPermsArray($perms))
            return ["suc" => 0, "desc" => "Podano niepoprawne permisje!"];
        return ["suc" => 1];
    }

    public static function addUser(int $websiteId, string $username, string $nickname, ?string $email, string $password, array $perms = [], bool $disabled = false): array
    {
//        Walidacja długości
        $paramsValidator = self::validateParamsForUser($username, $nickname, $password, $perms);
        if($paramsValidator["suc"] == 0)
            return $paramsValidator;

//        Walidacja - anti SQL injection
        require_once(dirname(__DIR__,2)."/utils/SQLSecurity.php");
        $insecureCharsResponse = SQLSecurity::generateResponseForAPI(SQLSecurity::doesStringContains($username),"username");
        if(sizeof($insecureCharsResponse) !== 0)
            return $insecureCharsResponse;
        $insecureCharsResponse = SQLSecurity::generateResponseForAPI(SQLSecurity::doesStringContains($nickname),"nickname");
        if(sizeof($insecureCharsResponse) !== 0)
            return $insecureCharsResponse;

        $conn = Connection::getConnection();

        $query = $conn->query("SELECT username FROM websites_admins WHERE website_id = $websiteId");
        if($query->fetch_row()[0] !== $username)
        {
            $query = $conn->query("SELECT id FROM websites_users WHERE website_id = $websiteId AND username = '$username' AND deleted = 0");
            if($query->num_rows == 0)
            {
                $disabled = (int) $disabled;
                if(is_null($perms))
                    $perms = [];
                $perms = json_encode($perms);

                $password = password_hash($password,PASSWORD_DEFAULT);

                if(!empty($email))
                {
                    $valuesSQL1 = "(website_id, username, nickname, email, password, perms, disabled, deleted)";
                    $valuesSQL2 = "(?,?,?,?,?,?,?,false)";
                    $prepareBindsTypes = "isssssi";
                    $prepareBinds = [$websiteId, $username, $nickname, $email, $password, $perms, $disabled];
//                    $valuesSQL2 = "(?,'?','?','?','?','?',?,false)";
//                    $valuesSQL2 = "(null,$websiteId,'$username','$nickname','$email','$password','$perms',$disabled,false)";
                }
                else
                {
                    $valuesSQL1 = "(website_id, username, nickname, password, perms, disabled, deleted)";
                    $valuesSQL2 = "(?,?,?,?,?,?,false)";
                    $prepareBindsTypes = "issssi";
                    $prepareBinds = [$websiteId, $username, $nickname, $password, $perms, $disabled];
                }

                $insertStmt = $conn->prepare("INSERT INTO websites_users $valuesSQL1 VALUES $valuesSQL2");
                $insertStmt->bind_param($prepareBindsTypes,...$prepareBinds);
                $insertStmt->execute();

                if($conn->errno === 0) $resp = ["suc" => 1, "desc" => "Dodano użytkownika!"];
                else $resp = ["suc" => 0, "desc" => "Błąd podczas dodawania użytkownika. Kod błędu: ".($conn->errno)."!"];
                $conn->close();
            }
            else $resp = ["suc" => 0, "desc" => "Już istnieje użytkownik o takim loginie!"];
        }
        else $resp = ["suc" => 0, "desc" => "Już istnieje użytkownik o takim loginie (administrator)!"];

        return $resp;
    }

    public static function editUser(int $websiteId, string $username, string $nickname, string $password, array $perms = [], bool $disabled = false): array
    {
//        Walidacja pól
        $paramsValidator = self::validateParamsForUser($username, $nickname, $password, $perms);
        if($paramsValidator["suc"] == 0)
            return $paramsValidator;


//        Walidacja - anti SQL injection
        require_once(dirname(__DIR__,2)."/utils/SQLSecurity.php");
        $insecureCharsResponse = SQLSecurity::generateResponseForAPI(SQLSecurity::doesStringContains($username),"username");
        if(sizeof($insecureCharsResponse) !== 0)
            return $insecureCharsResponse;
        $insecureCharsResponse = SQLSecurity::generateResponseForAPI(SQLSecurity::doesStringContains($nickname),"nickname");
        if(sizeof($insecureCharsResponse) !== 0)
            return $insecureCharsResponse;

        $conn = Connection::getConnection();

        $query = $conn->query("SELECT username FROM websites_admins WHERE website_id = $websiteId");
        if($query->fetch_row()[0] !== $username)
        {
            $query = $conn->query("SELECT id FROM websites_users WHERE website_id = $websiteId AND username = '$username'");
            if($query->num_rows == 0)
            {
                $disabled = (int) $disabled;
                if(is_null($perms))
                    $perms = [];
                $perms = json_encode($perms);

                $password = password_hash($password,PASSWORD_DEFAULT);

                $conn->query("INSERT INTO websites_users VALUES (null,$websiteId,'$username','$nickname','$password','$perms',$disabled)");
                if($conn->errno === 0) $resp = ["suc" => 1, "desc" => "Dodano użytkownika!"];
                else $resp = ["suc" => 0, "desc" => "Błąd podczas dodawania użytkownika. Kod błędu: ".($conn->errno)."!"];
                $conn->close();
            }
            else $resp = ["suc" => 0, "desc" => "Już istnieje użytkownik o takim loginie!"];
        }
        else $resp = ["suc" => 0, "desc" => "Już istnieje użytkownik o takim loginie (administrator)!"];

        return $resp;
    }

    public static function deleteUser(int $websiteId, string $username): void
    {
        $conn = Connection::getConnection();
        $stmt = $conn->prepare("UPDATE websites_users 
            SET deleted = 1 
            WHERE website_id = ? 
              AND username = ?");
        $stmt->bind_param("is",$websiteId,$username);
        $stmt->execute();

        $stmt->close();
        $conn->close();
    }

    public static function getWebsiteUserByUsername(string $username, int $websiteId): ?array
    {
        $conn = Connection::getConnection();

        if(is_string($value)) $value = "'".$value."'";
        $sql = "SELECT id FROM websites_users WHERE $col = $value";
        if($websiteId != null) $sql .= " AND website_id = $websiteId";

        $query = $conn->query($sql);
        if($query->num_rows >= 1)
        {
            $result = [];
            foreach($query->fetch_row() as $row)
                $result[] = $row[0];
        }
        else $result = null;

        $query->close();
        $conn->close();
        return $result;
    }
}