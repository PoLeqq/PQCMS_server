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

    public function getLoginSessionTime(): int
    {
        return $this->unsafe_getField("login_session_time");
    }

    public function setLoginSessionTime(?int $loginSessionTime): void
    {
        $this->unsafe_setField("login_session_time",$loginSessionTime);
    }

    private function unsafe_setField(string $column, mixed $value): void
    {
//        W przypadku, gdy będą tu stringi (raczej nie będzie), trzeba zabezpieczyć kod z utils/SQLSecurity.php)
        if(is_string($value)) $value = "'$value'";
        if(is_null($value)) $value = "DEFAULT";

        $conn = Connection::getConnection();
        $conn->query("UPDATE websites_settings SET $column = $value WHERE website_id = $this->websiteId");
        $conn->close();
    }

    public function unsafe_getField(string $column): mixed
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