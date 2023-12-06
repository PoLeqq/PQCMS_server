<?php

session_start();
if(empty($_SESSION["pqcms-client-system-user_id"]))
{
    header("location: ../login/");
    die("Najpierw się zaloguj. Niepoprawne przekierowanie!");
}