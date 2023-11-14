<?php
    function isFirstTime(): bool
    {
        require_once(dirname(__DIR__)."/Verifier.inc.php");
//        TODO zmiana, powinno być połączenie z serwerem naszym i potem sprawdzanie
//        $conn = Database::getConnection();
//        $result = $conn->query("SELECT id FROM users");
//
//        $firstTime = false;
//        if($result->num_rows == 0) $firstTime = true;
//        $conn->close();
//
//        return $firstTime;
    }