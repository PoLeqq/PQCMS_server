<?php

class Connection
{
    static private string $database_host = "localhost";
    static private string $database_username = "root";
    static private string $database_password = "";
    static private string $database_name = "pqcms_server";

    public static function getConnection(): bool|mysqli
    {
        return mysqli_connect(Connection::$database_host,Connection::$database_username,Connection::$database_password,Connection::$database_name);
    }
}