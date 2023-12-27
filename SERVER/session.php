<?php
header("content-type: application/json");
session_start();
//session_destroy();
echo json_encode($_SESSION,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);