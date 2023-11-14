<?php

    /**
     * Zwraca błąd w formie JSON
     * $name - nazwa (opis) błędu
     */
    function getError($name) {
        return json_encode(["err" => $name]);
    }

    /**
     * Zwraca wiadomość o sukcesie w formie JSON
     * $name - nazwa (opis) czynności
     */
    function getSuccess($name) {
        return json_encode(["succ" => $name]);
    }

    if(!isset($_POST["name"]))
    {
        echo getError("Nie podano nazwy rangi");
        exit(0);
    }
    if(!isset($_POST["parent"]))
    {
        echo getError("Nie podano rodzica rangi (czy jesteś tu poprzez bezpośredni link?)");
        exit(0);
    }

    require_once "../../../../hr/Rank.php";
    $rank = new Rank(getNextRankId(),$_POST["name"],null,(int) $_POST["priority"],(int) $_POST["parent"]);
    $errno = $rank->save();
    if($errno == 0)
    {
        echo '<script>';
        echo 'console.log(window.parent.parent);';
        echo 'window.parent.parent.refreshHRFrame();';
        echo '</script>';
        echo "test";
        // header("location: ./");
        
        exit(0);
    }
    else
        echo $errno;