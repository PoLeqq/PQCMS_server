<?php

    require_once("updateData.php");
    echo @updateDatabase($_POST["host"],$_POST["user"],$_POST["password"]);