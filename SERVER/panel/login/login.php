<?php

    header("Content-Type: application/json; charset=utf-8");

    session_start();

    if(isset($_SESSION["electrocms-admin-logged"]) && $_SESSION["electrocms-admin-logged"]) {
        header("location: ../");
        die("You are already logged in! If not redirected, try refreshing the page.");
    }

    if(!isset($_SESSION["token-server-login"]) || !isset($_SESSION["token-server-login-expire"])) die("Wrong token. Reload the form.");
    if(time() >= $_SESSION["token-server-login-expire"]) die("Token has expired. Reload the form.");
    if($_SESSION["token-server-login"] != $_POST["token"]) die("Incorrect token.");

    if(!isset($_POST["username"]) || !isset($_POST["password"])) {
        die("Check your posts.");
    }

    require_once(dirname(__DIR__, 2) . "/database/Connection.inc.php");

    if(isBanned($_SERVER["REMOTE_ADDR"])) {
        die("Your IP has been banned!");
    }

    function addLoginHistory($ip, $username, $password, $logged): void
    {
        $conn = Connection::getConnection();
        if(!$logged) $logged = "0";
        $conn->query("INSERT INTO login_history VALUES (null,'$ip','$username','$password',null,$logged)");
        $conn->close();
    }

    function getTries($ip): int
    {
        $conn = Connection::getConnection();

        date_default_timezone_set("Europe/Warsaw");
        $date = date("Y-m-d");

        $result = $conn->query("SELECT date FROM login_history WHERE ip='$ip' AND date LIKE '$date%' AND logged=0");
        $res = 5 - $result->num_rows;
        $conn->close();
        return $res;
    }

    function isBanned($ip): bool
    {
        if(getTries($ip) <= 0) return true;

        $conn = Connection::getConnection();
        $result = $conn->query("SELECT * FROM login_banned_ips WHERE ip='$ip'");
        $banned = $result->num_rows >= 1;

        $conn->close();
        
        if(!$banned) $banned = getTries($ip) <= 0;
        return $banned;
    }


    function login($ip, $username, $password): bool
    {
        if(isBanned($ip)) return false;

        if(!empty($username) || !empty($password)) 
        {
            $conn = Connection::getConnection();
            $result = $conn->query("SELECT id, password FROM users WHERE username = '$username'");
            if(mysqli_num_rows($result) == 1) 
            {
                $row = mysqli_fetch_assoc($result);
                if(password_verify($password, $row['password'])) 
                {
                    addLoginHistory($ip,$username,$password,true);

                    $_SESSION["electrocms-admin-id"] = $row["id"];
                    $_SESSION["electrocms-admin-logged"] = true;
                    unset($_SESSION["token-server-login"]);
                    unset($_SESSION["token-server-login-expire"]);

                    $conn->close();
                    return true;
                } else {
                    addLoginHistory($ip,$username,$password,false);
                    $conn->close();
                    return false;
                }
            } else {
                addLoginHistory($ip,$username,$password,false);
                $conn->close();
                return false;
            }
        }

        return false;
    }

    if(login($_SERVER["REMOTE_ADDR"],$_POST["username"],$_POST["password"])) {
        header("Location: ../");
        die("If not redirected, try refreshing the page.");
    } else {
        die("Wrong username/password. Attepts left: ".getTries($_SERVER["REMOTE_ADDR"]));
    }