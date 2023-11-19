<?php
    session_start();
    unset($_SESSION["pqcms-server-admin-logged"]);
    unset($_SESSION["pqcms-server-admin-id"]);
    header("location: ../login/");