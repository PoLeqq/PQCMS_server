<?php

    header("Content-Type: application/json; charset=utf-8");

    $x = 50;
    $y = 100;
    echo json_encode(["waypoint" => [
        "x" => $x, 
        "y" => $y
    ]]);