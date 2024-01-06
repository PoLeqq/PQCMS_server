<?php

require_once(dirname(__DIR__,2)."/database/Connection.inc.php");
class SecureKey
{
    public static function generateSecureKey(int $website_id): string
    {
        $conn = Connection::getConnection();

        do {
            $secureKey = bin2hex(random_bytes(64));
        } while($conn->query("SELECT id FROM websites_secure_keys WHERE website_id = $website_id AND secure_key = '$secureKey' AND used_time IS NULL")->num_rows > 0);

        $conn->query("INSERT INTO websites_secure_keys (website_id, secure_key) VALUES ($website_id, '$secureKey')");
        $conn->close();

        return $secureKey;
    }

    public static function isValidSecureKey(string $secureKey): bool
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