<?php

header('Content-Type: application/json; charset=utf-8');

require_once(dirname(__DIR__, 3) . "/utils/APIUtils.php");
APIUtils::validatePostForAuthKey($_POST,basename(__FILE__));

$website = APIUtils::getSafeWebsite($_POST);
$ranks = $website->getRanks();
if($ranks["suc"] == 0)
    die(json_encode($ranks,JSON_UNESCAPED_UNICODE));

$responseRanks = $ranks["resp"];
echo json_encode(["suc" => 1, "resp" => $responseRanks],JSON_UNESCAPED_UNICODE);