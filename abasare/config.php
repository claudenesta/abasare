<?php
//  This is your new /public_html/config.php file

// Define a constant for the root path of your project. This is always reliable.
define('ROOT_PATH', __DIR__);

// Start the session only once, right here.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include all your essential libraries using the absolute root path.
// This will now work from anywhere in your project.
require_once(ROOT_PATH . '/DBController.php');
require_once(ROOT_PATH . '/lib/db_function.php'); // Assuming you have this file
?>