<?php
	include("db_connection.php");
	session_start();


// Default customer ID for guest user
$defaultCustomerId = "guest";

if (!isset($_SESSION["customer_id"])){
    header("location:sign-in.php");
    exit;
}

// Check if user is signed in
$isSignedIn = isset($_SESSION["customer_id"]);
// Set customer ID to default if not signed in
$currectuser = $isSignedIn ? $_SESSION["customer_id"] : $defaultCustomerId;
///--------------------------------------------------------------------------------------------------
// Retrieve user's information if signed in
if ($isSignedIn) {
    $sql = "SELECT * FROM `customer` WHERE C_ID = ?";
    $stmtUser = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmtUser, "s", $currectuser);
    mysqli_stmt_execute($stmtUser);
    $gotResult = mysqli_stmt_get_result($stmtUser);

    if ($gotResult && mysqli_num_rows($gotResult) > 0) {
        $row = mysqli_fetch_array($gotResult);
        $firstname = $row['C_Firstname'];
        $lastname = $row['C_Lastname'];
        $phno = $row['C_ContactNumber'];
        $Email = $row['C_Email'];
     //   $bod = $row['C_DOB'];
        $password = $row['C_PW'];
    }
}
//---------------------------------------------------------------------------------------------
//start for wishlist
$gotWish = "SELECT * FROM `wishlist` WHERE C_ID = $currectuser ";
$gotWishStmt = mysqli_query($con, $gotWish);
$wish = mysqli_fetch_assoc($gotWishStmt);
$wishID = $wish['W_ID'];

$loadWish = "SELECT * FROM `wishlist_item` WHERE W_ID = $wishID";
$loadWishStmt = mysqli_query($con, $loadWish);

//---------------------------------------------------------------------------------------------
// load the customization for the product
// Check if the product ID is provided in the URL
if (isset($_POST['product_id'])) 
{
    // Sanitize the input to prevent SQL injection
    $productID = mysqli_real_escape_string($con, $_POST['product_id']);

    // Query to fetch the details of the selected product, including customize_status column
    $query = "SELECT *, Customize_Status FROM `product` WHERE `P_ID` = $productID";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        // Fetch product details
        $productDetails = mysqli_fetch_assoc($result);

        // Check if customization is available
        if ($productDetails['Customize_Status'] == 'yes') {
            // Query to fetch options for the product
            $query2 = "SELECT opt.*, cc.CC_Group, cc.Compulsory_Status
            FROM `opt` AS opt 
            INNER JOIN `customize_category` AS cc ON opt.CC_ID = cc.CC_ID 
            WHERE opt.`P_ID` = $productID
            ORDER BY opt.CC_ID ASC";
            $result2 = mysqli_query($con, $query2);

            // Fetch options if they exist
            $options = array();
            if (mysqli_num_rows($result2) > 0) {
                while ($row = mysqli_fetch_assoc($result2)) {
                    $options[] = $row;
                }
            }

            // Query to fetch customization records based on CC_ID
            $customizationQuery = "SELECT * FROM customization WHERE CC_ID = ? AND available_status = 'Available'";
            $customizationStmt = mysqli_prepare($con, $customizationQuery);

            if ($customizationStmt) {
                foreach ($options as $option) {
                    $ccID = $option['CC_ID'];
                    mysqli_stmt_bind_param($customizationStmt, "i", $ccID);
                    mysqli_stmt_execute($customizationStmt);
                    $customizationResult = mysqli_stmt_get_result($customizationStmt);
                    $customizationRecords[$ccID] = mysqli_fetch_all($customizationResult, MYSQLI_ASSOC);
                }
                mysqli_stmt_close($customizationStmt);
            } else {
                echo "Error in preparing statement: " . mysqli_error($con);
            }
        } else {
            // If customization is not available
            $options = array(); // Reset options array
            $customizationRecords = array(); // Reset customization records array
            //echo "<p><strong>This product does not have any customization options available.</strong></p>";
        }
    } else {
        // Product not found
       // echo "Product not found.";
    }
} else {
    // Product ID not provided in the URL
   // echo "Product not provided.";
}

//-----------------------------------------------------------------------------------------------------
//start for cart item and details tables
/*add product to cart*/
    $gotCart = "SELECT * FROM `cart` WHERE C_ID = $currectuser AND C_Status='No-paid'";
	$gotCartStmt = mysqli_query($con,$gotCart);
	$cart= mysqli_fetch_assoc($gotCartStmt);
	$userID = $cart['CT_ID'];

    // Query to check if any customization options are available for the product in the option table
    $optionCheckQuery = "SELECT COUNT(*) AS option_count FROM `opt` WHERE `P_ID` = ?";
    $optionCheckStmt = mysqli_prepare($con, $optionCheckQuery);
    mysqli_stmt_bind_param($optionCheckStmt, "i", $productID);
    mysqli_stmt_execute($optionCheckStmt);
    $optionCheckResult = mysqli_stmt_get_result($optionCheckStmt);
    $optionCount = mysqli_fetch_assoc($optionCheckResult)['option_count'];

