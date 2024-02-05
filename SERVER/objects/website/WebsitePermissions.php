<?php

class WebsitePermissions
{
    public static function isProperPermission(string $perm): bool
    {
        if($perm === "")
            return false;

//        * - poprawna
        if($perm === "*")
            return true;

//        .. ** - błędna
        if(str_contains($perm,".."))
            return false;
        if(substr_count($perm,"*") > 1)
            return false;

//        jeśli już zawiera * - musi być na końcu
        if(str_contains($perm,"*") && $perm[strlen($perm) - 1] !== '*')
            return false;

        $parts = explode('.', $perm);
        foreach ($parts as $part) {
            if(!preg_match('/^[a-z0-9*\.]+$/i',$part))
                return false;
            if(str_contains($part,"*") && $part !== "*")
                return false;
        }

        return true;
    }

    /**
     * Funkcja rekurencyjna. Zwraca listę permisji, które posiadają rangi (wraz z wszystkimi rodzicami)
     * @return void
     */
    private static function getParentsPermissions(mysqli $conn, $rankPerms) {
//todo
    }

    public static function hasPermissions(array $userPerms, array $checkPerms, ?int $websiteId = null): array
    {
        /*
         * Permisje sprawdza w następujący sposób:
         * Permisje użytkownika - if isset
         * Pobranie rang
         * Sortowanie rang wg priority (jeśli są takie same dla kilku rang to gratki)
         * Permisje rang - if isset
         * Permisje uż
         *
         *
         * Priorytety:
         * permisje usera
         * permisje rang (sorted by priority, asc)
         *
         * Algorytm:
         * Sprawdza permisje wg prio (if isset)
         * Jeśli nie ma, getParent
         */

        if(is_null($websiteId))
        {
            $permsResponse = [];
            foreach($checkPerms as $perm)
            {
                if(!self::isProperPermission($perm))
                {
                    $permsResponse[$perm] = -1;
                    continue;
                }

                $originalPerm = $perm;
                while(!array_key_exists($perm, $userPerms))
                {
                    $perm = self::getParentPermission($perm);
                    if($perm === "")
                        break;
                }

                $hasPermission = array_key_exists($perm,$userPerms) && $userPerms[$perm] == 1;
                $permsResponse[$originalPerm] = (int) $hasPermission;
            }
        }
        else
        {
//            $ranks = [];
//            foreach($userPerms as $perm => $value)
//            {
//                echo $perm;
//                if(str_starts_with($perm,"pqcms.rank."))
//                {
//                    $rank = str_replace("pqcms.rank.","",$perm);
//                    $ranks[] = $rank;
//                }
//            }

//            $allPerms = [$userPerms];
//            require_once(dirname(__DIR__,2)."/database/Connection.inc.php");
//            $conn = Connection::getConnection();
//            foreach($ranks as $rank)
//            {
////                tutaj miało być ale pqcms.rank.* , ale chyba zrezygnuję z posiadania wszystkich rang 1 permisją :p
//                $sql = "SELECT perms FROM websites_ranks WHERE name = '$rank' AND website_id = $websiteId";
//
//                $query = $conn->query($sql);
//                if($query->num_rows >= 1)
//                {
//                    $jsonPerms = json_decode($query->fetch_array()[0],true);
//                    if($jsonPerms !== false)
//                        $allPerms[] = $jsonPerms;
//                }
//
//                $query->close();
//                $conn->close();
//            }

            $allPerms = [$userPerms];
            require_once(dirname(__DIR__,2)."/database/Connection.inc.php");
            $conn = Connection::getConnection();
            $query = $conn->query("SELECT name, perms FROM websites_ranks WHERE website_id = $websiteId AND deleted = 0 ORDER BY priority DESC");
            while($row = $query->fetch_row())
            {
//                tutaj miało być ale pqcms.rank.* , ale chyba zrezygnuję z posiadania wszystkich rang 1 permisją :p
                if(isset($userPerms["pqcms.rank.${row[0]}"]) && $userPerms["pqcms.rank.${row[0]}"])
                {
                    $jsonPerms = json_decode($row[1],true);
                    if($jsonPerms !== false)
                        $allPerms[] = $jsonPerms;
                }
            }
            $query->close();
            $conn->close();

            $permsResponse = [];
            foreach($checkPerms as $perm)
            {
                if(!self::isProperPermission($perm))
                {
                    $permsResponse[$perm] = -1;
                    continue;
                }

                $originalPerm = $perm;
                $contains = false;
                $hasPermission = false;
                while(!$contains)
                {
                    foreach($allPerms as $allPerm)
                    {
                        if(array_key_exists($perm, $allPerm))
                        {
                            $contains = true;
                            $hasPermission = $allPerm[$perm] == 1;
                            break 2;
                        }
                    }

                    $perm = self::getParentPermission($perm);
                    if($perm === "")
                        break;
                }

                $permsResponse[$originalPerm] = (int) $hasPermission;
            }
        }


        return $permsResponse;
    }

