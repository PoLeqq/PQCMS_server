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

    public static function addUser(int $websiteId, string $username, string $nickname, string $password, array $perms = [], bool $disabled = false): array
    {
//        Walidacja długości
        if(strlen($username) < 5 || strlen($username) > 30)
            return ["suc" => 0, "desc" => "Login musi mieć od 5 do 30 znaków!"];
        if(strlen($nickname) < 2 || strlen($nickname) > 30)
            return ["suc" => 0, "desc" => "Nazwa użytkownika musi mieć od 2 do 30 znaków!"];
        if(strlen($password) < 8 || strlen($password) > 50)
            return ["suc" => 0, "desc" => "Hasło musi mieć od 8 do 30 znaków!"];
        require_once "WebsitePermissions.php";
        if(!WebsitePermissions::isProperPermsArray($perms))
            return ["suc" => 0, "desc" => "Podano niepoprawne permisje!"];

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

    public static function unsafe_getWebsiteUserBy(string $col, mixed $value, int $websiteId = null): ?array
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