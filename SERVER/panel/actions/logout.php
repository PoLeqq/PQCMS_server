<?php
    session_start();
    unset($_SESSION["electrocms-admin-logged"]);
    unset($_SESSION["electrocms-admin-id"]);
    header("location: ../login/");