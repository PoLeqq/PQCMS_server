<?php

class WebsiteSettings
{
    private int $websiteId;

    public function __construct(int $websiteId)
    {
        $this->websiteId = $websiteId;
    }

    public function getLoginAttempts(): int 
    {
        return $this->unsafe_getField("login_attempts");
    }

    public function setLoginAttempts(?int $loginAttempts): void
    {
        $this->unsafe_setField("login_attempts",$loginAttempts);
    }

    public function resetLoginAttempts(): void
    {
        $this->unsafe_setField("login_attempts",null);
    }

    public function getTokenLifespan(): int 
    {
        return $this->unsafe_getField("token_lifespan");
    }

    public function setTokenLifespan(?int $lifespan): void
    {
        $this->unsafe_setField("token_lifespan",$lifespan);
    }

    public function resetTokenLifespan(): void
    {
        $this->unsafe_setField("token_lifespan",null);
    }

    private function unsafe_setField(string $column, mixed $value): void
    {
        if(is_string($value)) $value = "'$value'";
        if(is_null($value)) $value = "DEFAULT";

        $conn = Connection::getConnection();
        $conn->query("UPDATE websites_settings SET $column = $value WHERE website_id = $this->websiteId");
        $conn->close();
    }

    private function unsafe_getField(string $column): mixed
    {
        $conn = Connection::getConnection();
        $query = $conn->query("SELECT $column FROM websites_settings WHERE website_id = $this->websiteId");

        $fetchArray = mysqli_fetch_array($query);
        if($fetchArray == null || count($fetchArray) == 0) return null;
        $result = $fetchArray[0];

        $query->close();
        $conn->close();
        return $result;
    }
}