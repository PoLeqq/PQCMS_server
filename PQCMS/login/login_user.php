<?php
    require_once("../config/JSONLogin.php");
    require_once("../utils/database/Database.inc.php");

    session_start();

    $login = new JSONLogin();
    $attempts = $login->getAttempts();

    function logInUser($username,$password)
    {

        // długość bana
//        $banTimeSeconds = $GLOBALS["login"]->getBanTime();



//        // czy użytkownik jest zbanowany i czy minął czas bana
//        if(($banned = fetchAssocWhere($conn,"login_bans_ip","ip",$_SERVER["REMOTE_ADDR"])) != NULL){
//            if(strtotime("now") - strtotime($banned["blocked"]) < $banTimeSeconds)
//            {
//                // funkcja zapisujące logowanie
//                insertInto($conn,"login_logs","login","ip",$username,$_SERVER["REMOTE_ADDR"]);
//
//                $conn->close();
//                return "Przekroczono ilość dozwolonych prób logowania, proszę spróbować później";
//            }
//
//            // usunięcie ip z tabeli
//            deleteRowWhere($conn,"login_bans_ip","id",$banned["id"]);
//
//            $_SESSION["loginAmount"] = $GLOBALS["attempts"];
//        }
//
//        $username = trim($username);
//        $password = trim($password);
//
//        if($password == "" || $username == "") {
//            afterLogIn($conn,$username, false);
//            return "Wszystkie pola są wymagane";
//        }
//
//        // filtrowanie danych
//        $username = filter_string($username);
//        $password = filter_string($password);
//
//        // pobieranie danych z bazy po nazwie użytkownika
//        $data = fetchAssocWhere($conn,"users","nickname",$username);
//
//        if($data == NULL){
//            afterLogIn($conn,$username, false);
//            return "Nieprawidłowa nazwa użytkownika lub hasło";
//        }
//
//        // weryfikacja hasła
//        if(!password_verify($password,$data["password"])){
//            afterLogIn($conn,$username, false);
//            return "Nieprawidłowa nazwa użytkownika lub hasło";
//        } else {
//            afterLogIn($conn,$username, true);
//        }
    }

    /** Filtrowanie stringu z niebezpiecznych znaków:
     *  -usuwanie znaków pustych (bajtów zerowych)
     *  -zamiana znaków <>& na kod HTML
     *  -zamiana znaków '" na kod HTML
     */
    function filter_string($string)
    {
        $str = preg_replace('/\x00/', '', $string);                 //usuwanie znaków pustych
        $str = filter_var($str,FILTER_SANITIZE_SPECIAL_CHARS);      //znaki <,>,& na kod HTML
        $str = str_replace(["'", '"'], ['&#39;', '&#34;'], $str);   // znaki '," na kod HTML
        return $str;
    }

    /** Operacje wykonywane po próbie zalogowania
     */
    function afterLogIn($conn,$username, $succesful)
    {
        //funkcja zapisująca logi logowań
        insertInto($conn,"login_logs","login","ip","successful",$username,$_SERVER["REMOTE_ADDR"],$succesful);

        if(!$succesful) {
            $_SESSION["loginAmount"]--;

            if($_SESSION["loginAmount"] == 0)
            {
                //dodanie adresu ip użytkownika do tabeli (zbanowanie)
                insertInto($conn,"login_bans_ip","ip",$_SERVER["REMOTE_ADDR"]);
            }
        } else {
            $_SESSION["loginAmount"] = $GLOBALS["attempts"];
            $_SESSION["user"] = $username;
            header("Location: ../checkLicense.php");
        }

        $conn->close();
    }