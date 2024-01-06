<?php

header('Content-Type: application/json; charset=utf-8');

require_once(dirname(__DIR__, 2) . "/utils/APIUtils.php");
APIUtils::validatePost($_POST,basename(__FILE__));

require_once(dirname(__DIR__,3)."/objects/website/WebsitePermissions.php");
echo json_encode(["suc" => 1, "perms" => WebsitePermissions::getPermissionsDescriptions()],JSON_UNESCAPED_UNICODE);