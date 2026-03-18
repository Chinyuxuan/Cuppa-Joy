
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include("db_connection.php");
// Clear session data on logout

    // Destroy the session
    session_destroy();

	
    
    // Redirect to the sign-in page or any other desired page
	header("Location: sign-in.php");
    exit;


// Close the database connection
mysqli_close($con);
?>