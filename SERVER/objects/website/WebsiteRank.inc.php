<?php

require_once(dirname(__DIR__,2)."/database/Connection.inc.php");
require_once(dirname(__DIR__)."/Website.inc.php");
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
        $stmt = $conn->prepare("SELECT * FROM websites_ranks WHERE id = ?");
        $stmt->bind_param("i",$this->id);
        $stmt->execute();

        $fetchArray = $stmt->get_result()->fetch_assoc();
        if($fetchArray == null || count($fetchArray) == 0 || !isset($fetchArray[$column]))
            return null;
        $result = $fetchArray[$column];

        $stmt->close();
        $conn->close();
        return $result;
    }

    public static function addRank(int $websiteId, string $name, string $displayName, array $perms, int $priority, ?int $parentId): array
    {
//        Walidacja długości
        if(strlen($name) < 5 || strlen($name) > 30)
            return ["suc" => 0, "desc" => "Nazwa musi mieć od 5 do 30 znaków!"];
        if(preg_match("/^[a-z]+$/", $name) != 1)
            return ["suc" => 0, "desc" => "Nazwa może składać się tylko z małych liter (bez polskich znaków)!"];
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

        $stmt = $conn->prepare("SELECT name FROM websites_ranks 
            WHERE website_id = ? 
              AND name = ?
              AND deleted = 0");
        $stmt->bind_param("is",$websiteId,$name);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();
        if($result->num_rows == 0)
        {
            $perms = json_encode($perms);

            $parentId = null;
            $stmt = $conn->prepare("INSERT INTO websites_ranks VALUES (null,?,?,?,?,?,?,0)");
            $stmt->bind_param("issssi",$websiteId,$name,$displayName,$perms,$priority,$parentId);
            $stmt->execute();

//            stmt->errno, conn->errno, stmt->getresult->errno? chyba jest git ale nwm
            if($stmt->errno === 0) $resp = ["suc" => 1, "desc" => "Dodano rangę!"];
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

    public static function deleteRank(Website $website, string $name): array
    {
        if(preg_match("/^[a-z]+$/", $name) != 1)
            return ["suc" => 0, "desc" => "Nazwa rangi może składać się tylko z małych liter a-z (bez polskich znaków)!"];

        $websiteRank = WebsiteRank::getWebsiteRankByName($_POST["name"],$website->getId());
        if(is_null($websiteRank))
            die(json_encode(["suc" => 0, "desc" => "Nie znaleziono rangi o podanej nazwie!"],JSON_UNESCAPED_UNICODE));

//        todo usunięcie permisji typu: pqcms.hr.rank.get.<name> z userów, rang
        $conn = Connection::getConnection();

        $stmt = $conn->prepare("SELECT name FROM websites_ranks 
            WHERE website_id = ? 
              AND name = ?
              AND deleted = 0");
        $websiteId = $website->getId();
        $stmt->bind_param("is",$websiteId, $name);
        $stmt->execute();
        if($stmt->get_result()->num_rows == 0)
            return ["suc" => 0, "desc" => "Ta ranga nie istnieje!"];

        $stmt = $conn->prepare("UPDATE websites_ranks 
            SET deleted = 1 
            WHERE website_id = ? 
              AND name = ?
              AND deleted = 0");
        $stmt->bind_param("is", $websiteId,$name);
        $stmt->execute();
        $stmt->close();

//        $stmt = $conn->prepare('UPDATE websites_users SET perms = REPLACE(REPLACE(perms, '"pqcms.*":true', ""),'"pqcms.*":false',"");');

//        Pobranie użytkowników strony
        $stmt = $conn->prepare("SELECT id, perms FROM websites_users WHERE website_id = ?");
        $stmt->bind_param("i",$websiteId);
        $stmt->execute();

        $userPerms = [];

//        dodanie do array nowych permisji (usunięcie permisji do usuniętej rangi) tylko wtedy, gdy user tę rangę posiada
        $rankPerm = "pqcms.rank.$name";
        $result = $stmt->get_result();
        $stmt->close();

        while($row = $result->fetch_assoc())
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
        $stmt = $conn->prepare("SELECT id FROM websites_ranks WHERE website_id = ? AND name = ? AND deleted = 0");
        $stmt->bind_param("is",$websiteId,$name);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if($result->num_rows >= 1)
        {
            $rankID = $result->fetch_row()[0];

            $stmt = $conn->prepare("UPDATE websites_ranks SET parent_id = NULL 
                          WHERE parent_id = ?");
            $stmt->bind_param("i",$rankID);
            $stmt->execute();
            $stmt->close();
        }


        $conn->close();
        return ["suc" => 1, "desc" => "Usunięto rangę!"];
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
        $stmt = $conn->prepare("SELECT id FROM websites_ranks WHERE website_id = $websiteId 
                                AND name = ?
                                AND deleted = 0");
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