<?php

require_once(dirname(__DIR__) . "/database/Connection.inc.php");

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

    public function doesExists(): bool
    {
        return $this->getField("id") != null;
    }

//    Gettery, settery opierające się na obiekcie, wymagające bardziej złożonych operacji
//    (działają na kilku tabelach, nie tylko na "websites"

    public function getAdminId(): ?int
    {
        $conn = Connection::getConnection();
        $query = $conn->query("SELECT id FROM websites_admins WHERE website_id = $this->id");

        if($query->num_rows == 0)
            $result = null;
        else $result = $query->fetch_row()[0];

        $query->close();
        $conn->close();
        return $result;
    }

    public function addAdmin(string $username, string $nickname, string $password): void
    {
        require_once(dirname(__DIR__) . "/objects/website/WebsiteAdmin.inc.php");
        WebsiteAdmin::unsafe_addWebsiteAdmin($this->id,$username,$nickname,$password);
    }

    public function addUser(string $username, string $nickname, string $password, array $perms, bool $disabled): void
    {
        require_once(dirname(__DIR__) . "/objects/website/WebsiteUser.inc.php");
        WebsiteUser::unsafe_addWebsiteUser($this->id,$username,$nickname,$password, $perms, $disabled);
    }

    public function getUsers(): ?array
    {
        $conn = Connection::getConnection();
        $query = $conn->query("SELECT id FROM websites_users WHERE website_id = $this->id");

        if($query->num_rows == 0)
            $result = null;
        else
        {
            $result = [];
            foreach($query->fetch_row() as $row)
                $result[] = $row[0];
        }

        $query->close();
        $conn->close();
        return $result;
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

    public static function getWebsiteIDByMatching($column,$value): ?int
    {
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
        if($blocked) $blocked = 1; else $blocked = 0;
        if($license_expiration != null) $license_expiration = "'$license_expiration'";
        else $license_expiration = 'null';

        $sql = "INSERT INTO websites VALUES (null,'$domain','$login','$license_key',$license_expiration,$blocked)";
        $conn->query($sql);
        $conn->close();
    }
}