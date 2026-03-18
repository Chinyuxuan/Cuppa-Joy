<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include("db_connection.php");
// Check if the address ID is passed via POST
// Unset the session variable
unset($_SESSION['selected_address_id']);
echo 'Session variable unset successfully.';
?>
