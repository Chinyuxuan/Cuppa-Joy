<?php
include("db_connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['wishlist_item_id'])) {
    // Sanitize input
    $wishlistItemId = mysqli_real_escape_string($con, $_POST['wishlist_item_id']);
    
    // Delete the wishlist item from the database
    $deleteQuery = "DELETE FROM wishlist_item WHERE WI_ID = '$wishlistItemId'";
    if (mysqli_query($con, $deleteQuery)) {
        echo "Wishlist item deleted successfully";
    } else {
        echo "Error deleting wishlist item: " . mysqli_error($con);
    }
} else {
    echo "Invalid request";
}
?>
