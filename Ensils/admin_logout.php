<?php
session_start();

// Destroy admin session
unset($_SESSION['admin']);

// Redirect to admin login
header("Location: admin_login.php");
exit();
?>

