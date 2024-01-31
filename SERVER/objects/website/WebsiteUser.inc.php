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

    public function isDeleted(): bool
    {
        return $this->unsafe_getField("deleted");
    }

    public function doesPasswordMatch($password): bool
    {
        return password_verify($password,$this->unsafe_getField("password"));
    }

    public function setPassword(string $password): bool
    {
        if(strlen($password) < 8 || strlen($password) > 40)
            return false;

        $conn = Connection::getConnection();

        $password = password_hash($password,PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE websites_users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $password,$this->id);
        $stmt->execute();
        return $stmt->errno === 0;
    }

    private function unsafe_getField($column): mixed
    {
        $conn = Connection::getConnection();
        $query = $conn->query("SELECT $column FROM websites_users WHERE id = $this->id");

        $fetchArray = $query->fetch_row();
        if($fetchArray == null || count($fetchArray) == 0) return null;
        $result = $fetchArray[0];

        $query->close();
        $conn->close();
        return $result;
    }

    public static function validateUsername(/*string*/$username): array
    {
        require_once(dirname(__DIR__,2)."/api/utils/validators/Validator.inc.php");
        if(Validator::validate([$username],["s(5-30)"])["suc"] === 0)
            return ["suc" => 0, "desc" => "Login musi być napisem o długości 5-30 znaków!"];

        return (preg_match("/^[a-z]+$/", $username) == 1) ? ["suc" => 1] : ["suc" => 0, "desc" => "Login może zawierać tylko małe litery!"];
    }

    public static function validateNickname(/*string*/$nickname): array
    {
        require_once(dirname(__DIR__,2)."/api/utils/validators/Validator.inc.php");
        return Validator::validate([$nickname],["s(2-30)"])["suc"] ? ["suc" => 1] : ["suc" => 0, "desc" => "Nazwa użytkownika musi być napisem o długości 2-30 znaków!"];
    }

    public static function validateEmail(/*string*/$email): array
    {
        require_once(dirname(__DIR__,2)."/api/utils/validators/Validator.inc.php");
        if(Validator::validate([$email],["s(5-255)"])["suc"] === 0)
            return ["suc" => 0, "desc" => "Email musi być napisem o długości 5-255 znaków!"];
        return filter_var($email,FILTER_VALIDATE_EMAIL) ? ["suc" => 1] : ["suc" => 0, "desc" => "Podany e-mail jest niepoprawny!"];
    }

    public static function validatePassword(/*string*/$password): array
    {
        require_once(dirname(__DIR__,2)."/api/utils/validators/Validator.inc.php");
        $betterPassword = preg_replace('/\s+/', '', $password);
        if($betterPassword !== $password)
            return ["suc" => 0, "desc" => "Hasło nie może posiadać białych znaków (np. spacji)!"];
        return Validator::validate([$password],["s(8-50)"])["suc"] ? ["suc" => 1] : ["suc" => 0, "desc" => "Hasło musi być napisem o długości 8-50 znaków!"];
    }

    public static function validateParamsForUser(string $username, string $nickname, string $password, array $perms): array
    {
        $betterUsername = preg_replace('/\s+/', '', $username);
        if($betterUsername !== $username)
            return ["suc" => 0, "desc" => "Nazwa użytkownika nie może posiadać białych znaków (np. spacji)!"];

        $betterNickname = trim($nickname);


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

    public static function resetPassword(int $websiteId, string $username): array
    {
        $conn = Connection::getConnection();

        $selectStmt = $conn->prepare("SELECT id, nickname, email from websites_users WHERE website_id = ? AND username = ?");
        $selectStmt->bind_param("is",$websiteId,$username);
        $selectStmt->execute();
        $resultSelect = $selectStmt->get_result();
        if($resultSelect->num_rows === 0)
            return ["suc" => 0, "desc" => "Nie znaleziono użytkownika o podanym loginie!"];

        $user = $resultSelect->fetch_assoc();
        $resultSelect->close();

        if(empty($user["email"]))
            return ["suc" => 0, "desc" => "Nie można zresetować hasła, ponieważ użytkownik nie ma przypisanego adresu e-mail!"];

        $stmt = $conn->prepare("UPDATE websites_users SET password = NULL WHERE id = ?");
        $stmt->bind_param("s",$user["id"]);
        $stmt->execute();

        require_once "ResetPasswordToken.php";
        $token = ResetPasswordToken::generateToken($user["id"]);

        $link = "http://localhost/pqcms/server/client/account/ResetPassword.php?token=$token";

        require_once(dirname(__DIR__)."/Mailer.php");
        Mailer::sendMail($user["email"],'PQCMS - Twoje hasło zostało zresetowane!',
<<<HTML
<!DOCTYPE html>
<html lang="pl">
    <head>
        <style>
            .body {
                background: linear-gradient(135deg, #8a2be2, #793ee6 60%, #00bfff 120%);
                background-size: 400% 400%;
                color: white;
            }
        </style>
    </head>
    <body>
        <div class="body">
            <p>
                Administrator zresetował Twoje hasło. Kliknij na link, aby utworzyć nowe:
            </p>
            <a href="${link}">${link}</a>
            <p style="font-weight: bold">   
                Link straci ważność za 30 minut
            </p>
            <p>
                Wyświetlana nazwa konta: ${user["nickname"]} (ze względów bezpieczeństwa nie jest pokazywany login)
            </p>
            <p style="font-weight: bold; font-size: 25px;">
                WAŻNA INFORMACJA! Po kliknięciu na link upewnij się, że jesteś na oficjalnej stronie PQCMS!
            </p>
        </div>
    </body>
</html>
HTML,
            "Administrator zresetował Twoje hasło. Otwórz link, aby ustawić nowe: $link");

        return ["suc" => 1, "desc" => "Zresetowano hasło użytkownikowi!"];
    }



    public static function editUser(int $websiteId, string $username = null, ?string $nickname = null, ?string $email = null, ?string $password = null, ?array $perms = null, ?bool $disabled = false): array
    {
//        Walidacja pól
        if(!is_null($username))
        {
            $validator = self::validateUsername($username);
            if($validator["suc"] === 0)
                return $validator;
        }
        if(!is_null($nickname))
        {
            $validator = self::validateNickname($nickname);
            if($validator["suc"] === 0)
                return $validator;
        }
        if(!is_null($email))
        {
            $validator = self::validateEmail($email);
            if($validator["suc"] === 0)
                return $validator;
        }
        if(!is_null($password))
        {
            $validator = self::validatePassword($password);
            if($validator["suc"] === 0)
                return $validator;
        }
        if(!is_null($perms))
        {
            require_once "WebsitePermissions.php";
            if(!WebsitePermissions::isProperPermsArray($perms))
                return ["suc" => 0, "desc" => "Podano niepoprawne permisje!"];
        }

        $conn = Connection::getConnection();
        $query = $conn->query("SELECT username FROM websites_admins WHERE website_id = $websiteId");
        if($query->fetch_row()[0] !== $username)
        {
            $stmtUsers = $conn->prepare("SELECT id FROM websites_users WHERE website_id = $websiteId AND username = ?");
            $stmtUsers->bind_param("s",$username);
            $stmtUsers->execute();

            $stmtUsersResult = $stmtUsers->get_result();

            if($stmtUsersResult->num_rows == 0)
                $resp = ["suc" => 0, "desc" => "Już istnieje użytkownik o takim loginie!"];
            else
            {
                $userId = $stmtUsersResult->fetch_row()[0];

                if(!is_null($perms))
                    $perms = json_encode($perms);


                $sql = [];
                $params = [];
                $paramsTypes = "";

                if(!is_null($nickname))
                {
                    $sql[] = " nickname = ?";
                    $params[] = $nickname;
                    $paramsTypes .= "s";
                }
                if(!is_null($email))
                {
                    $sql[] = " email = ?";
                    $params[] = $email;
                    $paramsTypes .= "s";
                }
                if(!is_null($password))
                {
                    $sql[] = " password = ?";
                    $params[] = password_hash($password,PASSWORD_DEFAULT);
                    $paramsTypes .= "s";
                }
                if(!is_null($perms))
                {
                    $sql[] = " perms = ?";
                    $params[] = $perms;
                    $paramsTypes .= "s";
                }
                if(!is_null($disabled))
                {
                    $sql[] = " disabled = ?";
                    $params[] = (int) $disabled;
                    $paramsTypes .= "i";
//                    $params[] = (int) $disabled;
                }

                $updateSQLQuery = "UPDATE websites_users SET ".implode(',',$sql)." WHERE id = $userId";

//                var_dump($updateSQLQuery);
//                var_dump($params);

                $updateStmt = $conn->prepare($updateSQLQuery);
//                "issssi",$websiteId,$username,$nickname,$passwordm,$perms,$disabled
                $updateStmt->bind_param($paramsTypes,...$params);
                $updateStmt->execute();

                if($conn->errno === 0) $resp = ["suc" => 1, "desc" => "Edycja użytkownika powiodła się!"];
                else $resp = ["suc" => 0, "desc" => "Błąd podczas edytowania użytkownika. Kod błędu: ".($conn->errno)."!"];
                $conn->close();
            }
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

        $stmt = $conn->prepare("SELECT id FROM websites_users 
          WHERE BINARY username = ? 
            AND website_id = ? 
            AND deleted = 0");
        $stmt->bind_param("si",$username,$websiteId);
        $stmt->execute();


        if($stmt->errno !== 0)
            return null;

        $result = $stmt->get_result();
        if($result->num_rows >= 1)
        {
            $response = [];
            while($row = $result->fetch_row())
                $response[] = $row[0];
        }
        else $response = null;

        $stmt->close();
        $conn->close();
        return $response;
    }
}