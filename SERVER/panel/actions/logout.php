<?php

session_start();
if (empty($_SESSION["pqcms-server-admin-id"])) {
    header("location: ../");
    die("Najpierw musisz się zalogować! Błędne przekierowanie.");
}

foreach(array_keys($_SESSION) as $sessionKey)
    if(str_starts_with($sessionKey, "pqcms-server-"))
        unset($_SESSION[$sessionKey]);

header("location: ../login/");
session_start();