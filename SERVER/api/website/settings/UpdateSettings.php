<?php

header('Content-Type: application/json; charset=utf-8');
require_once(dirname(__DIR__,2)."/utils/APIUtils.php");

APIUtils::validatePostForAuthKey($_SERVER["REMOTE_ADDR"],basename(__FILE__, '.php'),$_POST);

if((!isset($_POST["login_count"]) && empty($_POST["login_count_reset"])) ||
    (!isset($_POST["login_session_time"]) && empty($_POST["login_session_time_reset"])))
    die(json_encode(["suc" => 0, "desc" => "Uzupełnij wszystkie pola!"],JSON_UNESCAPED_UNICODE));

require_once(dirname(__DIR__,3)."/objects/Website.inc.php");
$website = APIUtils::getWebsite($_SERVER["REMOTE_ADDR"],$_POST);

$apiFields = [
    "login_count","login_count_reset",
    "login_session_time","login_session_time_reset"
];
$fields = [];
$fieldValidators = [
    "i(0-255)","i(0-1)",
    "i(60-3600)","i(0-1)"
];

$sendingFieldValidators = [];
$i = 0;
foreach ($apiFields as $apiField)
{
    if(isset($_POST[$apiField]))
    {
        $fields[] = $_POST[$apiField];
        $sendingFieldValidators[] = $fieldValidators[$i];
    }
    else
        unset($apiFields[$apiField]);
    $i++;
}

$validatorResponse = Validator::validate($fields,$sendingFieldValidators);
if($validatorResponse["suc"] == 0)
    APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,["suc" => 0, "desc" => "Walidacja nie powiodła się dla pola \"{$apiFields[$validatorResponse["element_index"]]}\""]);
//    APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,["suc" => 0, "desc" => "Walidacja nie powiodła się dla pola \"{$apiFields[$validatorResponse["element_index"]]}\"", "dev_msg" => $validatorResponse["desc"]]);

require_once(dirname(__DIR__,3)."/objects/website/AuthKey.inc.php");
$settings = $website->getSettings();

$responseChanged = [];
// Jeśli user ma permisje, zmień podane dane
{
    if(!empty($_POST["login_count"]) || !empty($_POST["login_count_reset"]))
    {
        if($website->hasPermission($_POST["client_ip"], $_POST["auth_key"], "pqcms.settings.login_count.set"))
        {
            if(!empty($_POST["login_count_reset"]))
                $settings->setLoginAttempts(null);
            else
                $settings->setLoginAttempts($_POST["login_count"]);
            $responseChanged["login_count"] = 1;
        }
        else
            $responseChanged["login_count"] = 0;
    }

    if(!empty($_POST["login_count"]) || !empty($_POST["login_count_reset"]))
    {
        if($website->hasPermission($_POST["client_ip"], $_POST["auth_key"], "pqcms.settings.login_session_time.set"))
        {
            if(!empty($_POST["login_session_time_reset"]))
                $settings->setLoginSessionTime(null);
            else
                $settings->setLoginSessionTime($_POST["login_session_time"]);
            $responseChanged["login_session_time"] = 1;
        }
        else
            $responseChanged["login_session_time"] = 0;
    }
}

$response = ["suc" => 1, "desc" => "Zmieniono ustawienia strony!", "changed" => $responseChanged];
APIUtils::endAPIscript(basename(__FILE__, '.php'),$_POST,$response);