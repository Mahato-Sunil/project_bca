<?php
// Start the session
session_start();

// Check if the user is already logged in by verifying session variables
if (isset($_SESSION['admin_username']) && isset($_SESSION['admin_role']) && isset($_SESSION['admin_key'])) {
    $key = $_SESSION['admin_key'];
    $role = $_SESSION['admin_role'];

    // Admin is logged in, redirect to the admin dashboard
    if ($role === 'admin') {
        if ($_SERVER['REQUEST_URI'] !== "/Dashboard/admin-dashboard.php?key={$key}") {
            $locationNext = "admin-dashboard.php?key={$key}";
            header("Location: ../Dashboard/$locationNext");
            exit();
        }
    } else {
        error_log("Unexpected role: " . $role);
        exit('Unexpected role.');
    }
}

if (isset($_SESSION['user_username']) && isset($_SESSION['user_role']) && isset($_SESSION['user_key'])) {
    $key = $_SESSION['user_key'];
    $role = $_SESSION['user_role'];

    // User is logged in, redirect to the user dashboard
    if ($role === 'user') {
        if ($_SERVER['REQUEST_URI'] !== "/Dashboard/user-dashboard.php?key={$key}") {
            $locationNext = "user-dashboard.php?key={$key}";
            header("Location: ../Dashboard/$locationNext");
            exit();
        }
    } else {
        error_log("Unexpected role: " . $role);
        exit('Unexpected role.');
    }
}

// If neither admin nor user session is set, log an error
error_log("User not logged in or missing session variables.");
