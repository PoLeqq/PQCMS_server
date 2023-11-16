<?php

require_once(dirname(__DIR__,2)."/database/Connection.inc.php");
class SecureKey
{
    public static function generateSecureKey(int $website_id): string
    {
        $conn = Connection::getConnection();

        do {
            $secureKey = bin2hex(random_bytes(64));
        } while($conn->query("SELECT id FROM websites_keys WHERE website_id = $website_id AND secure_key = '$secureKey'")->num_rows > 0);

        $conn->query("INSERT INTO websites_keys (website_id, secure_key) VALUES ($website_id, '$secureKey')");
        $conn->close();

        return $secureKey;
    }

    public static function invalidateSecureKey(int $website_id, string $secureKey): void
    {
        $conn = Connection::getConnection();

//        TODO ??? dołożyć do tego jeszcze kolumnę `data` - gdy zostanie dodana do DB (o ile zostanie dodana)
        $conn->query("UPDATE websites_keys SET used_time = CURRENT_TIMESTAMP WHERE website_id = $website_id AND secure_key = '$secureKey'");
        $conn->close();
    }
}