    /**
     * Zwraca permisję-rodzica.
     * Zwraca null, gdy permisja nie jest poprawna
     * Zwraca pusty napis, gdy nie ma rodzica
     * @param string $perm
     * @return string|null
     */
    public static function getParentPermission(string $perm): ?string
    {
        if(!self::isProperPermission($perm))
            return null;

        $parts = explode('.', $perm);
        $partsCount = count($parts);
        if($partsCount === 1)
            if($perm === "*")
                return "";
            else
                return "*";

        if($parts[$partsCount-1] !== "*")
            $parts[$partsCount-1] = "*";
        else
            unset($parts[$partsCount-1]);

        return join(".",$parts);
    }

    public static function isProperPermsArray(array $array): bool
    {
        if($array === [])
            return true;
        if(array_values($array) === $array)
            return false;

        foreach($array as $perm => $value)
        {
            if(!self::isProperPermission($perm))
                return false;
            if(!is_bool($value))
                return false;
        }
        return true;
    }

    public static function parsePostPermsArray(array $array): ?array
    {
        if($array === [])
            return [];
        if(array_values($array) === $array)
            return null;

        $parsedArray = [];
        foreach($array as $perm => $value)
        {
            if(!self::isProperPermission($perm))
                return null;
            if($value === "0")
                $parsedArray[$perm] = false;
            else if($value === "1")
                $parsedArray[$perm] = true;
            else
                return null;
        }
        return $parsedArray;
    }

    /**
     * Zwraca opis wszystkich permisji
     * @return array tablica asocjacyjna: ["permisja" => "opis"]
     */
    public static function getPermissionsDescriptions(?int $websiteId): array
    {
        require_once(dirname(__DIR__,2)."/database/Connection.inc.php");
        $conn = Connection::getConnection();

        $query = $conn->query("SELECT * FROM perms_descriptions");
        $perms = [];
        while($row = $query->fetch_assoc())
            $perms[] = $row;

        $query->close();
        $conn->close();

//        todo w przyszłości podzielenie tego na perimsje per user (np: (...).<username>, (...).<rankname>
        if(!is_null($websiteId))
        {
            require_once(dirname(__DIR__)."/Website.inc.php");
            $website = new Website($websiteId);

            $ranks = $website->getRanks();
            $users = $website->getUsers();

            $newPerms = [];
            foreach($perms as $perm)
            {
                $name = $perm["perm"];
                $desc = $perm["description"];

                if(str_contains($name,"(rank)"))
                    foreach($ranks as $rank)
                    {
                        $newPerm = [
                            "perm" => str_replace("(rank)",$rank["name"], $name),
                            "description" => str_replace("(rank)", $rank["display_name"]." (${rank["name"]})",$desc)
                        ];
                        $newPerms[] = $newPerm;
                    }
                else if(str_contains($name,"(user)"))
                    foreach($users as $user)
                    {
                        if(!isset($user["disabled"]))
                            continue;
                        $newPerm = [
                            "perm" => str_replace("(user)",$user["username"], $name),
                            "description" => str_replace("(user)",$user["nickname"]." (${user["username"]})",$desc)
                        ];
                        $newPerms[] = $newPerm;
                    }
                else
                    $newPerms[] = $perm;
            }

            foreach($ranks as $rank)
                $newPerms[] = [
                    "perm" => "pqcms.rank.".$rank["name"],
                    "description" => "Ranga: ${rank["display_name"]} (${rank["name"]})"
                ];
//            $userPerms = [
//                "pqcms.hr"
//            ];
//            foreach($website->getUsers() as $user)
//            {
//                $perms[] = [
//                    "perm" => "pqcms.site.group.set",
//                    "desc" =>
//                ];
//            }
            return $newPerms;
        }
        return $perms;
    }

}