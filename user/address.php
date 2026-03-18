<?php
session_start();

// Check if the session variable is set
if(isset($_SESSION['selected_address_id'])) {
    // Retrieve the selected address ID
    $selectedAddressId = $_SESSION['selected_address_id'];
    // Display the selected address ID
    echo "Selected Address ID: " . $selectedAddressId;
} else {
    // If the session variable is not set, handle the case accordingly
    echo "No address selected.";
}
?>
