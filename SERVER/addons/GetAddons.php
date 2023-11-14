<?php
    if(empty($_POST["domain"]) || empty($_POST["username"]) || empty($_POST["license-key"]))
        die(json_encode(["err" => "Wrong posts!"]));

