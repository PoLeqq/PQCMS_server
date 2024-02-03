<?php

header("Content-Type: application/json; charset=utf-8");
session_start();

if(isset($_SESSION["pqcms-server-admin-logged"]) && $_SESSION["pqcms-server-admin-logged"])
{
    header("location: ../");
    die("You are already logged in! If not redirected, try refreshing the page.");
}

if(!isset($_SESSION["pqcms-server-token-login"]) || !isset($_SESSION["pqcms-server-token-login-expire"]))
    die("Wrong token. Reload the form.");
if(time() >= $_SESSION["pqcms-server-token-login-expire"])
    die("Token has expired. Reload the form.");
if($_SESSION["pqcms-server-token-login"] != $_POST["token"])
    die("Incorrect token.");

if(!isset($_POST["username"]) || !isset($_POST["password"]))
    die("Check your posts.");

require_once(dirname(__DIR__, 2) . "/database/Connection.inc.php");
if(isBanned($_SERVER["REMOTE_ADDR"]))
    die("Your IP has been banned!");

function addLoginHistory($ip, $username, $password, $logged): void
{
    $conn = Connection::getConnection();
    if(!$logged) $logged = "0";
    date_default_timezone_set('Europe/Warsaw');
    $now = date("Y-m-d H:i:s");
    $stmt = $conn->prepare("INSERT INTO login_history VALUES (null,?,?,?,?,?)");
    $stmt->bind_param("ssssi",$ip,$username,$password,$now,$logged);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

function getTries($ip): int
{
    $conn = Connection::getConnection();

    date_default_timezone_set("Europe/Warsaw");
    $date = date("Y-m-d")."%";

    $stmt = $conn->prepare("SELECT date FROM login_history WHERE ip=? AND date LIKE ? AND logged=0");
    $stmt->bind_param("ss",$ip,$date);
    $stmt->execute();

    $res = 5 - $stmt->get_result()->num_rows;
    $conn->close();
    return $res;
}

function isBanned($ip): bool
{
    if(getTries($ip) <= 0) return true;

    $conn = Connection::getConnection();
    $stmt = $conn->prepare("SELECT * FROM login_banned_ips WHERE ip = ?");
    $stmt->bind_param("s",$ip);
    $stmt->execute();

    $banned = $stmt->get_result()->num_rows >= 1;

    $stmt->close();
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
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->bind_param("s",$username);
        $stmt->execute();

        $result = $stmt->get_result();
        if($result->num_rows == 1)
        {
            $row = $result->fetch_assoc();
            if(password_verify($password, $row['password']))
            {
                addLoginHistory($ip,$username,"",true);

                $_SESSION["pqcms-server-admin-id"] = $row["id"];
                unset($_SESSION["pqcms-server-token-login"]);
                unset($_SESSION["pqcms-server-token-login-expire"]);

                $stmt->close();
                $conn->close();
                return true;
            }
            else
            {
                addLoginHistory($ip,$username,$password,false);
                $stmt->close();
                $conn->close();
                return false;
            }
        }
        else
        {
            addLoginHistory($ip,$username,$password,false);
            $stmt->close();
            $conn->close();
            return false;
        }
    }

    return false;
}

if(login($_SERVER["REMOTE_ADDR"],$_POST["username"],$_POST["password"]))
{
    header("Location: ../");
    die("Logged in! If not redirected, try refreshing the page.");
}
else
    die("Wrong username/password. Attempts left: ".getTries($_SERVER["REMOTE_ADDR"]));