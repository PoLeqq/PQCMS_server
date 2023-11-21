<?php

header("Content-type: application/json; charset=utf-8");
require_once(dirname(__DIR__)."/Communicator.inc.php");

$verifyLicense = Communicator::communicate(CommunicateURL::VERIFY_LICENSE,[]);
unset($verifyLicense["expiry_date"]);

print_r($verifyLicense);