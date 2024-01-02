<?php

require_once(dirname(__DIR__,2)."/database/Connection.inc.php");
class WebsiteRank
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

    public function getName(): string
    {
        return $this->unsafe_getField("name");
    }

    public function getDisplayName(): string
    {
        return $this->unsafe_getField("display_name");
    }

    public function getPerms(): array
    {
        return $this->unsafe_getField("perms");
    }

    public function getPriority(): int
    {
        return $this->unsafe_getField("priority");
    }

    public function getParentId(): ?int
    {
        return $this->unsafe_getField("parent_id");
    }

    private function unsafe_getField($column): mixed
    {
        $conn = Connection::getConnection();
        $query = $conn->query("SELECT $column FROM websites_ranks WHERE id = $this->id");

        $fetchArray = mysqli_fetch_array($query);
        if($fetchArray == null || count($fetchArray) == 0) return null;
        $result = $fetchArray[0];

        $query->close();
        $conn->close();
        return $result;
    }

    public static function addRank(int $websiteId, string $name, array $perms, int $priority, ?int $parentId): array
    {
//        Walidacja długości
        if(strlen($name) < 5 || strlen($name) > 30)
            return ["suc" => 0, "desc" => "Nazwa musi mieć od 5 do 30 znaków!"];
        require_once "WebsitePermissions.php";
        $perms = WebsitePermissions::parsePostPermsArray($perms);
        if(is_null($perms))
            return ["suc" => 0, "desc" => "Podano niepoprawne permisje!"];

//        Walidacja - anti SQL injection
        require_once(dirname(__DIR__,2)."/utils/SQLSecurity.php");
        $insecureCharsResponse = SQLSecurity::generateResponseForAPI(SQLSecurity::doesStringContains($name),"name");
        if(sizeof($insecureCharsResponse) !== 0)
            return $insecureCharsResponse;

        $conn = Connection::getConnection();

        $query = $conn->query("SELECT name FROM websites_ranks 
            WHERE website_id = $websiteId 
              AND name = '$name'");
        if($query->num_rows == 0)
        {
            $perms = json_encode($perms);
            if(is_null($parentId))
                $parentId = "NULL";

            $conn->query("INSERT INTO websites_ranks VALUES (null,$websiteId,'$name','$perms','$priority',$parentId)");
            if($conn->errno === 0) $resp = ["suc" => 1, "desc" => "Dodano rangę!"];
            else $resp = ["suc" => 0, "desc" => "Błąd podczas dodawania rangi. Kod błędu: ".($conn->errno)."!"];
            $conn->close();
        }
        else $resp = ["suc" => 0, "desc" => "Już istnieje ranga o takiej nazwie!"];

        return $resp;
    }

    public static function unsafe_getRankBy(string $col, mixed $value, int $websiteId = null): ?array
    {
        $conn = Connection::getConnection();

        if(is_string($value)) $value = "'".$value."'";
        $sql = "SELECT * FROM websites_ranks WHERE $col = $value";
        if($websiteId != null) $sql .= " AND website_id = $websiteId";

        $query = $conn->query($sql);
        if($query->num_rows >= 1)
        {
            $result = [];
            foreach($query->fetch_assoc() as $row)
                $result[] = $row;
        }
        else $result = null;

        $query->close();
        $conn->close();
        return $result;
    }
}