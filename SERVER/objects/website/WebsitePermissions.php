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
}