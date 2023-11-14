<?php

    require_once("updateData.php");
    echo @updateElectroCMS($_POST["user"],$_POST["licenseKey"]);