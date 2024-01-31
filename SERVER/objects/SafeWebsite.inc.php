<?php

require_once "Website.inc.php";
class SafeWebsite
{
    protected int $id;
    protected string $ip;
    protected string $authKey;
    protected Website $website;

    public function __construct(int $id, string $ip, string $authKey)
    {
        $this->id = $id;
        $this->authKey = $authKey;
        $this->ip = $ip;
        $this->website = new Website($id);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getWebsite(): Website
    {
        return $this->website;
    }

    public function getUsers(): array
    {
        $users = [];
        foreach($this->website->getUsers() as $user)
        {
            if($this->website->hasPermission($this->ip,$this->authKey,"pqcms.hr.user.get.".$user["username"]))
                $users[] = $user;
        }
        return $users;
    }

    public function getRanks(): array
    {
        $ranks = [];
        require_once("website/WebsiteRank.inc.php");
        foreach($this->website->getRanks() as $rank)
        {
            if($this->website->hasPermission($this->ip,$this->authKey,"pqcms.hr.rank.get.".$rank["name"]))
            {
                if(!is_null($rank["parent_id"]))
                {
                    $parentRank = WebsiteRank::unsafe_getRankBy("id",$rank["parent_id"]);
                    if(!is_null($rank))
                        $rank["parent"] = $parentRank[2];
                }
                else
                    $rank["parent"] = null;
                unset($rank["parent_id"]);
                $ranks[] = $rank;
            }
        }
        return ["suc" => 1, "resp" => $ranks];
    }

    public function addRank(string $name, string $displayName,array $perms, int $priority, ?int $parentId): array
    {
        if($this->website->hasPermission($this->remoteAddr,$this->authKey,"pqcms.hr.rank.add"))
            return $this->website->addRank($name,$displayName,$perms,$priority,$parentId);
        return $this->getUnpermittedArray();
    }

    public function addUser(string $username, string $nickname, ?string $email, string $password, array $perms, int $disabled): array
    {
        foreach($perms as $perm => $value)
            if(!$this->website->hasPermission($this->ip,$_POST["auth_key"],$perm))
                unset($perms[$perm]);

        if($this->website->hasPermission($this->ip,$this->authKey,"pqcms.hr.user.add"))
        {
            $addUser = $this->website->addUser($username, $nickname, $email, $password, $perms, $disabled);
            if($addUser["suc"] === 1)
                $addUser["perms"] = $perms;
            return $addUser;
        }
        return $this->getUnpermittedArray();
    }

    public function deleteUser(string $username): array
    {
        if($this->website->hasPermission($this->ip,$this->authKey,"pqcms.hr.user.delete.$username"))
        {
            $this->website->deleteUser($username);
            return ["suc" => 1, "desc" => "Usunięto użytkownika!"];
        }
        return $this->getUnpermittedArray();
    }

    public function deleteRank(string $name): array
    {
        if($this->website->hasPermission($this->ip,$this->authKey,"pqcms.hr.rank.delete.$name"))
        {
            $this->website->deleteRank($name);
            return ["suc" => 1, "desc" => "Usunięto rangę!"];
        }
        return $this->getUnpermittedArray();
    }

    public function editUser(string $username, ?string $nickname, ?string $email, ?string $password, ?array $perms, ?int $disabled): array
    {
//        *pqcms.hr.user.edit.nickname.<nickname>
        if(!is_null($nickname) && !$this->website->hasPermission($this->ip,$this->authKey,"pqcms.hr.user.edit.nickname.".$username))
            $nickname = null;
        if(!is_null($email) && !$this->website->hasPermission($this->ip,$this->authKey,"pqcms.hr.user.edit.email.".$username))
            $email = null;
        if(!is_null($password) && !$this->website->hasPermission($this->ip,$this->authKey,"pqcms.hr.user.edit.password.set.".$username))
            $password = null;
        if(!is_null($disabled) && !$this->website->hasPermission($this->ip,$this->authKey,"pqcms.hr.user.edit.disable.".$username))
            $disabled = null;
        if(!is_null($perms) && !$this->website->hasPermission($this->ip,$this->authKey,"pqcms.hr.user.edit.perms.".$username))
            $perms = null;

        if(!empty($perms))
            foreach($perms as $perm => $value)
                if(!$this->website->hasPermission($this->ip,$this->authKey,$perm))
                    unset($perms[$perm]);

//        Nie może być, ponieważ gdy są puste permisje to się wyświetla - mimo, że user mógłBY zostać zmieniony
//        if(is_null($nickname) && is_null($email) && is_null($password) && empty($perms) && is_null($disabled))
//            return ["suc" => 0, "desc" => "Nic nie zmieniono, ponieważ nie masz odpowiednich uprawnień!"];

        $resp = $this->website->editUser($username, $nickname, $email, $password, $perms, $disabled);
        if($resp["suc"] === 1 && !is_null($perms))
            $resp["perms"] = $perms;

        return $resp;
    }

    public function editRank(string $name, ?string $displayName, ?int $priority, ?int $parentId, ?array $perms): array
    {
        //        *pqcms.hr.rank.edit.nickname.<nickname>
        if(!is_null($displayName) && !$this->website->hasPermission($this->ip,$this->authKey,"pqcms.hr.rank.edit.displayname.".$name))
            $displayName = null;
        if(!is_null($priority) && !$this->website->hasPermission($this->ip,$this->authKey,"pqcms.hr.rank.edit.priority.".$name))
            $email = null;
        if(!is_null($parentId) && !$this->website->hasPermission($this->ip,$this->authKey,"pqcms.hr.rank.edit.parentid.".$name))
            $parentId = null;
        if(!is_null($perms) && !$this->website->hasPermission($this->ip,$this->authKey,"pqcms.hr.rank.edit.perms.".$name))
            $perms = null;

        if(!empty($perms))
            foreach($perms as $perm)
                if(!$this->website->hasPermission($this->ip,$this->authKey,$perm))
                    unset($perms[$perm]);

        $resp = $this->website->editRank($name, $displayName, $priority, $parentId, $perms);
        if($resp["suc"] === 1 && !is_null($perms))
            $resp["perms"] = $perms;

        return $resp;
    }

    /**
     * @param string $username
     * @param string|null $password
     * @return array
     */
    public function setUserPassword(string $username, ?string $password): array
    {
//        *pqcms.hr.user.edit.nickname.<nickname>
        if($this->website->hasPermission($this->remoteAddr,$this->authKey,"pqcms.hr.user.add"))
            return $this->website->editUser($username, $nickname, $password, $perms, $disabled);
        return $this->getUnpermittedArray();
    }

    public function getLicenseExpiration(): array
    {
        if($this->website->hasPermission($this->ip,$this->authKey,"pqcms.data.licenseexpiration"))
            return ["suc" => 1, "license_expiration" => $this->website->getLicenseExpiration()];
        return $this->getUnpermittedArray();
    }

    private function getUnpermittedArray(): array
    {
        return ["suc" => 0, "desc" => "Nie masz uprawnień!"];
    }

    public function invalidateSession(string $username): array
    {
        if(!$this->website->hasPermission($this->ip,$this->authKey,"pqcms.hr.user.invalidatesession.".$username))
            return ["suc" => 0, "desc" => "Nie posiadasz uprawnień!"];

        require_once(__DIR__."/website/AuthKey.inc.php");
        require_once(__DIR__."/website/WebsiteUser.inc.php");

        $websiteUser = WebsiteUser::getWebsiteUserByUsername($username,$this->id);
        if(is_null($websiteUser))
            return ["suc" => 0, "desc" => "Nie znaleziono użytkownika o podanym loginie!"];

        return AuthKey::invalidateAuthKeyByUsername($this->id,$websiteUser[0],false,true);
    }
}