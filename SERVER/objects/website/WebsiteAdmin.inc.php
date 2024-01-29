<?php

class WebsiteAdmin
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

    public function doesPasswordMatch($password): bool
    {
        return password_verify($password,$this->unsafe_getField("password"));
    }

    private function unsafe_getField($column): mixed
    {
        $conn = Connection::getConnection();
        $query = $conn->query("SELECT $column FROM websites_admins WHERE id = $this->id");

        $fetchArray = mysqli_fetch_array($query);
        if($fetchArray == null || count($fetchArray) == 0) return null;
        $result = $fetchArray[0];

        $query->close();
        $conn->close();
        return $result;
    }

    public static function addWebsiteAdmin(int $websiteId, string $username, string $nickname, string $email, string $password): array
    {
        require_once(dirname(__DIR__,2)."/utils/SQLSecurity.php");
        $insecureCharsResponse = SQLSecurity::generateResponseForAPI(SQLSecurity::doesStringContains($username),"username");
        if(sizeof($insecureCharsResponse) !== 0)
            return $insecureCharsResponse;
        $insecureCharsResponse = SQLSecurity::generateResponseForAPI(SQLSecurity::doesStringContains($nickname),"nickname");
        if(sizeof($insecureCharsResponse) !== 0)
            return $insecureCharsResponse;

        $conn = Connection::getConnection();
        $password = password_hash($password,PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO websites_admins VALUES (null,?,?,?,?,?,0)");
        $stmt->bind_param("issss",$websiteId,$username,$nickname,$email,$password);
        $stmt->execute();

        $errno = $stmt->errno;
        $conn->close();
        return ["suc" => $errno === 0, "desc" => $errno];
    }
}