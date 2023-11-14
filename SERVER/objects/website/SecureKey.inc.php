<?php


class SecureKey
{
    public static function generateSecureKey(int $website_id): string
    {
        require_once(dirname(__DIR__,2)."/database/Connection.inc.php");
        $conn = Connection::getConnection();

        do {
            $secureKey = bin2hex(random_bytes(32));
        } while($conn->query("SELECT id FROM websites_keys WHERE website_id = $website_id AND secure_key = '$secureKey'")->num_rows > 0);

        $conn->query("INSERT INTO websites_keys (website_id, secure_key) VALUES ($website_id, '$secureKey')");
        $conn->close();

        return $secureKey;
    }
}