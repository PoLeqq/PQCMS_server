<?php

// NAPISZE TUTAJ, BO JUŻ RAZ DOSTAŁEM MINDFUCKA...
// zwraca wszystko true/false:
// valid - czy poprawne (inny klucz, niż obecny || (outdated || validated) == 1)
// outdated - czy przestarzały
// invalidated - czy admin (klient/pqcms) zamknął sesję

require_once(dirname(__DIR__,2)."/database/Connection.inc.php");
class AuthKey
{
    /**
     * @param int $website_id id strony
     * @param int $userId id użytkownika (lub administratora)
     * @param bool $admin czy jest administratorem
     * @return ?array auth_key (null w przypadku, gdy to konto posiada już aktywną sesję (auth_key))
     * pusty array, gdy wystąpił błąd podczas pobierania ustawienia login_session_time
     * @throws Exception raczej nigdy lol
     */
    public static function generateAuthKey(int $website_id, string $ip, int $userId, bool $admin): ?array
    {
        if(AuthKey::isAnyAuthKey($website_id,$userId,$admin))
            return [];

        $conn = Connection::getConnection();

        do {
            $authKey = bin2hex(random_bytes(64));
//            todo można przerobić sql niżej do count(), tak samo jak auth key (zwiększenie wydajności)
        } while($conn->query("SELECT id FROM websites_auth_keys 
                            WHERE website_id = $website_id 
                            AND auth_key = '$authKey'")->num_rows > 0);

        if($admin) $column = "admin_id";
        else $column = "user_id";

        $loginSessionTimeResult = $conn->query("SELECT login_session_time FROM websites_settings WHERE website_id = $website_id");
        if($row = $loginSessionTimeResult->fetch_row())
            $sessionTime = $row[0];
        else
            return null;

        $currentDate = date("Y-m-d H:i:s", time()+$sessionTime);
        $conn->query("INSERT INTO websites_auth_keys 
                    (website_id, $column, ip, auth_key, expired_time) 
                    VALUES 
                    ($website_id, $userId, '$ip', '$authKey', '$currentDate')");
        $conn->close();

        return ["value" => $authKey, "expiry_time" => time()+$sessionTime];
    }

//    TODO
//    public static function extendExpireTime(int $websiteId, int $userId, bool $admin): bool
//    {
//
//    }

    public static function isValidAuthKey(int $websiteId, int $userId, bool $admin, string $authKey): bool
    {
        if(!AuthKey::isAnyAuthKey($websiteId,$userId,$admin))
            return false;

        $conn = Connection::getConnection();
        if($admin) $column = "admin_id";
        else $column = "user_id";
        $query = $conn->query("SELECT expired_time, auth_key FROM websites_auth_keys 
                              WHERE website_id = $websiteId 
                              AND $column = $userId
                              AND logout = 0 
                              LIMIT 1");

        if($query->num_rows == 0)
            $result = false;
        else
        {
            $row = $query->fetch_row();
            $expiredTime = $row[0];
            $expiredDate = date("Y-m-d H:i:s", $expiredTime);
            $currentDate = date("Y-m-d H:i:s", time());

            if($expiredDate < $currentDate) $result = false;
            else $result = $row[1] === $authKey;
        }

        $query->close();
        $conn->close();
        return $result;
    }

    public static function isProperAuthKey(string $authKey): bool
    {
        return (bool) preg_match('/^[0-9a-f]{128}$/', $authKey);
    }

    public static function isValidAuthKeyForIp(int $websiteId, string $ip, string $authKey): array
    {
//        Sprawdzenie, czy klucz ma wartości tylko 0-9,a-f
        if(!self::isProperAuthKey($authKey))
            return["valid" => 0, "outdated" => 0, "invalidated" => 0, "not_secure" => 1];
        $conn = Connection::getConnection();
        $query = $conn->query("SELECT expired_time, invalid, logout FROM websites_auth_keys 
                              WHERE website_id = $websiteId
                              AND ip = '$ip'
                              AND auth_key = '$authKey'
                              ORDER BY id DESC
                              LIMIT 1");

        if($query->num_rows == 0)
            $result = ["valid" => 0, "outdated" => 0, "invalidated" => 0];
        else
        {
            $row = $query->fetch_row();

            $expiredTime = $row[0];
            $expiredDate = strtotime($expiredTime);

            date_default_timezone_set('Europe/Warsaw');
            $outdated = $expiredDate < time();
            $invalidated = $row[1] || $row[2];

            $result = ["valid" => (int) (!($outdated || $invalidated)), "outdated" => (int) $outdated, "invalidated" => (int) $invalidated];
        }

        $query->close();
        $conn->close();
        return $result;
    }

    /**
     * Funkcja sprawdza, czy jest jakikolwiek ważny auth key (w sumie to powinien być tylko 1, czyli czy sesja konta
     * jest ważna)
     * @param int $websiteId id strony
     * @param int $userId id usera (lub admina)
     * @param bool $admin czy admin
     * @return bool czy sesja konta jest ważna
     */
    public static function isAnyAuthKey(int $websiteId, int $userId, bool $admin): bool
    {
        $conn = Connection::getConnection();
        if($admin) $column = "admin_id";
        else $column = "user_id";
        $query = $conn->query("SELECT expired_time FROM websites_auth_keys 
                    WHERE website_id = $websiteId 
                    AND $column = $userId
                    AND logout = 0 
                    AND invalid = 0
                    ORDER BY expired_time DESC 
                    LIMIT 1");

        if($query->num_rows == 0)
            $result = false;
        else
        {
            $expiredTime = $query->fetch_row()[0];
            $expiredDate = strtotime($expiredTime);
            if($expiredDate < time()) $result = false;
            else $result = true;
        }

        $query->close();
        $conn->close();
        return $result;
    }

    /**
     * Funkcja sprawdza, czy jest jakikolwiek ważny auth key (w sumie to powinien być tylko 1, czyli czy sesja konta
     * jest ważna)
     * @param int $websiteId id strony
     * @param int $userId id usera (lub admina)
     * @param bool $admin czy admin
     * @return bool czy sesja konta jest ważna
     */
    public static function getLastAuthKeyID(int $websiteId, int $userId, bool $admin): ?int
    {
        $conn = Connection::getConnection();
        if($admin) $column = "admin_id";
        else $column = "user_id";
        $query = $conn->query("SELECT id, expired_time FROM websites_auth_keys 
                    WHERE website_id = $websiteId 
                    AND $column = $userId
                    AND logout = 0 
                    AND invalid = 0
                    ORDER BY expired_time DESC 
                    LIMIT 1");

        if($query->num_rows == 0)
            $result = null;
        else
        {
            $row = $query->fetch_row();
            $expiredTime = $row[1];
            $expiredDate = strtotime($expiredTime);
            if($expiredDate < time()) $result = null;
            else $result = $row[0];
        }

        $query->close();
        $conn->close();
        return $result;
    }

    /**
     * @param int $website_id id strony
     * @param string $authKey auth_key
     * @param bool $logout czy wylogowanie "dobrowolne" (1 - wylogowanie, 0 - działanie admina)
     * @return void
     */
    public static function invalidateAuthKey(int $website_id, string $authKey, bool $logout): void
    {
        $conn = Connection::getConnection();

        date_default_timezone_set('Europe/Warsaw');
        $now = date("Y-m-d H:i:s");

        if($logout)
            $sql = "";

        $conn->query("UPDATE websites_auth_keys 
                              SET expired_time = '$now', logout = 1 
                              WHERE website_id = $website_id 
                              AND auth_key = '$authKey'");
        $conn->close();
    }

    /**
     * Zwraca aktywny auth_key dla podanego IP oraz zwraca jego wartość. Jeśli nie znajdzie, funckcja zwraca pusty łańcuch
     * @param int $websiteId
     * @param string $ip
     * @return string aktywne auth_key
     */
    public static function getActiveAuthKey(int $websiteId, string $ip): string {
        $conn = Connection::getConnection();

        $query = $conn->query("SELECT expired_time, auth_key FROM websites_auth_keys
                    WHERE logout = 0
                    AND website_id = $websiteId
                    ORDER BY expired_time DESC
                    LIMIT 1");

        $resp = "";
        if($query->num_rows != 0)
        {
            $row = $query->fetch_row();
            if(strtotime($row[0]) > strtotime("now"))
                $resp = $row[1];
        }

        $conn->close();
        return $resp;
    }

    public static function isAdminAuthKey(int $websiteId, string $authKey): bool
    {
//        TODO walidacja - authKey tylko 0-9 a-f
        $conn = Connection::getConnection();

        $query = $conn->query("SELECT admin_id, expired_time FROM websites_auth_keys
                    WHERE logout = 0
                    AND website_id = $websiteId
                    AND auth_key = '$authKey' 
                    ORDER BY expired_time DESC
                    LIMIT 1");

        $resp = false;
        if($query->num_rows != 0)
        {
            $row = $query->fetch_row();
            if(!is_null($row[0]) && strtotime($row[1]) > strtotime("now"))
                $resp = true;
        }

        $conn->close();
        return $resp;
    }
}