// Check if the product customize status is 'no'
if (isset($productDetails['Customize_Status']) && $productDetails['Customize_Status'] === 'no' || $optionCount == 0) 
{
        // Check if the form data for adding to the cart is submitted
        if ($_SERVER['REQUEST_METHOD'] == "POST"&&isset($_POST['add_to_cart'])) 
        {

            // Check if the total quantity in the cart exceeds 12
            $totalQuantityQuery = "SELECT SUM(Qty) AS total_quantity FROM cart_item WHERE CT_ID = ?";
            $totalQuantityStmt = mysqli_prepare($con, $totalQuantityQuery);
            mysqli_stmt_bind_param($totalQuantityStmt, "i", $userID);
            mysqli_stmt_execute($totalQuantityStmt);
            $totalQuantityResult = mysqli_stmt_get_result($totalQuantityStmt);
            $totalQuantity = mysqli_fetch_assoc($totalQuantityResult)['total_quantity'];

            // Get the quantity to be added to the cart
            $quantityToAdd = intval($_POST['qty']);

            // Calculate the total quantity after adding the new product
            $totalQuantityAfterAddition = $totalQuantity + $quantityToAdd;

            // Check if the total quantity after addition exceeds 12
            if ($totalQuantityAfterAddition > 12) 
            {
                // Total quantity exceeds 12, so show an alert message
                echo "
                <script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        swal({
                            title: 'Limit Exceeded',
                            text: 'You cannot add more than 12 items to the cart.',
                            icon: 'error',
                            button: 'OK'
                        }).then(function() {
                            // Redirect to a different page to prevent the alert from popping up again
                            window.location.href = 'wishlist.php';
                        });
                    });
                </script>";
            }
            else 
            {
                // Check if the product exists in the cart_item table
                $existingCartItemQuery = "SELECT * FROM cart_item WHERE CT_ID = ? AND P_ID = ?";
                $existingCartItemStmt = mysqli_prepare($con, $existingCartItemQuery);
                mysqli_stmt_bind_param($existingCartItemStmt, "ii", $userID, $productID);
                mysqli_stmt_execute($existingCartItemStmt);
                $existingCartItemResult = mysqli_stmt_get_result($existingCartItemStmt);

                if ($existingCartItemResult && mysqli_num_rows($existingCartItemResult) > 0) 
                {
                    // Product exists in the cart_item table, so set $cartItemExists to true
                    $cartItemExists = true;

                    // Fetch the existing cart item data
                    $existingCartItemData = mysqli_fetch_assoc($existingCartItemResult);
                    $existingCartItemId = $existingCartItemData['CI_ID'];

                    // Fetch the old quantity from the cart item
                    $oldQty = $existingCartItemData['Qty']; // Use the fetched quantity

                    // Calculate the new quantity
                    $newQty = intval($oldQty) + intval($_POST['qty']);

                    // Calculate the new subtotal
                    $newSubtotal = $newQty * $productDetails['P_Price'];

                    // Generate JavaScript code to handle the update
                    echo "
                    <script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
                    <script type='text/javascript'> 
                        document.addEventListener('DOMContentLoaded', function() {
                            swal({
                                title: 'Are you sure?',
                                text: 'Are you sure you want to add the same item?',
                                icon: 'warning',
                                buttons: {
                                    cancel: {
                                        text: 'Cancel',
                                        value: null,
                                        visible: true,
                                        className: 'swal-button swal-button--cancel', // Custom class for the Cancel button
                                        closeModal: true,
                                    },
                                    confirm: {
                                        text: 'Confirm',
                                        value: true,
                                        visible: true,
                                        className: 'swal-button swal-button--confirm', // Custom class for the Confirm button
                                        closeModal: true,
                                    },
                                },
                            }).then((confirmation) => {
                                if (confirmation) {
                                    // Update the quantity and subtotal directly in the database
                                    var newQty = " . json_encode($newQty) . ";
                                    var newSubtotal = " . json_encode($newSubtotal) . ";
                                    var cartItemId = " . json_encode($existingCartItemId) . ";
                                    
                                    var xhr = new XMLHttpRequest();
                                    xhr.open('POST', 'update_cart_item.php');
                                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                                    xhr.onload = function() {
                                        if (xhr.status === 200) {
                                            // Quantity updated successfully
                                            console.log('Quantity updated successfully:', xhr.responseText);
                                            window.location.href = 'cart.php';
                                        } else {
                                            // Error updating quantity
                                            console.error('Error updating quantity:', xhr.statusText);
                                        }
                                    };
                                    xhr.send('newQty=' + newQty + '&newSubtotal=' + newSubtotal + '&cartItemId=' + cartItemId);
                                } else {
                                    // If user cancels, do nothing (prevent form submission)
                                }
                            });
                        });
                    </script>";
                    
                }
                else
                {
                    // Insert the product into the cart_item table
                    // Check if quantity is provided and numeric
                    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['qty']) && is_numeric($_POST['qty'])) 
                    {
                        // Get quantity from the form
                        $quantity = intval($_POST['qty']);

                        // Calculate subtotal
                        $subtotal = $quantity * $productDetails['P_Price'];

                        $insertQuery = "INSERT INTO cart_item (CT_ID, P_ID, Qty, sub_price) VALUES (?, ?, ?, ?)";
                        $insertStmt = mysqli_prepare($con, $insertQuery);

                        if ($insertStmt) 
                        {
                            // Bind parameters and execute the insert statement
                            mysqli_stmt_bind_param($insertStmt, "iiid", $userID, $productID, $quantity, $subtotal);
                            if (mysqli_stmt_execute($insertStmt)) {
                                // Get the ID of the newly inserted cart item
                                $cartItemId = mysqli_insert_id($con);

                                // Proceed to insert customizations into the details table
                                if (isset($_POST['customizations'])) {
                                    // Flatten the array if necessary
                                    $customizations = [];
                                    foreach ($_POST['customizations'] as $customizationId) {
                                        if (is_array($customizationId)) {
                                            $customizations = array_merge($customizations, $customizationId);
                                        } else {
                                            $customizations[] = $customizationId;
                                        }
                                    }
                                    foreach ($customizations as $customizationId) {
                                        // Check if a customization ID is selected
                                        if (!empty($customizationId)) {
                                            // Insert the selected customization into the detail table
                                            $insertDetail = "INSERT INTO `details` (customize_id, c_item_id) VALUES (?, ?)";
                                            $insertDetailStmt = mysqli_prepare($con, $insertDetail);

                                            // Check if statement preparation was successful
                                            if ($insertDetailStmt) {
                                                mysqli_stmt_bind_param($insertDetailStmt, "ii", $customizationId, $cartItemId);
                                                if (mysqli_stmt_execute($insertDetailStmt)) {
                                                    // Success
                                                //   echo "Customization added to cart successfully.";
                                                } else {
                                                    // Error in executing the statement
                                                    echo "Error: " . mysqli_stmt_error($insertDetailStmt);
                                                }
                                                mysqli_stmt_close($insertDetailStmt);
                                            } else {
                                                // Error in preparing the statement
                                                echo "Error in preparing statement: " . mysqli_error($con);
                                            }
                                        }
                                    }
                                } else {
                                    //echo "No customizations chosen.";
                                }
                                // Redirect to cart page
                            header("Location: cart.php");
                            exit();
                                // Success
                            //  echo "Product added to cart successfully.";
                                //header("Location:index.php");
                                //exit();
                            } else {
                                // Error in executing the statement
                                echo "Error: " . mysqli_stmt_error($insertStmt);
                            }
                            mysqli_stmt_close($insertStmt);
                        } else {
                            // Error in preparing the statement
                            echo "Error: " . mysqli_error($con);
                        }
                    } else {
                        // Quantity not provided or not numeric
                    // echo "Invalid quantity.";
                    }
                
                }
            }
        }
        
    
} 
else
{   // Check if the total quantity of items in the cart exceeds 12
    // Check if the total quantity of items in the cart exceeds 12
    $totalQuantityQuery = "SELECT SUM(Qty) AS totalQuantity FROM cart_item WHERE CT_ID = ?";
    $totalQuantityStmt = mysqli_prepare($con, $totalQuantityQuery);
    mysqli_stmt_bind_param($totalQuantityStmt, "i", $userID);
    mysqli_stmt_execute($totalQuantityStmt);
    $totalQuantityResult = mysqli_stmt_get_result($totalQuantityStmt);

    if ($totalQuantityResult) 
    {
        $totalQuantityRow = mysqli_fetch_assoc($totalQuantityResult);
        $totalQuantity = $totalQuantityRow['totalQuantity'];
        
        // Check if adding the new product will exceed the limit of 12 items
        if (isset($_POST['qty']) && is_numeric($_POST['qty']) && ($totalQuantity + intval($_POST['qty']) > 12)) 
        {
            // Alert the user that adding more items would exceed the limit
            echo "
            <script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    swal({
                        title: 'Limit Exceeded',
                        text: 'You cannot add more than 12 items to the cart.',
                        icon: 'warning',
                        button: 'OK'
                    });
                });
            </script>";
        } 
        else 
        {
            // Proceed with adding the product to the cart
            // Check if the product exists in the cart_item table
            $existingCartItemQuery = "SELECT ci.CI_ID, ci.Qty, ci.sub_price, d.customize_id
            FROM cart_item ci
            INNER JOIN details d ON ci.CI_ID = d.c_item_id
            WHERE ci.CT_ID = ? AND ci.P_ID = ?";

            $existingCartItemStmt = mysqli_prepare($con, $existingCartItemQuery);
            mysqli_stmt_bind_param($existingCartItemStmt, "ii", $userID, $productID);
            mysqli_stmt_execute($existingCartItemStmt);
            $existingCartItemResult = mysqli_stmt_get_result($existingCartItemStmt);

            $existingCustomizations = [];

            if ($existingCartItemResult) 
            {
                // Check if the query executed successfully
                if (mysqli_num_rows($existingCartItemResult) > 0) {
                    // Loop through existing cart items to fetch customizations
                    while ($row = mysqli_fetch_assoc($existingCartItemResult)) {
                        $existingCustomizations[$row['CI_ID']][] = $row['customize_id'];
                        $existingCartItemData[$row['CI_ID']] = $row; // Store the existing cart item data
                    }

                    // Debug: Display existing customizations
                    //echo "Existing Customizations: ";
                // var_dump($existingCustomizations);

                    // Flatten $_POST['customizations'] array if necessary
                    $flattenedCustomizations = [];
                    if (isset($_POST['customizations'])) {
                        foreach ($_POST['customizations'] as $customizationId) {
                            if (is_array($customizationId)) {
                                // If the element is an array, merge it with $flattenedCustomizations
                                $flattenedCustomizations = array_merge($flattenedCustomizations, $customizationId);
                            } else {
                                // If the element is not an array, simply add it to $flattenedCustomizations
                                $flattenedCustomizations[] = $customizationId;
                            }
                        }
                    }

                    // Debug: Display current customizations
                    //echo "Current Customizations: ";
                // var_dump($flattenedCustomizations);

                    // Check if the current product exists in the cart
                    $cartItemExists = false;
                    $existingCartItemId = null; // Store the existing cart item ID
                    foreach ($existingCustomizations as $cartItemId => $customizations) {
                        // Sort the existing customizations for accurate comparison
                        sort($customizations);

                        // Sort the customizations of the current product being added to the cart
                        sort($flattenedCustomizations);

                        // Check if the number of customizations matches
                        if (count($customizations) === count($flattenedCustomizations)) {
                            // Initialize a flag to track if all customizations match
                            $allMatch = true;

                            // Loop through each customization ID and check if it exists in the existing customizations
                            foreach ($flattenedCustomizations as $customizationId) {
                                // Check if the current customization exists in the existing customizations
                                if (!in_array($customizationId, $customizations)) {
                                    // If it doesn't exist, set the flag to false and break the loop
                                    $allMatch = false;
                                    break;
                                }
                            }

                            // If all customizations match, set $cartItemExists to true and store the existing cart item ID
                            if ($allMatch) {
                                $cartItemExists = true;
                                $existingCartItemId = $cartItemId;
                                // Retrieve the cart item data for this cart item ID from the stored array
                                $existingCartItemData = $existingCartItemData[$cartItemId];
                                break;
                            }
                        }
                    }

                    // Debug: Display cart item existence status
                    //echo "Cart Item Exists: " . ($cartItemExists ? "true" : "false");

                    if ($cartItemExists) {
                        // Product with the same set of customizations already exists in the cart
                    // Fetch the old quantity from the cart item
                    $oldQty = $existingCartItemData['Qty'];

                        // Calculate the new quantity
                        $newQty = intval($oldQty) + intval($_POST['qty']);

                        // Initialize new subtotal with base product price
                        $newSubtotal = 0; // Initialize with zero

                        // Fetch selected customization IDs from $_POST['customizations']
                        $flattenedCustomizations = [];
                        if (isset($_POST['customizations'])) {
                            foreach ($_POST['customizations'] as $customizationId) {
                                if (is_array($customizationId)) {
                                    // If the element is an array, merge it with $flattenedCustomizations
                                    $flattenedCustomizations = array_merge($flattenedCustomizations, $customizationId);
                                } else {
                                    // If the element is not an array, simply add it to $flattenedCustomizations
                                    $flattenedCustomizations[] = $customizationId;
                                }
                            }
                        }
                        // Initialize total customization price
                        $totalCustomizationPrice = 0;

                        // Calculate total customization price
                        foreach ($flattenedCustomizations as $customizationId) {
                            // Fetch customization price from database based on $customizationId
                            $customizationPriceFetchQuery = "SELECT Custom_Price FROM customization WHERE Custom_ID = ?";
                            $customizationPriceFetchStmt = mysqli_prepare($con, $customizationPriceFetchQuery);
                            mysqli_stmt_bind_param($customizationPriceFetchStmt, "i", $customizationId);
                            mysqli_stmt_execute($customizationPriceFetchStmt);
                            $customizationPriceResult = mysqli_stmt_get_result($customizationPriceFetchStmt);

                            if ($customizationPriceRow = mysqli_fetch_assoc($customizationPriceResult)) {
                                $totalCustomizationPrice += $customizationPriceRow['Custom_Price'];
                            }

                            // Close statement
                            mysqli_stmt_close($customizationPriceFetchStmt);
                        }

                        // Calculate new subtotal including base price and customization price
                        $newSubtotal = $newQty * ($productDetails['P_Price'] + $totalCustomizationPrice);


                        // Generate JavaScript code to handle the update
                        echo "
                        <script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
                        <script type='text/javascript'> 
                            document.addEventListener('DOMContentLoaded', function() {
                                swal({
                                    title: 'Are you sure?',
                                    text: 'Are you sure you want to add the same item?',
                                    icon: 'warning',
                                    buttons: {
                                        cancel: {
                                            text: 'Cancel',
                                            value: null,
                                            visible: true,
                                            className: 'swal-button swal-button--cancel', // Custom class for the Cancel button
                                            closeModal: true,
                                        },
                                        confirm: {
                                            text: 'Confirm',
                                            value: true,
                                            visible: true,
                                            className: 'swal-button swal-button--confirm', // Custom class for the Confirm button
                                            closeModal: true,
                                        },
                                    },
                                }).then((confirmation) => {
                                    if (confirmation) {
                                        // Update the quantity and subtotal directly in the database
                                        var newQty = parseInt(" . $newQty . "); // Parse as float
                                        var newSubtotal = parseFloat(" . $newSubtotal . "); // Parse as float
                                        var cartItemId = " . $existingCartItemId . ";
                                        
                                        var xhr = new XMLHttpRequest();
                                        xhr.open('POST', 'update_cart_item.php');
                                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                                        xhr.onload = function() {
                                            if (xhr.status === 200) {
                                                // Quantity updated successfully
                                                console.log('Quantity updated successfully:', xhr.responseText);
                                            window.location.href = 'cart.php'; // Redirect to the cart page
                                            } else {
                                                // Error updating quantity
                                                console.error('Error updating quantity:', xhr.statusText);
                                            }
                                        };
                                        xhr.send('newQty=' + newQty + '&newSubtotal=' + newSubtotal + '&cartItemId=' + cartItemId);
                                    } else {
                                        // If user cancels, do nothing (prevent form submission)
                                    }
                                });
                            });
                        </script>";
                                        
            
                    } 
                    else 
                    {
                            // Proceed to insert the product into the cart_item table
                            // Check if quantity is provided and numeric
                            if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['qty']) && is_numeric($_POST['qty'])) {
                                // Get quantity from the form
                                $quantity = intval($_POST['qty']);

                                // Calculate base subtotal (without customizations)
                                $baseSubtotal = $productDetails['P_Price'];

                                // Initialize total customization price
                                $totalCustomizationPrice = 0;

                                // Calculate total customization price
                                if (isset($_POST['customizations'])) {
                                    // Flatten the array if necessary
                                    $flattenedCustomizations = [];
                                    foreach ($_POST['customizations'] as $customizationId) {
                                        if (is_array($customizationId)) {
                                            $flattenedCustomizations = array_merge($flattenedCustomizations, $customizationId);
                                        } else {
                                            $flattenedCustomizations[] = $customizationId;
                                        }
                                    }

                                    // Fetch customization prices from the database
                                    $customizationQuery = "SELECT SUM(Custom_Price) AS totalCustomizationPrice
                                                        FROM customization
                                                        WHERE Custom_ID IN (" . implode(',', array_map('intval', $flattenedCustomizations)) . ")";
                                    $customizationResult = mysqli_query($con, $customizationQuery);

                                    if ($customizationResult) {
                                        $customizationRow = mysqli_fetch_assoc($customizationResult);
                                        $totalCustomizationPrice = $customizationRow['totalCustomizationPrice'];
                                    } else {
                                        // Error fetching customization prices
                                        echo "Error: " . mysqli_error($con);
                                        exit(); // Exit the script or handle error as per your requirement
                                    }
                                }

                                // Calculate final subtotal including customizations
                                $subtotal = ($baseSubtotal + $totalCustomizationPrice) * $quantity;

                                // Prepare and execute insert statement for cart_item
                                $insertQuery = "INSERT INTO cart_item (CT_ID, P_ID, Qty, sub_price) VALUES (?, ?, ?, ?)";
                                $insertStmt = mysqli_prepare($con, $insertQuery);

                                if ($insertStmt) {
                                    // Bind parameters and execute the insert statement
                                    mysqli_stmt_bind_param($insertStmt, "iiid", $userID, $productID, $quantity, $subtotal);
                                    if (mysqli_stmt_execute($insertStmt)) {
                                        // Get the ID of the newly inserted cart item
                                        $cartItemId = mysqli_insert_id($con);

                                        // Proceed to insert customizations into the details table
                                        if (isset($_POST['customizations'])) {
                                            foreach ($flattenedCustomizations as $customizationId) {
                                                // Insert each selected customization into the detail table
                                                $insertDetail = "INSERT INTO `details` (customize_id, c_item_id) VALUES (?, ?)";
                                                $insertDetailStmt = mysqli_prepare($con, $insertDetail);

                                                if ($insertDetailStmt) {
                                                    // Bind parameters and execute the insert statement
                                                    mysqli_stmt_bind_param($insertDetailStmt, "ii", $customizationId, $cartItemId);
                                                    if (mysqli_stmt_execute($insertDetailStmt)) {
                                                        // Customization added to cart successfully
                                                        // echo "Customization added to cart successfully.";
                                                    } else {
                                                        // Error in executing the statement
                                                        echo "Error: " . mysqli_stmt_error($insertDetailStmt);
                                                    }
                                                    mysqli_stmt_close($insertDetailStmt);
                                                } else {
                                                    // Error in preparing the statement
                                                    echo "Error in preparing detail statement: " . mysqli_error($con);
                                                }
                                            }
                                        } else {
                                            // No customizations chosen.
                                            // echo "No customizations chosen.";
                                        }

                                        // Success
                                        // echo "Product added to cart successfully.";
                                        header("Location: cart.php");
                                        exit();
                                    } else {
                                        // Error in executing the statement
                                        echo "Error: " . mysqli_stmt_error($insertStmt);
                                    }
                                    mysqli_stmt_close($insertStmt);
                                } else {
                                    // Error in preparing the statement
                                    echo "Error: " . mysqli_error($con);
                                }
                            } else {
                                // Quantity not provided or not numeric
                                // echo "Invalid quantity.";
                            }

                    }
                } 
                else 
                {
                    // If the product doesn't exist in the cart, insert it
                    // Proceed to insert the product into the cart_item table
                    // Check if quantity is provided and numeric
                    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['qty']) && is_numeric($_POST['qty'])) 
                    {
                        // Get quantity from the form
                        $quantity = intval($_POST['qty']);
                    
                        // Calculate base subtotal (without customizations)
                        $baseSubtotal = $productDetails['P_Price'];
                    
                        // Initialize total customization price
                        $totalCustomizationPrice = 0;
                    
                        // Calculate total customization price
                        if (isset($_POST['customizations'])) 
                        {
                            // Flatten the array if necessary
                            $flattenedCustomizations = [];
                            foreach ($_POST['customizations'] as $customizationId) {
                                if (is_array($customizationId)) {
                                    $flattenedCustomizations = array_merge($flattenedCustomizations, $customizationId);
                                } else {
                                    $flattenedCustomizations[] = $customizationId;
                                }
                            }
                    
                            // Fetch customization prices from the database
                            $customizationQuery = "SELECT SUM(Custom_Price) AS totalCustomizationPrice
                                                FROM customization
                                                WHERE Custom_ID IN (" . implode(',', array_map('intval', $flattenedCustomizations)) . ")";
                            $customizationResult = mysqli_query($con, $customizationQuery);
                    
                            if ($customizationResult) {
                                $customizationRow = mysqli_fetch_assoc($customizationResult);
                                $totalCustomizationPrice = $customizationRow['totalCustomizationPrice'];
                            } else {
                                // Error fetching customization prices
                                echo "Error: " . mysqli_error($con);
                                exit(); // Exit the script or handle error as per your requirement
                            }
                        }
                    
                        // Calculate final subtotal including customizations
                        $subtotal = ($baseSubtotal + $totalCustomizationPrice) * $quantity;
                    
                        // Prepare and execute insert statement for cart_item
                        $insertQuery = "INSERT INTO cart_item (CT_ID, P_ID, Qty, sub_price) VALUES (?, ?, ?, ?)";
                        $insertStmt = mysqli_prepare($con, $insertQuery);
                    
                        if ($insertStmt) 
                        {
                            // Bind parameters and execute the insert statement
                            mysqli_stmt_bind_param($insertStmt, "iiid", $userID, $productID, $quantity, $subtotal);
                            if (mysqli_stmt_execute($insertStmt)) 
                            {
                                // Get the ID of the newly inserted cart item
                                $cartItemId = mysqli_insert_id($con);
                    
                                // Proceed to insert customizations into the details table
                                if (isset($_POST['customizations'])) 
                                {
                                    foreach ($flattenedCustomizations as $customizationId) {
                                        // Insert each selected customization into the detail table
                                        $insertDetail = "INSERT INTO `details` (customize_id, c_item_id) VALUES (?, ?)";
                                        $insertDetailStmt = mysqli_prepare($con, $insertDetail);
                    
                                        if ($insertDetailStmt) {
                                            // Bind parameters and execute the insert statement
                                            mysqli_stmt_bind_param($insertDetailStmt, "ii", $customizationId, $cartItemId);
                                            if (mysqli_stmt_execute($insertDetailStmt)) {
                                                // Customization added to cart successfully
                                                // echo "Customization added to cart successfully.";
                                            } else {
                                                // Error in executing the statement
                                                echo "Error: " . mysqli_stmt_error($insertDetailStmt);
                                            }
                                            mysqli_stmt_close($insertDetailStmt);
                                        } else {
                                            // Error in preparing the statement
                                            echo "Error in preparing detail statement: " . mysqli_error($con);
                                        }
                                    }
                                } else {
                                    // No customizations chosen.
                                    // echo "No customizations chosen.";
                                }
                    
                                // Success
                                // echo "Product added to cart successfully.";
                                header("Location: cart.php");
                                exit();
                            } else {
                                // Error in executing the statement
                                echo "Error: " . mysqli_stmt_error($insertStmt);
                            }
                            mysqli_stmt_close($insertStmt);
                        } else {
                            // Error in preparing the statement
                            echo "Error: " . mysqli_error($con);
                        }
                    } else {
                        // Quantity not provided or not numeric
                        // echo "Invalid quantity.";
                    }
                }
            }
        }
    } 
    else 
    {
        // Error in executing the query
        echo "Error: " . mysqli_error($totalQuantityStmt);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Responsive Bootstrap4 Shop Template, Created by Imran Hossain from https://imransdesign.com/">

	<!-- title -->
	<title>Wishlist - Cuppa Joy</title>

	<!-- favicon -->
	<link rel="shortcut icon" type="image/png" href="assets/img/smile-black.png">
	<!-- google font -->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Poppins:400,700&display=swap" rel="stylesheet">
	<!-- fontawesome -->
	<link rel="stylesheet" href="assets/css/all.min.css">
	<!-- bootstrap -->
	<link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
	<!-- owl carousel -->
	<link rel="stylesheet" href="assets/css/owl.carousel.css">
	<!-- magnific popup -->
	<link rel="stylesheet" href="assets/css/magnific-popup.css">
	<!-- animate css -->
	<link rel="stylesheet" href="assets/css/animate.css">
	<!-- mean menu css -->
	<link rel="stylesheet" href="assets/css/meanmenu.min.css">
	<!-- main style -->
	<link rel="stylesheet" href="assets/css/main.css">
	<!-- responsive -->
	<link rel="stylesheet" href="assets/css/responsive.css">
	<!-- <link rel="stylesheet" href="assets\css\single-prod.css"> -->
	<link rel="stylesheet" href="wishlist.css">

</head>
<body>
	
	<!--PreLoader-->
    <div class="loader">
        <div class="loader-inner">
            <div class="circle"></div>
        </div>
    </div>
    <!--PreLoader Ends-->
	
<!-- header -->
<div class="top-header-area" id="sticker">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 col-sm-12 text-center">
					<div class="main-menu-wrap">
						<!-- logo -->
						<div class="site-logo">
							<a href="index.php">
								<img src="assets\img\full-white.png" alt="">
							</a>
						</div>
						<!-- logo -->

						<!-- menu start -->
						<nav class="main-menu">
							<ul>
								<li ><a href="index.php">Home</a></li>
								<li><a href="shop.php">Menu</a></li>
								<li><a href="promo.php"> Show promo</a></li>
								<li><a href="about.php">About Us</a></li>
								<?php
										if(isset($_SESSION["customer_id"])) {
											echo '<li><a href="history.php">Order History</a></li>';
										} else {
											// Output the link with the ID
											echo '<li id="historyGo"><a href="history.php">History</a></li>';
											// Output JavaScript to show SweetAlert confirmation dialog when the link is clicked
											echo '<script>
												// When the page is loaded
												document.addEventListener("DOMContentLoaded", function() {
													// Get the link element
													var HLink = document.getElementById("historyGo");
													// Add click event listener to the link
													HLink.addEventListener("click", function(event) {
														// Prevent the default link behavior
														event.preventDefault();
														// Show the SweetAlert confirmation dialog
														swal({
															title: "Sign In Required",
															text: "You need to sign in to view your history.",
															icon: "warning",
															buttons: {
																cancel: "Cancel",
																confirm: "Sign In"
															},
														}).then((willSignIn) => {
															// If the user clicks "Sign In", redirect them to the sign-in page
															if (willSignIn) {
																window.location.href = "sign-in.php";
															}
														});
													});
												});
											</script>';
										}
										?>		
	
								<li>
									<div class="header-icons">
									<?php
										if(isset($_SESSION["customer_id"])) {
											echo '<a class="shopping-cart" id="cartLink" href="cart.php"><i class="fas fa-shopping-cart"></i></a>';
										} else {
											// Output the link with the ID
											echo '<a class="shopping-cart" id="cartLink" href="cart.php"><i class="fas fa-shopping-cart"></i></a>';
											// Output JavaScript to show SweetAlert confirmation dialog when the link is clicked
											echo '<script>
												// When the page is loaded
												document.addEventListener("DOMContentLoaded", function() {
													// Get the link element
													var cartLink = document.getElementById("cartLink");
													// Add click event listener to the link
													cartLink.addEventListener("click", function(event) {
														// Prevent the default link behavior
														event.preventDefault();
														// Show the SweetAlert confirmation dialog
														swal({
															title: "Sign In Required",
															text: "You need to sign in to access your cart.",
															icon: "warning",
															buttons: {
																cancel: "Cancel",
																confirm: "Sign In"
															},
														}).then((willSignIn) => {
															// If the user clicks "Sign In", redirect them to the sign-in page
															if (willSignIn) {
																window.location.href = "sign-in.php";
															}
														});
													});
												});
											</script>';
										}
										?>

										<?php
										if(isset($_SESSION["customer_id"])) {
											echo '<a class="shopping-cart wish-heart" href="wishlist.php"><i class="fas fa-heart"></i></a>';
										} else {
											// Output the link with the ID
											echo '<a class="shopping-cart wish-heart" id="wishLink"href="wishlist.php"><i class="fas fa-heart"></i></a>';
											// Output JavaScript to show SweetAlert confirmation dialog when the link is clicked
											echo '<script>
												// When the page is loaded
												document.addEventListener("DOMContentLoaded", function() {
													// Get the link element
													var wishLink = document.getElementById("wishLink");
													// Add click event listener to the link
													wishLink.addEventListener("click", function(event) {
														// Prevent the default link behavior
														event.preventDefault();
														// Show the SweetAlert confirmation dialog
														swal({
															title: "Sign In Required",
															text: "You need to sign in to access your cart.",
															icon: "warning",
															buttons: {
																cancel: "Cancel",
																confirm: "Sign In"
															},
														}).then((willSignIn) => {
															// If the user clicks "Sign In", redirect them to the sign-in page
															if (willSignIn) {
																window.location.href = "sign-in.php";
															}
														});
													});
												});
											</script>';
										}
										?>
										<!--<a class="mobile-hide search-bar-icon" href="#"><i class="fas fa-search"></i></a>--
										<a class="shopping-cart" href="wishlist.php"><i class="fas fa-heart"></i></a>-->
										<?php
											if(isset($_SESSION["customer_id"])) {
												echo '<a class="shopping-cart" href="profile.php">
														<i class="fas fa-user"></i>
														<span id="firstname"> Welcome ' . ($isSignedIn ? htmlspecialchars($firstname) : "Guest") . '</span>
													</a>';
											} else {
												// User is not logged in, do nothing or display alternative content
											}
										?>


										
										<!--<a class="shopping-cart logout" href="logout.php"><i class="fas fa-sign-out-alt"></i>   Log Out</a>-->
										<?php
										if(!isset($_SESSION["customer_id"])) {
											// User is logged in, display logout button
											echo '<a class="shopping-cart signIn" href="sign-in.php"><i class="fas fa-sign-in-alt"></i> Sign In</a>';
										} else {
											// User is not logged in, do nothing or display alternative content
										}
										
										?>
										<?php
										if(!isset($_SESSION["customer_id"])) {
											// User is logged in, display logout button
											echo '<a class="shopping-cart signUp" href="sign-up.php"><i class="fas fa-registered"></i> Sign Up</a>';
										} else {
											// User is not logged in, do nothing or display alternative content
										}
										
										?>
										<?php
										if(isset($_SESSION["customer_id"])) {
											// User is logged in, display logout button
											echo '<a class="shopping-cart logout" href="logout.php" onclick="return confirmLogout();"><i class="fas fa-sign-out-alt"></i> Sign Out</a>';
										} else {
											// User is not logged in, do nothing or display alternative content
										}
										
										?>
									</div>
								</li>
							</ul>
						</nav>
						<!-- <a class="mobile-show search-bar-icon" href="#"><i class="fas fa-search"></i></a> -->
						<div class="mobile-menu"></div>
						<!-- menu end -->
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end header -->

	<!-- search area -->
	<div class="search-area">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<span class="close-btn"><i class="fas fa-window-close"></i></span>
					<div class="search-bar">
						<div class="search-bar-tablecell">
							<h3>Search For:</h3>
							<input type="text" placeholder="Keywords">
							<button type="submit">Search <i class="fas fa-search"></i></button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end search arewa -->
	
	<!-- breadcrumb-section -->
	<div class="breadcrumb-section breadcrumb-bg">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 offset-lg-2 text-center">
					<div class="breadcrumb-text">
						<p>Delightful & Delicious</p>
						<h1>Wishlist</h1>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end breadcrumb section -->

    <!-- Wishlist -->
    <div class="wishlist-table-wrap">
        <div>
            <?php
                //load the wishlist id for the customer
                $gotWish = "SELECT * FROM `wishlist` WHERE C_ID = $currectuser ";
                $gotWishStmt = mysqli_query($con, $gotWish);
                $wish = mysqli_fetch_assoc($gotWishStmt);
                $wishID = $wish['W_ID'];

                //load the wishlist item for this wishlist
                $loadWish = "SELECT * FROM `wishlist_item` WHERE W_ID = $wishID";
                $loadWishStmt = mysqli_query($con, $loadWish);

                // Check if the wishlist items for the given $wishID are empty
                if (mysqli_num_rows($loadWishStmt) > 0) 
                {
                    // Initialize arrays to store wishlist items with 'yes' and 'no' status
                    $yesStatusItems = array();
                    $noStatusItems = array();

                    // Loop over the wishlist items
                    while ($wishlistItemRow = mysqli_fetch_assoc($loadWishStmt)) 
                    {
                        $productId = $wishlistItemRow['P_ID'];
                        // Fetch product details from the product table based on the product ID
                        $productQuery = "SELECT * FROM product WHERE P_ID = $productId";
                        $productResult = mysqli_query($con, $productQuery);
                        $productRow = mysqli_fetch_assoc($productResult);
                        
                        // Check product status and store in corresponding array
                        if ($productRow['P_Status'] == 'yes') {
                            $yesStatusItems[] = $wishlistItemRow;
                        } else {
                            $noStatusItems[] = $wishlistItemRow;
                        }
                    }

                    // Output wishlist items with 'yes' status
                    if (count($yesStatusItems) > 0) 
                    {
                        echo '<div class="row">';
                        $columnCount = 0;
                        foreach ($yesStatusItems as $wishlistItemRow) 
                        {
                            // Display the product
                            $productId = $wishlistItemRow['P_ID'];
                            $productQuery = "SELECT * FROM product WHERE P_ID = $productId";
                            $productResult = mysqli_query($con, $productQuery);
                            $productRow = mysqli_fetch_assoc($productResult);

                            echo '<div class="col-lg-4 col-md-6">';
                            echo '<div class="wishlist-container wishlist-yes">';
                            echo '<div class="wishlist-item">';
                            echo '<div class="wishlist-photo"><a href="single-product.php?product_id=' . $productRow['P_ID'] . '"><img src="../image/product/' . $productRow['P_Photo'] . '" alt=""></a></div>';
                            echo '<div class="wishlist-details">';
                            echo '<h3>' . $productRow['P_Name'] . '</h3>';
                            echo '<p class="wishlist-price">RM ' . number_format($productRow['P_Price'], 2) . '</p>';

                            echo '<a href="#" class="cart-btn view-details-btn" data-product-id="' . $productRow['P_ID'] . '"><i class="fas fa-shopping-cart"></i> View details</a>';
                            echo '<form method="POST" class="delete-form" name="delete_form">';
                            echo '<input type="hidden" name="wishlist_item_id" value="' . $wishlistItemRow['WI_ID'] . '">';
                            echo '<button type="submit" name="delete_btn" class="del-btn"><i class="fas fa-trash"></i> Remove item</button>';
                            echo '</form>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';

                            $columnCount++;
                            // Close and start a new row every 3 items
                            if ($columnCount % 3 == 0) {
                                echo '</div><div class="row">';
                            }
                        }
                        // Close the last row if it's not already closed
                        if ($columnCount % 3 != 0) {
                            echo '</div>';
                        }
                    }
                    //------------------------------------------------------------------------------------------------------------
                    // Output wishlist items with 'no' status
                    if (count($noStatusItems) > 0) 
                    {
                        echo '<div class="no-prod">';
                        echo '<div class="msg-no">';
                        echo '<h2>No available products</h2>';
                        echo '</div>';
                        
                        echo '<div class="row">';
                        $itemCount = 0;
                        foreach ($noStatusItems as $wishlistItemRow) 
                        {
                            // Display the product
                            $productId = $wishlistItemRow['P_ID'];
                            $productQuery = "SELECT * FROM product WHERE P_ID = $productId";
                            $productResult = mysqli_query($con, $productQuery);
                            $productRow = mysqli_fetch_assoc($productResult);
                            
                            echo '<div class="col-lg-4 col-md-6">';
                            echo '<div class="wishlist-container wishlist-no">';
                            echo '<div class="wishlist-item">';
                            echo '<div class="wishlist-photo"><img src="../image/product/' . $productRow['P_Photo'] . '" alt="Product Image"></div>';
                            echo '<div class="wishlist-details">';
                            echo '<h3>' . $productRow['P_Name'] . '</h3>';
                            echo '<p class="wishlist-price">RM ' . number_format($productRow['P_Price'], 2) . '</p>';
                            
                            echo '<form method="POST" class="delete-form">';
                            echo '<input type="hidden" name="wishlist_item_id" value="' . $wishlistItemRow['WI_ID'] . '">';
                            echo '<button type="submit" name="delete_btn" class="del-btn"><i class="fas fa-trash"></i> Remove item</button>';
                            echo '</form>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            
                            $itemCount++;
                            // Close and start a new row every 3 items
                            if ($itemCount % 3 == 0) {
                                echo '</div><div class="row">';
                            }
                        }
                        // Close the last row if it's not already closed
                        if ($itemCount % 3 != 0) {
                            echo '</div>';
                        }
                        echo '</div>';
                    }
                } 
                else 
                {//totally no any wishlist item
                    echo '<div class="empty-list">';
                    echo '<h2>No items in the wishlist</h2>';
                    echo '<img src="assets/img/empty-wishlist.png">';
                    echo '</div>';
                }
            ?>
        </div>
    </div>
    <!-- End Wishlist -->

    <div id="overlay"></div>

        <!--add to cart at wishlist-->
        <section id="addCart">
            <h2>Add to cart
                <span class="modal-close" onclick="closePopup()">&times;</span>

            </h2>
            <form id="addCartForm" name="addCartForm" method="post" onsubmit="return validateForm(event)">

                <input type="hidden" name="product_id" value="<?php echo $productID; ?>">
                <!-- product id is for load the informs for the pop up form -->
                <input type="hidden" name="cart_item_id" value="<?php echo $cartItemId; ?>">
                <!-- for delete the wishlist item from wishlist -->

                <div id="customization-container">
            
                    <?php if (!empty($options)) : ?>
                        <ul class="options-list">
                            <?php 
                            // Separate options based on compulsory status
                            $radioOptions = array_filter($options, function($option) {
                                return $option['Compulsory_Status'] == 'yes';
                            });

                            $checkboxOptions = array_filter($options, function($option) {
                                return $option['Compulsory_Status'] != 'yes';
                            });

                            ?>

                            <div class="row">
                                <?php foreach ($radioOptions as $option) : ?>
                                    <div class="col-md-3">
                                        <h4><?php echo $option['CC_Group']; ?></h4>
                                        <?php if ($option['Compulsory_Status'] == 'yes') : ?>
                                            <span class="text-danger compulsoryError" id="compulsoryError<?php echo $option['CC_ID']; ?>">* Pick one option.</span>
                                        <?php endif; ?>
                                        <?php $customizations = $customizationRecords[$option['CC_ID']]; ?>
                                        <?php foreach ($customizations as $index => $customization) : ?>
                                            <div class="inputGroup">
                                                <input type="radio" id="radio<?php echo $option['CC_ID'] . $index; ?>" name="customizations[<?php echo $option['CC_ID']; ?>]" value="<?php echo $customization['Custom_ID']; ?>" required>
                                                <label for="radio<?php echo $option['CC_ID'] . $index; ?>"><?php echo $customization['Custom_Name']; ?>  <span class="price">+ RM <?php echo number_format($customization['Custom_Price'],2); ?></span></label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <!-- --------------------------------------------------------------------------------------------------------------- -->
                            <div class="row checkbox">
                                <?php foreach ($checkboxOptions as $option) : ?>
                                    <div class="col-md-3">
                                        <h4><?php echo $option['CC_Group']; ?></h4>
                                        <span class="text-danger compulsoryError"<?php echo $option['CC_ID']; ?>">* Optional.</span>
                                        <?php $customizations = $customizationRecords[$option['CC_ID']]; ?>
                                        <?php foreach ($customizations as $index => $customization) : ?>
                                            <div class="inputGroup">
                                                <input type="checkbox" id="checkbox<?php echo $option['CC_ID'] . $index; ?>" name="customizations[<?php echo $option['CC_ID']; ?>][]" value="<?php echo $customization['Custom_ID']; ?>">
                                                <label for="checkbox<?php echo $option['CC_ID'] . $index; ?>"><?php echo $customization['Custom_Name']; ?>  <span class="price">+ RM <?php echo number_format($customization['Custom_Price'],2); ?></span></label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </ul>
                    <?php endif; ?>
                </div>

                <!-- Quantity and Submit Button -->
                <div class="qty mt-5">
                    <span class="minus bg-dark">-</span>
                    <input type="number" class="count" name="qty" value="0">
                    <span class="plus bg-dark">+</span>
                    <button type="submit" id="submitBtn" class="cart-btn" name="add_to_cart" onclick="submitForm()" ><i class="fas fa-shopping-cart"></i> Add to Cart</button>
                    <p class="cart-limit">*Maximum of 12 items in a cart*</p>
                </div>
            </form>

        </section>
    <!--end add cart from wishlist-->

    <!-- logo carousel -->
    <div class="logo-carousel-section">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="logo-carousel-inner">
						<div class="single-logo-item">
								<img src="assets\img\full-white.png" alt="">
							</div>
							<div class="single-logo-item">
								<img src="assets\img\full-white.png" alt="">
							</div>
							<div class="single-logo-item">
								<img src="assets\img\full-white.png" alt="">
							</div>
							<div class="single-logo-item">
								<img src="assets\img\full-white.png" alt="">
							</div>
							<div class="single-logo-item">
								<img src="assets\img\full-white.png" alt="">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- end logo carousel -->

    <!-- footer -->
    <div class="footer-area">
            <div class="container">
                <div class="row">
                    <div class="col-lg-5 col-md-6">
                        <div class="footer-box about-widget">
                            <h2 class="widget-title">About us</h2>
                            <p>Welcome to Cuppa Joy, your cozy retreat in the heart of the Ayer Keroh. Order your favorite coffee and delicious meals with ease, and enjoy the same warm hospitality from the comfort of your home.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="footer-box get-in-touch">
                            <h2 class="widget-title">Get in Touch</h2>
                            <ul>
                                <li>123, Jalan Ayer Keroh Lama,Kampung Baru Ayer Keroh, 75450 Ayer Keroh, Melaka ,Malaysia</li>
                                <li><i class="fas fa-mail-bulk"></i>cuppajoy88@gmail.com</li>
                                <li><i class="fas fa-phone"></i>012-3568004</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-box pages">
                            <h2 class="widget-title">Pages</h2>
                            <ul>
                                <li><a href="index.php">Home</a></li>
                                <li><a href="shop.php">Menu</a></li>
                                <li><a href="promo.php">Show Promo</a></li>
                                <li><a href="contact.php">Contact Us</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end footer -->
	
	
	
	<!-- jquery -->
	<script src="assets/js/jquery-1.11.3.min.js"></script>
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> -->
	<!-- bootstrap -->
	<script src="assets/bootstrap/js/bootstrap.min.js"></script>
	<!-- count down -->
	<script src="assets/js/jquery.countdown.js"></script>
	<!-- isotope -->
	<script src="assets/js/jquery.isotope-3.0.6.min.js"></script>
	<!-- waypoints -->
	<script src="assets/js/waypoints.js"></script>
	<!-- owl carousel -->
	<script src="assets/js/owl.carousel.min.js"></script>
	<!-- magnific popup -->
	<script src="assets/js/jquery.magnific-popup.min.js"></script>
	<!-- mean menu -->
	<script src="assets/js/jquery.meanmenu.min.js"></script>
	<!-- sticker js -->
	<script src="assets/js/sticker.js"></script>
	<!-- main js -->
	<script src="assets/js/main.js"></script>

	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

	<script src="wishlist.js"></script>

	<script>
    // Function to set the product ID in the hidden input field
    function setProductId(productId) {
        document.getElementById('productId').value = productId;
    }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script>
    function submitForm() {
        // Get the quantity value from the form
        var quantity = $('#addCartForm .count').val();

        // Check if quantity value is less than or equal to 0
        if (quantity <= 0) {
            event.preventDefault(); // Prevent form submission
            console.log("Quantity is 0"); // Debugging statement
            // swal("Error", "Please choose a quantity greater than 0.", "error"); // Display a SweetAlert error message
            swal({
                icon: 'error',
                title: 'Error',
                text: 'Please choose a quantity greater than 0.'
            });
            return false; // Prevent form submission
        }

        // Check if quantity value is greater than 12
        if (quantity > 12) {
            event.preventDefault(); // Prevent form submission
            console.log("Quantity is greater than 12"); // Debugging statement
        // swal("Error", "Cannot add more than 12 quantity into cart.", "error"); // Display a SweetAlert error message
        swal({
                icon: 'error',
                title: 'Error',
                text: 'Cannot add more than 12 quantity into cart.'
            });
            return false; // Prevent form submission
        }

        // Allow form submission if quantity is valid
        return true;
    }
    </script>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        var form = document.getElementById('addCartForm');
        form.addEventListener('submit', function(event) {
            // Check validity of the form
            if (!form.checkValidity()) {
                // Form is invalid, show alert
                alert('Please make sure all customization options are selected.');
                event.preventDefault(); // Prevent form submission
            }
        });
    });

    </script>

    <script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
    <script>
    function confirmLogout() {
        // Display a confirmation dialog using SweetAlert
        swal({
            title: 'Are you sure?',
            text: "You will be logged out",
            icon: 'warning',
            buttons: {
                cancel: {
                    text: "Cancel",
                    value: null,
                    visible: true,
                    className: "",
                    closeModal: true,
                },
                confirm: {
                    text: "Yes, log me out",
                    value: true,
                    visible: true,
                    className: "swal-button swal-button--confirm",
                    closeModal: true
                }
            }
        }).then((result) => {
            // If user confirms, proceed to logout page
            if (result) {
                window.location.href = 'logout.php';
            }
        });

        // Prevent the default link action
        return false;
    }
    </script>


</body>
</html>
