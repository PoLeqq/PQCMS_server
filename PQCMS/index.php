<?php

session_start();
if(isset($_SESSION['user'])) header('Location: panel/');
else header('Location: login/');