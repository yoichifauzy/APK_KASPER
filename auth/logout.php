<?php
session_start(); // Start the session to access session variables

// Unset all of the session variables
$_SESSION = array();

// Destroy the session.
session_destroy();

// Redirect to the landing page or login page
header("Location: ../landingpage.php?status=logout_success");
exit();
?>