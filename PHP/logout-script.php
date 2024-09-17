<?php
// Start session if not already started
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the login page or any other appropriate page after logout
header("Location: ../");
header('Cache-Control: no-cache');
exit();
