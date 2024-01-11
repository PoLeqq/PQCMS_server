<?php

require_once(dirname(__DIR__,2)."/database/Connection.inc.php");
class SecureKey
{
    public static function generateSecureKey(int $website_id, string $remoteAddr): string
    {
        $conn = Connection::getConnection();

        date_default_timezone_set('Europe/Warsaw');
        $date = date("Y-m-d H:i:s", time() + 3600);
        do {
            $secureKey = bin2hex(random_bytes(64));
        } while($conn->query("SELECT id FROM websites_secure_keys WHERE website_id = $website_id AND secure_key = '$secureKey' AND expired_time >= '$date'")->num_rows > 0);

        $conn->query("INSERT INTO websites_secure_keys (website_id, secure_key, ip, expired_time) VALUES ($website_id, '$secureKey', '$remoteAddr', '$date')");
        $conn->close();

        return $secureKey;
    }

    public static function isValidSecureKey(int $websiteId, string $remoteAddr, string $secureKey): bool
    {
        if(!self::isProperSecureKey($secureKey))
            return false;
        $conn = Connection::getConnection();

        date_default_timezone_set('Europe/Warsaw');
        $date = date("Y-m-d H:i:s");

        $stmt = $conn->prepare("SELECT id FROM websites_secure_keys 
          WHERE website_id = ? 
            AND expired_time >= ?
            AND ip = ?
            AND invalid = 0
            AND BINARY secure_key = ?");
        $stmt->bind_param("isss",$websiteId,$date,$remoteAddr,$secureKey);
        $stmt->execute();

        return $stmt->get_result()->num_rows >= 1;
    }

    public static function hasValidSecureKey(int $websiteId, string $remoteAddr): bool
    {
        $conn = Connection::getConnection();

        date_default_timezone_set('Europe/Warsaw');
        $date = date("Y-m-d H:i:s");

        return $conn->query("SELECT id FROM websites_secure_keys 
          WHERE website_id = $websiteId 
            AND expired_time >= '$date'
            AND ip = '$remoteAddr'
            AND invalid = 0")->num_rows > 0;
    }

    public static function isProperSecureKey(string $secureKey): bool
    {
        return (bool)preg_match('/^[0-9a-f]{128}$/', $secureKey);
    }

    public static function invalidateSecureKey(int $website_id, string $secureKey, string $apiName): void
    {
        $conn = Connection::getConnection();

//        TODO ??? dołożyć do tego jeszcze kolumnę `data` - gdy zostanie dodana do DB (o ile zostanie dodana)
        date_default_timezone_set('Europe/Warsaw');
        $now = date("Y-m-d H:i:s");
        $conn->query("UPDATE websites_secure_keys SET used_time = '$now', api_name = '$apiName' WHERE website_id = $website_id AND secure_key = '$secureKey'");
        $conn->close();
    }
}