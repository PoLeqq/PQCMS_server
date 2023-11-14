<?php
    session_start();

    if(isset($_SESSION['user']))
//        echo
        header('Location: panel/');
    else{
        require_once("./config/JSONLogin.php");
        $login = new JSONLogin();
        $_SESSION["loginAmount"] = $login->getAttempts();
        header('Location: login/');
    }