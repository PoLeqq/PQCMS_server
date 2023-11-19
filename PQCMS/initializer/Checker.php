<?php

function isFirstTime(): ?bool
{
    require_once(dirname(__DIR__) . "/Communicator.inc.php");
    $adminExists = Communicator::communicate(CommunicateURL::DOES_ADMIN_EXISTS,[]);
    if($adminExists["suc"] == 0)
        return null;
    return !$adminExists["resp"];
}