<?php
// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if the address ID is provided in the request
    if (isset($_POST["A_ID"])) {
        // Include your database connection
        include("db_connection.php");

        // Sanitize the address ID to prevent SQL injection
        $addressId = mysqli_real_escape_string($con, $_POST["A_ID"]);

        // Prepare the update statement to set Address_status to 0
        $query = "UPDATE `address` SET Address_status = 0 WHERE A_ID = ?";
        $stmt = mysqli_prepare($con, $query);

        // Bind the address ID parameter
        mysqli_stmt_bind_param($stmt, "i", $addressId);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            // Address status updated successfully
            echo "Address status updated successfully.";
        } else {
            // Error updating address status
            echo "Error updating address status: " . mysqli_error($con);
        }

        // Close the statement
        mysqli_stmt_close($stmt);

        // Close the database connection
        mysqli_close($con);
    } else {
        // Address ID not provided in the request
        echo "Address ID not provided.";
    }
} else {
    // Invalid request method
    echo "Invalid request method.";
}
?>
