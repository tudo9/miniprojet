<?php
session_start();

// clear all session variables
$_SESSION = array();

// finish the session
session_destroy();
// save info of the logged out admin in session variables
$_SESSION['logged_out'] = true;

// go to login page
header("Location: login.php");
exit();
?>