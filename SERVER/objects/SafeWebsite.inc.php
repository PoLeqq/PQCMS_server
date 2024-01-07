<?php

require_once "Website.inc.php";
class SafeWebsite
{
    protected int $id;
    protected string $remoteAddr;
    protected string $authKey;
    protected Website $website;

    public function __construct(int $id, string $remoteAddr, string $authKey)
    {
        $this->id = $id;
        $this->authKey = $authKey;
        $this->remoteAddr = $remoteAddr;
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
            if($this->website->hasPermission($this->remoteAddr,$this->authKey,"pqcms.hr.user.get.".$user["username"]))
                $users[] = $user;
        }
        return ["suc" => 1, "resp" => $users];
    }

    public function getRanks(): array
    {
        $ranks = [];
        require_once("website/WebsiteRank.inc.php");
        foreach($this->website->getRanks() as $rank)
        {
            if($this->website->hasPermission($this->remoteAddr,$this->authKey,"pqcms.hr.rank.get.".$rank["name"]))
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

    public function addUser(string $username, string $nickname, string $password, array $perms, int $disabled): array
    {
        if($this->website->hasPermission($this->remoteAddr,$this->authKey,"pqcms.hr.user.add"))
            return $this->website->addUser($username, $nickname, $password, $perms, $disabled);
        return $this->getUnpermittedArray();
    }

    public function editUser(string $username, ?string $nickname, ?string $password, ?array $perms, ?int $disabled): array
    {
        if(!$this->website->hasPermission($this->remoteAddr,$this->authKey,"pqcms.hr.user.edit.nickname.".$username))
            $nickname = null;
        if(!$this->website->hasPermission($this->remoteAddr,$this->authKey,"pqcms.hr.user.edit.perms.".$username))
            $perms = null;

//        *pqcms.hr.user.edit.nickname.<nickname>
        if($this->website->hasPermission($this->remoteAddr,$this->authKey,"pqcms.hr.user.add"))
            return $this->website->editUser($username, $nickname, $password, $perms, $disabled);
        return $this->getUnpermittedArray();
    }

    public function getLicenseExpiration(): array
    {
        if($this->website->hasPermission($this->remoteAddr,$this->authKey,"pqcms.data.licenseexpiration"))
            return ["suc" => 1, "license_expiration" => $this->website->getLicenseExpiration()];
        return $this->getUnpermittedArray();
    }

    private function getUnpermittedArray(): array
    {
        return ["suc" => 0, "desc" => "Nie masz uprawnień!"];
    }
}