<?php
require_once("include/config.php");
require_once("include/session_check.php");

$_SESSION['error'] = null;
$_SESSION['success'] = null;
$_SESSION['username'] = null;
$_SESSION['user_id'] = null;

$_SESSION['success'] = "Successfully logged out";

header('location:index.php');
?>