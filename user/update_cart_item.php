<?php
// Include your database connection code here
include('db_connection.php');

// Check if the new quantity, new subtotal, and cart item ID are set
if (isset($_POST['newQty']) && isset($_POST['newSubtotal']) && isset($_POST['cartItemId'])) {
    // Sanitize the input data
    $newQty = intval($_POST['newQty']);
    $newSubtotal = floatval($_POST['newSubtotal']);
    $cartItemId = intval($_POST['cartItemId']);

    echo "Cart Item is: $cartItemId";


// Prepare and execute the update query
$updateQtyQuery = "UPDATE `cart_item` SET `Qty` = ?, `sub_price` = ? WHERE `CI_ID` = ?";
$updateQtyStmt = mysqli_prepare($con, $updateQtyQuery);

if ($updateQtyStmt) {
    mysqli_stmt_bind_param($updateQtyStmt, "idi", $newQty, $newSubtotal, $cartItemId);
    if (mysqli_stmt_execute($updateQtyStmt)) {
        // Quantity and subtotal updated successfully
        echo "Quantity and subtotal updated successfully.";
        // Redirect to another page if needed
    } else {
        // Error updating quantity and subtotal
        echo "Error: " . mysqli_stmt_error($updateQtyStmt);
    }
    mysqli_stmt_close($updateQtyStmt);
} else {
    // Error preparing update statement
    echo "Error: " . mysqli_error($con);
}

} else {
    // If new quantity, new subtotal, or cart item ID is not set, return an error
    echo "Error: New quantity, new subtotal, or cart item ID not provided.";
}
?>
