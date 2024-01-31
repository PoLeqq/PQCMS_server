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
        if(preg_match("/^[a-z]+$/", $name) != 1)
            return ["suc" => 0, "desc" => "Nazwa może składać się tylko z małych liter!"];
        if(strlen($displayName) < 2 || strlen($displayName) > 30)
            return ["suc" => 0, "desc" => "Wyświetlana nazwa musi mieć od 2 do 30 znaków!"];
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

            $conn->query("INSERT INTO websites_ranks VALUES (null,$websiteId,'$name','$displayName','$perms','$priority',$parentId,0)");
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
        $sql = "SELECT * FROM websites_ranks WHERE $col = $value AND deleted = 0";
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

    public static function getWebsiteRankByName(string $name, int $websiteId): ?array
    {
        $conn = Connection::getConnection();

        $stmt = $conn->prepare("SELECT id FROM websites_ranks 
          WHERE BINARY name = ? 
            AND website_id = ? 
            AND deleted = 0");
        $stmt->bind_param("si",$name,$websiteId);
        $stmt->execute();

        if($stmt->errno !== 0)
            return null;

        $result = $stmt->get_result();
        if($result->num_rows >= 1)
            $response = $result->fetch_assoc();
        else $response = null;

        $result->close();
        $stmt->close();
        $conn->close();
        return $response;
    }

    public static function deleteRank(Website $website, string $name): void
    {
        $conn = Connection::getConnection();
        $stmt = $conn->prepare("UPDATE websites_ranks 
            SET deleted = 1 
            WHERE website_id = ? 
              AND name = ?");
        $websiteId = $website->getId();
        $stmt->bind_param("is", $websiteId,$name);
        $stmt->execute();
        $stmt->close();

//        $stmt = $conn->prepare('UPDATE websites_users SET perms = REPLACE(REPLACE(perms, '"pqcms.*":true', ""),'"pqcms.*":false',"");');

//        Pobranie użytkowników strony
        $query = $conn->query("SELECT id, perms FROM websites_users WHERE website_id = $websiteId");
        $userPerms = [];

//        dodanie do array nowych permisji (usunięcie permisji do usuniętej rangi) tylko wtedy, gdy user tę rangę posiada
        $rankPerm = "pqcms.rank.$name";
        while($row = $query->fetch_assoc())
        {
            $newPerms = json_decode($row["perms"],true);

            if(array_key_exists($rankPerm, $newPerms))
            {
                unset($newPerms[$rankPerm]);
                $userPerms[$row["id"]] = $newPerms;
            }
        }

//        zamiana permisji w DB
        foreach($userPerms as $id => $perms)
        {
            $stmt = $conn->prepare("UPDATE websites_users SET perms = ? WHERE id = ?");
            $perms = json_encode($perms);
            $stmt->bind_param("si",$perms,$id);
            $stmt->execute();
            $stmt->close();
        }

//        Również nullowanie, gdy ranga była dzieckiem
//        pobieranie id rangi:
        $stmt = $conn->prepare("SELECT id FROM websites_ranks WHERE website_id = ? AND name = ?");
        $stmt->bind_param("is",$websiteId,$name);
        $stmt->execute();
        $rankID = $stmt->get_result()->fetch_row()[0];

        $conn->query("UPDATE websites_ranks SET parent_id = NULL 
                      WHERE parent_id = $rankID");

        $conn->close();
    }

    public static function editRank(int $websiteId, string $name, ?string $displayName, ?int $priority, ?int $parentId, ?array $perms): array
    {
//        Walidacja pól
        if(!is_null($displayName))
        {
            $validator = self::validateDisplayName($displayName);
            if($validator["suc"] === 0)
                return $validator;
        }
        if(!is_null($priority))
        {
            $validator = self::validatePriority($priority);
            if($validator["suc"] === 0)
                return $validator;
        }
        if(!is_null($parentId))
        {
            $validator = self::validateParentId($parentId);
            if($validator["suc"] === 0)
                return $validator;
        }
        if(!is_null($perms))
        {
            require_once "WebsitePermissions.php";
            if(!WebsitePermissions::isProperPermsArray($perms))
                return ["suc" => 0, "desc" => "Podano niepoprawne permisje!"];
        }

        $conn = Connection::getConnection();
        $stmt = $conn->prepare("SELECT id FROM websites_ranks WHERE website_id = $websiteId AND name = ?");
        $stmt->bind_param("s",$name);
        $stmt->execute();

        $stmtResult = $stmt->get_result();

        if($stmtResult->num_rows == 0)
            $resp = ["suc" => 0, "desc" => "Ranga o podanej nazwie nie istnieje!"];
        else
        {
            $rankId = $stmtResult->fetch_row()[0];

            $sql = [];
            $params = [];
            $paramsTypes = "";

            if(!is_null($displayName))
            {
                $sql[] = " display_name = ?";
                $params[] = $displayName;
                $paramsTypes .= "s";
            }
            if(!is_null($priority))
            {
                $sql[] = " priority = ?";
                $params[] = $priority;
                $paramsTypes .= "i";
            }
            if(!is_null($parentId))
            {
                $sql[] = " parent_id = ?";
                $params[] = $parentId;
                $paramsTypes .= "i";
            }
            if(!is_null($perms))
            {
                $sql[] = " perms = ?";
                $params[] = json_encode($perms);
                $paramsTypes .= "s";
            }

            $updateSQLQuery = "UPDATE websites_ranks SET ".implode(',',$sql)." WHERE id = $rankId";

            $updateStmt = $conn->prepare($updateSQLQuery);
            $updateStmt->bind_param($paramsTypes,...$params);
            $updateStmt->execute();

            if($conn->errno === 0)
                $resp = ["suc" => 1, "desc" => "Edycja rangi powiodła się!"];
            else
                $resp = ["suc" => 0, "desc" => "Błąd podczas edytowania rangi. Kod błędu: ".($conn->errno)."!"];
        }

        $conn->close();
        return $resp;
    }
}