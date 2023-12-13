<?php

session_start();
if(empty($_SESSION["pqcms-client-system-user_id"]))
{
    $_SESSION["pqcms-client-system-login_redirect"] = "suggestion";
    header("location: ../login/");
    die("Najpierw się zaloguj. Niepoprawne przekierowanie!");
}

?>php
