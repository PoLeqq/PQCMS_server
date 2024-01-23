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

    public static function validateDisplayName(/*string*/$displayName): array
    {
        require_once(dirname(__DIR__,2)."/api/utils/validators/Validator.inc.php");
        return Validator::validate([$displayName],["s(2-30)"])["suc"] ? ["suc" => 1] : ["suc" => 0, "desc" => "Login musi być napisem o długości 2-30 znaków!"];
    }

    public static function validatePriority(/*int*/$priority): array
    {
        require_once(dirname(__DIR__,2)."/api/utils/validators/Validator.inc.php");
        return Validator::validate([$priority],["i(0-65535)"])["suc"] ? ["suc" => 1] : ["suc" => 0, "desc" => "Priorytet musi być liczbą w zakresie 0-65535!"];
    }

    public static function validateParentId(/*int*/$parentId): array
    {
//        todo kiedyś może czy wgl ranga istnieje?
        require_once(dirname(__DIR__,2)."/api/utils/validators/Validator.inc.php");
        return Validator::validate([$parentId],["i"])["suc"] ? ["suc" => 1] : ["suc" => 0, "desc" => "Priorytet musi być liczbą!"];
    }

    public function getWebsiteId(): int
    {
        return $this->getField("website_id");
    }

    public function getName(): string
    {
        return $this->getField("name");
    }

    public function getDisplayName(): string
    {
        return $this->getField("display_name");
    }

    public function getPerms(): array
    {
        return $this->getField("perms");
    }

    public function getPriority(): int
    {
        return $this->getField("priority");
    }

    public function getParentId(): ?int
    {
        return $this->getField("parent_id");
    }

    public function isDisabled(): ?bool
    {
        return $this->getField("deleted");
    }

    private function getField($column): mixed
    {
        $conn = Connection::getConnection();
        $query = $conn->query("SELECT * FROM websites_ranks WHERE id = $this->id");

        $fetchArray = $query->fetch_assoc();
        if($fetchArray == null || count($fetchArray) == 0 || !isset($fetchArray[$column]))
            return null;
        $result = $fetchArray[0];

        $query->close();
        $conn->close();
        return $result;
    }

    public static function addRank(int $websiteId, string $name, string $displayName, array $perms, int $priority, ?int $parentId): array
    {
//        Walidacja długości
        if(strlen($name) < 5 || strlen($name) > 30)
            return ["suc" => 0, "desc" => "Nazwa musi mieć od 5 do 30 znaków!"];
        if(strlen($displayName) < 5 || strlen($displayName) > 30)
            return ["suc" => 0, "desc" => "Wyświetlana nazwa musi mieć od 5 do 30 znaków!"];
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

            $conn->query("INSERT INTO websites_ranks VALUES (null,$websiteId,'$name','$displayName','$perms','$priority',$parentId)");
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