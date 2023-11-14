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

    public static function unsafe_addWebsiteUser(int $websiteId, string $username, string $nickname, string $password, ?array $perms, bool $disabled = false): void
    {
        $conn = Connection::getConnection();

        $disabled = (int) $disabled;
        $perms = json_encode($perms);

        $password = password_hash($password,PASSWORD_DEFAULT);

        $conn->query("INSERT INTO websites_users VALUES (null,$websiteId,'$username','$nickname','$password','$perms',$disabled)");
        $conn->close();
    }

    public static function getWebsiteUserBy(string $col, mixed $value, int $websiteId = null): ?array {
        $conn = Connection::getConnection();

        {
            if(is_string($value)) $value = "'".$value."'";
            $sql = "SELECT id FROM websites_users WHERE $col = $value";
            if($websiteId != null) $sql .= " AND website_id = $websiteId";
        }
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