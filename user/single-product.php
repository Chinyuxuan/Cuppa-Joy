
<?php
// Include the database connection code
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include("db_connection.php");

// Default customer ID for guest user
$defaultCustomerId = "guest";

// Check if user is signed in
$isSignedIn = isset($_SESSION["customer_id"]);

// Set customer ID to default if not signed in
$currectuser = $isSignedIn ? $_SESSION["customer_id"] : $defaultCustomerId;
//if current user is not sign in ,so it will display guest


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
       // $bod = $row['C_DOB'];
        $password = $row['C_PW'];
    }
}
//---------------------------------------------------------------------------------------
// Check if the product ID is provided in the URL
if (isset($_GET['product_id'])) {
    $productID = mysqli_real_escape_string($con, $_GET['product_id']);

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
            //opt is option table, cc is customize_category table

            // Fetch options if they exist
            $options = array();
            if (mysqli_num_rows($result2) > 0) {
                while ($row = mysqli_fetch_assoc($result2)) {
                    $options[] = $row;
                }
            }

            // Query to fetch customization records based on CC_ID and filter by available_status
            $customizationQuery = "SELECT * FROM customization WHERE CC_ID = ? AND available_status = 'Available'";
            $customizationStmt = mysqli_prepare($con, $customizationQuery);

            $filteredOptions = array();
            $customizationRecords = array();
            if ($customizationStmt) {
                foreach ($options as $option) {
                    $ccID = $option['CC_ID'];
                    mysqli_stmt_bind_param($customizationStmt, "i", $ccID);
                    mysqli_stmt_execute($customizationStmt);
                    $customizationResult = mysqli_stmt_get_result($customizationStmt);
                    $customizations = mysqli_fetch_all($customizationResult, MYSQLI_ASSOC);

                    // Only include categories with available customizations
                    if (!empty($customizations)) {
                        $filteredOptions[] = $option;//store the option(cust category)that is available
                        $customizationRecords[$ccID] = $customizations;//save the customization of the corresponding customization into array
                    }
                }
                mysqli_stmt_close($customizationStmt);
            } else {
                echo "Error in preparing statement: " . mysqli_error($con);
            }
        } else {
            // If product is not compulsory customized paroduct
            $filteredOptions = array(); // Reset options array
            $customizationRecords = array(); // Reset customization records array
        }
    } else {
        // Product not found
        //echo "Product not found.";
    }
} else {
    // Product ID not provided in the URL
    //echo "Product not provided.";
}
//----------------------------------------------------------------------------------------------------------------------------------------------
// Fetching related products
if (isset($productID)) {
    // Number of products per page
    $productsPerPage = 4;

    // Get the current page number
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    //If page is not set, it defaults to 1.
    //intval ensures the page number is an integer

    // Calculate the offset for the query
    $offset = ($page - 1) * $productsPerPage;

    // Assuming $currentProductId holds the ID of the current product being displayed
    $query = "SELECT * FROM `product` WHERE `P_ID` <> ? AND `P_Status` = 'yes' ORDER BY RAND() LIMIT ? OFFSET ?";
    //P_ID is not equal to the current product ID (<> ?).
    //RAND() is randomly

    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "iii", $productID, $productsPerPage, $offset);
    mysqli_stmt_execute($stmt);
    $relatedResult = mysqli_stmt_get_result($stmt);
}
//-----------------------------------------------------------------------------------------------------------------------------------
// Check if the user is signed in before executing cart-related functionality
//start add to cart
if ($isSignedIn) {
    //start for cart item and details tables
    $gotCart = "SELECT * FROM `cart` WHERE C_ID = $currectuser AND C_Status='No-paid'";
	$gotCartStmt = mysqli_query($con,$gotCart);
	$cart= mysqli_fetch_assoc($gotCartStmt);
	$userID = $cart['CT_ID'];//get the cart id for this customer if their cart is not paid yet

    // Query to check if any customization options are available for the product in the option table
    $optionCheckQuery = "SELECT COUNT(*) AS option_count FROM `opt` WHERE `P_ID` = ?";
    $optionCheckStmt = mysqli_prepare($con, $optionCheckQuery);
    mysqli_stmt_bind_param($optionCheckStmt, "i", $productID);
    mysqli_stmt_execute($optionCheckStmt);
    $optionCheckResult = mysqli_stmt_get_result($optionCheckStmt);
    $optionCount = mysqli_fetch_assoc($optionCheckResult)['option_count'];

// Check if the product customize status is 'no'
if ($productDetails['Customize_Status'] === 'no' || $optionCount == 0) {
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
            $totalQuantityAfterAddition = $totalQuantity + $quantityToAdd;//old qty + new qty

            // Check if the total quantity after addition exceeds 12
            if ($totalQuantityAfterAddition > 12) {
                // Total quantity exceeds 12, so show an alert message
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
                    $existingCartItemId = $existingCartItemData['CI_ID'];//CI_ID is cart item id , we use this to check same cart have same prod or not

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
                    //echo "No existing cart items found for the current product.";

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
                            if (mysqli_stmt_execute($insertStmt)) 
                            {
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
                                                    //echo "Customization added to cart successfully.";
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
                                } 
                                else 
                                {
                                    //echo "No customizations chosen.";
                                }

                                // Success insert the infroms
                                header("Location:cart.php");
                                exit();
                            }
                             else 
                            {
                                // Error in executing the statement
                                echo "Error: " . mysqli_stmt_error($insertStmt);
                            }
                            mysqli_stmt_close($insertStmt);
                        } 
                        else 
                        {
                            // Error in preparing the statement
                            echo "Error: " . mysqli_error($con);
                        }
                    } 
                    else 
                    {
                        // Quantity not provided or not numeric
                    }
                
                }
            }
        } 
} 
else
{ 
    //if the product is "yes" for cusromize status
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
            //cart item table as ci, details table as d

            $existingCartItemStmt = mysqli_prepare($con, $existingCartItemQuery);
            mysqli_stmt_bind_param($existingCartItemStmt, "ii", $userID, $productID);
            mysqli_stmt_execute($existingCartItemStmt);
            $existingCartItemResult = mysqli_stmt_get_result($existingCartItemStmt);

            $existingCustomizations = [];

            if ($existingCartItemResult) 
            {
                // Check if the query executed successfully
                if (mysqli_num_rows($existingCartItemResult) > 0) 
                {
                    // Loop through existing cart items to fetch customizations
                    while ($row = mysqli_fetch_assoc($existingCartItemResult)) {
                        $existingCustomizations[$row['CI_ID']][] = $row['customize_id'];
                        $existingCartItemData[$row['CI_ID']] = $row; // Store the existing cart item data
                    }//store the found same product's all customization for later checking

                    // Flatten $_POST['customizations'] array if necessary
                    $flattenedCustomizations = [];
                    if (isset($_POST['customizations'])) 
                    {
                        foreach ($_POST['customizations'] as $customizationId) 
                        {
                            if (is_array($customizationId)) {
                                // If the element is an array, merge it with $flattenedCustomizations
                                $flattenedCustomizations = array_merge($flattenedCustomizations, $customizationId);
                            } else {
                                // If the element is not an array, simply add it to $flattenedCustomizations
                                $flattenedCustomizations[] = $customizationId;
                            }
                        }
                    }//store the current product all customizations choosen 

                    // Check if the current product exists in the cart
                    $cartItemExists = false;
                    $existingCartItemId = null; // Store the existing cart item ID
                    foreach ($existingCustomizations as $cartItemId => $customizations) 
                    {
                        // Sort the existing customizations for accurate comparison
                        sort($customizations);

                        // Sort the customizations of the current product being added to the cart
                        sort($flattenedCustomizations);

                        // Check if the number of customizations matches
                        if (count($customizations) === count($flattenedCustomizations)) 
                        {
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
                            if ($allMatch) 
                            {
                                $cartItemExists = true;
                                $existingCartItemId = $cartItemId;
                                // Retrieve the cart item data for this cart item ID from the stored array
                                $existingCartItemData = $existingCartItemData[$cartItemId];
                                break;
                            }
                        }
                    }

                    if ($cartItemExists) 
                    {
                        // Product with the same set of customizations already exists in the cart
                        // Fetch the old quantity from the cart item
                        $oldQty = $existingCartItemData['Qty'];

                        // Calculate the new quantity
                        $newQty = intval($oldQty) + intval($_POST['qty']);

                        // Initialize new subtotal with base product price
                        $newSubtotal = 0; // Initialize with zero

                        // Fetch selected customization IDs from $_POST['customizations']
                        $flattenedCustomizations = [];
                        if (isset($_POST['customizations'])) 
                        {
                            foreach ($_POST['customizations'] as $customizationId) 
                            {
                                if (is_array($customizationId)) 
                                {
                                    // If the element is an array, merge it with $flattenedCustomizations
                                    $flattenedCustomizations = array_merge($flattenedCustomizations, $customizationId);
                                } else 
                                {
                                    // If the element is not an array, simply add it to $flattenedCustomizations
                                    $flattenedCustomizations[] = $customizationId;
                                }
                            }
                        }

                        // Initialize total customization price
                        $totalCustomizationPrice = 0;
                        // Calculate total customization price
                        foreach ($flattenedCustomizations as $customizationId)
                        {
                            // Fetch customization price from database based on $customizationId
                            $customizationPriceFetchQuery = "SELECT Custom_Price FROM customization WHERE Custom_ID = ?";
                            $customizationPriceFetchStmt = mysqli_prepare($con, $customizationPriceFetchQuery);
                            mysqli_stmt_bind_param($customizationPriceFetchStmt, "i", $customizationId);
                            mysqli_stmt_execute($customizationPriceFetchStmt);
                            $customizationPriceResult = mysqli_stmt_get_result($customizationPriceFetchStmt);

                            if ($customizationPriceRow = mysqli_fetch_assoc($customizationPriceResult)) 
                            {
                                $totalCustomizationPrice += $customizationPriceRow['Custom_Price'];
                            }//accumulate all selected customization price in this variable, to be added with product price

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
                        //if the cart item is not exist
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
                                foreach ($_POST['customizations'] as $customizationId) 
                                {
                                    if (is_array($customizationId)) 
                                    {
                                        $flattenedCustomizations = array_merge($flattenedCustomizations, $customizationId);
                                    } else 
                                    {
                                        $flattenedCustomizations[] = $customizationId;
                                    }
                                }

                                // Fetch customization prices from the database
                                $customizationQuery = "SELECT SUM(Custom_Price) AS totalCustomizationPrice
                                                    FROM customization
                                                    WHERE Custom_ID IN (" . implode(',', array_map('intval', $flattenedCustomizations)) . ")";
                                $customizationResult = mysqli_query($con, $customizationQuery);
                                //sum up the customizations price

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
                                        foreach ($flattenedCustomizations as $customizationId) 
                                        {
                                            // Insert each selected customization into the detail table
                                            $insertDetail = "INSERT INTO `details` (customize_id, c_item_id) VALUES (?, ?)";
                                            $insertDetailStmt = mysqli_prepare($con, $insertDetail);

                                            if ($insertDetailStmt) 
                                            {
                                                // Bind parameters and execute the insert statement
                                                mysqli_stmt_bind_param($insertDetailStmt, "ii", $customizationId, $cartItemId);
                                                if (mysqli_stmt_execute($insertDetailStmt)) 
                                                {
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
                            foreach ($_POST['customizations'] as $customizationId) 
                            {
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
                    
                            if ($customizationResult) 
                            {
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
                                    foreach ($flattenedCustomizations as $customizationId) 
                                    {
                                        // Insert each selected customization into the detail table
                                        $insertDetail = "INSERT INTO `details` (customize_id, c_item_id) VALUES (?, ?)";
                                        $insertDetailStmt = mysqli_prepare($con, $insertDetail);
                    
                                        if ($insertDetailStmt) 
                                        {
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
                    } 
                    else 
                    {
                        // Quantity not provided or not numeric
                        // echo "Invalid quantity.";
                    }
                }
            }
        }


    } else {
        // Error in executing the query
        echo "Error: " . mysqli_error($totalQuantityStmt);
    }
}
//-----------------------------------------------------------------------------------------------------------------------------------
    //start for wishlist
    $gotWish = "SELECT * FROM `wishlist` WHERE C_ID = $currectuser ";
    $gotWishStmt = mysqli_query($con,$gotWish);
    $wish= mysqli_fetch_assoc($gotWishStmt);
    $wishID = $wish['W_ID'];
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["product_id"]) && isset($_POST["wishlist"])) {

        $customerId = $_SESSION["customer_id"];
        $productId = $_POST["product_id"];

        // Check if the product is already in the wishlist
    $checkQuery = "SELECT * FROM wishlist_item WHERE W_ID IN (SELECT W_ID FROM wishlist WHERE C_ID = ?) AND P_ID = ?";
    $checkStmt = mysqli_prepare($con, $checkQuery);
    mysqli_stmt_bind_param($checkStmt, "ii", $customerId, $productId);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_store_result($checkStmt);

    if (mysqli_stmt_num_rows($checkStmt) > 0) {
        // Product is already in the wishlist
    // echo "Product is already in your wishlist.";
    } else {
        // Insert the product into the wishlist item table
        // First, check if the wishlist exists for the customer
        if ($wishID) {
            // Wishlist exists, use the retrieved wishlist ID
            $wishlistId = $wishID;
        } else {
            // Wishlist does not exist, create a new one and retrieve its ID
            $insertWishlistQuery = "INSERT INTO wishlist (C_ID) VALUES (?)";
            $insertWishlistStmt = mysqli_prepare($con, $insertWishlistQuery);
            mysqli_stmt_bind_param($insertWishlistStmt, "i", $customerId);
            mysqli_stmt_execute($insertWishlistStmt);
            $wishlistId = mysqli_insert_id($con);
        }

        // Insert the product into the wishlist item table
        $insertQuery = "INSERT INTO wishlist_item (P_ID, W_ID) VALUES (?, ?)";
        $insertStmt = mysqli_prepare($con, $insertQuery);
        mysqli_stmt_bind_param($insertStmt, "ii", $productId, $wishlistId);

        if (mysqli_stmt_execute($insertStmt)) {
        // echo "Product added to wishlist successfully.";
        } else {
            echo "Error adding product to wishlist: " . mysqli_error($con);
        }
    }

    } else {
    // echo "Invalid request.";
    }
    }


    //start for product review
    if (isset($_GET['product_id'])) {
        // Sanitize the input to prevent SQL injection
        $productID = mysqli_real_escape_string($con, $_GET['product_id']);

        // Query to fetch comments on the specific product from the "Rate Product" table
        $commentsQuery = "SELECT rp.Comment_Product, c.C_Firstname, c.C_Lastname
                        FROM rate_product rp
                        INNER JOIN rating r ON rp.Ra_ID = r.Ra_ID
                        INNER JOIN `reservation` o ON r.O_ID = o.O_ID
                        INNER JOIN cart ct ON o.CT_ID = ct.CT_ID
                        INNER JOIN customer c ON ct.C_ID = c.C_ID
                        WHERE rp.P_ID = $productID AND rp.Comment_Product <> ''";

        $commentsResult = mysqli_query($con, $commentsQuery);

        if ($commentsResult) {
            if (mysqli_num_rows($commentsResult) > 0) {
                // Fetch comments and customer names
                while ($row = mysqli_fetch_assoc($commentsResult)) {
                    $comment = $row['Comment_Product'];
                    $customerName = $row['C_Firstname'] . " " . $row['C_Lastname'];
                    // Output the comments and customer names
                    // echo "<p><strong>Customer:</strong> $customerName</p>";
                    // echo "<p><strong>Comment:</strong> $comment</p>";
                    // echo "<hr>";
                }
            } else {
            //  echo "No comments found for this product.";
            }
        } else {
            echo "Error fetching comments: " . mysqli_error($con);
        }
    } else {
        // Product ID not provided in the URL
    // echo "Product ID not provided.";
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
	<title>Product Details</title>

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

	<link rel="stylesheet" href="assets/css/single-prod.css">

	<!--num-->
	
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

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
										if(isset($_SESSION["customer_id"])) 
                                        {
											echo '<li><a href="history.php">Order History</a></li>';
										} 
                                        else 
                                        {
											// Output the link with the ID
											echo '<li id="historyGo"><a href="history.php">Order History</a></li>';
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
                                                echo '<a class="shopping-cart" href="wishlist.php"><i class="fas fa-heart"></i></a>';
                                            } else {
                                                // Output the link with the ID
                                                echo '<a class="shopping-cart" id="wishLink"href="wishlist.php"><i class="fas fa-heart"></i></a>';
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
<!-- 
	<-- search area --
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
	<-- end search arewa --
	 -->

	<!-- breadcrumb-section -->
	<div class="breadcrumb-section breadcrumb-bg">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 offset-lg-2 text-center">
					<div class="breadcrumb-text">
						<p>Delightful & Delicious</p>
						<h1>Product Details</h1>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end breadcrumb section -->

	<!-- single product -->
	<div class="single-product mt-150 mb-150">
		<div class="container">
			<div class="row">
				<div class="col-md-5">
					<div class="single-product-img">
						<img src="../image/product/<?php echo $productDetails['P_Photo']; ?>" alt="">
					</div>
				</div>
				<div class="col-md-7">
					<div class="single-product-content">
						<h3><?php echo $productDetails['P_Name']?></h3>
						<p class="single-product-pricing"> RM <?php echo number_format($productDetails['P_Price'],2)?></p>
                        
						<p><?php echo $productDetails['P_Desc']?></p>
						<div class="single-product-form">
							<!--<form action="index.html">
								<input type="number" name="qty" placeholder="0">
							</form>-->
							
							<!--<a href="cart.html" class="cart-btn"><i class="fas fa-shopping-cart"></i> Add to Cart</a>-->
                            <form method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $productID; ?>">
                                <?php 
                                if ($isSignedIn) 
                                {
                                    // Check if the product is already in the wishlist
                                    $checkQuery = "SELECT * FROM wishlist_item WHERE W_ID IN (SELECT W_ID FROM wishlist WHERE C_ID = ?) AND P_ID = ?";
                                    $checkStmt = mysqli_prepare($con, $checkQuery);
                                    mysqli_stmt_bind_param($checkStmt, "ii", $currectuser, $productID);
                                    mysqli_stmt_execute($checkStmt);
                                    mysqli_stmt_store_result($checkStmt);
                                    
                                    if (mysqli_stmt_num_rows($checkStmt) > 0) {
                                        // Product is already in the wishlist
                                        echo "<button type='button' class='wish-btn wish-disable' onclick='gotoWishlistPage()' title='Click to go to wishlist'>
                                                <i class='fas fa-heart wish-added'></i>
                                            </button>";
                                    } else {
                                        // Product is not in the wishlist
                                        echo "<button type='submit' class='wish-btn' name='wishlist'>
                                                <i class='fas fa-heart'></i>
                                            </button>";
                                    }
                                } else {
                                    // User is not signed in, redirect to sign-in page
                                    echo "<button type='button' class='wish-btn' onclick='gotoSignInPage()'>
                                                <i class='fas fa-heart'></i>
                                            </button>";
                                }
                                ?>
                            </form>


                            <?php if (!empty($filteredOptions)) : ?>
                                <p class="mention"><strong>Customizations available: </strong>
                                    <?php foreach ($filteredOptions as $option) : ?>
                                        <?php echo $option['CC_Group']; ?> |
                                    <?php endforeach; ?>
                                </p>
                            <?php else: ?>
                                <p class="mention"><strong>This item cannot be customized.</strong></p>
                            <?php endif; ?>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end single product -->


	<form id="addCartForm" name="addCartForm" method="post" onsubmit="return validateForm(event)">

        <input type="hidden" name="product_id" value="<?php echo $productID; ?>">
        <input type="hidden" name="cart_item_id" value="<?php echo $cartItemId; ?>">
        
        <?php if (!empty($filteredOptions)) : ?>
            <ul class="options-list">
                <?php 
                    // Separate options based on compulsory status
                    //for displayig the radio when the custs is compulsory
                    $radioOptions = array_filter($filteredOptions, function($option) {
                        return $option['Compulsory_Status'] == 'yes';
                    });

                    //for displayig the checkbox when the custs is not compulsory
                    $checkboxOptions = array_filter($filteredOptions, function($option) {
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
                                    <input type="radio" id="radio<?php echo $option['CC_ID'] . $index; ?>" name="customizations[<?php echo $option['CC_ID']; ?>]" value="<?php echo $customization['Custom_ID']; ?>" data-group-name="<?php echo $option['CC_Group']; ?>">
                                    <label for="radio<?php echo $option['CC_ID'] . $index; ?>"><?php echo $customization['Custom_Name']; ?>  <span class="price">+ RM <?php echo number_format($customization['Custom_Price'], 2); ?></span></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="row checkbox">
                    <?php foreach ($checkboxOptions as $option) : ?>
                        <div class="col-md-3">
                            <h4><?php echo $option['CC_Group']; ?></h4>
                            <span class="text-danger compulsoryError" <?php echo $option['CC_ID']; ?>">* Optional.</span>
                            <?php $customizations = $customizationRecords[$option['CC_ID']]; ?>
                            <?php foreach ($customizations as $index => $customization) : ?>
                                <div class="inputGroup">
                                    <input type="checkbox" id="checkbox<?php echo $option['CC_ID'] . $index; ?>" name="customizations[<?php echo $option['CC_ID']; ?>][]" value="<?php echo $customization['Custom_ID']; ?>">
                                    <label for="checkbox<?php echo $option['CC_ID'] . $index; ?>"><?php echo $customization['Custom_Name']; ?>  <span class="price">+ RM <?php echo number_format($customization['Custom_Price'], 2); ?></span></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </ul>
        <?php endif; ?>


        <!-- testimonail-section -->
        <?php 
            // Sanitize the input to prevent SQL injection
            $productID = mysqli_real_escape_string($con, $_GET['product_id']);

            // Query to fetch comments on the specific product from the "Rate Product" table
            $commentsQuery = "SELECT rp.Comment_Product, c.C_Firstname, c.C_Lastname
                            FROM rate_product rp
                            INNER JOIN rating r ON rp.Ra_ID = r.Ra_ID
                            INNER JOIN `reservation` o ON r.O_ID = o.O_ID
                            INNER JOIN cart ct ON o.CT_ID = ct.CT_ID
                            INNER JOIN customer c ON ct.C_ID = c.C_ID
                            WHERE rp.P_ID = $productID AND rp.Comment_Product <> ''";
                            //retrieves comments for specific product by joining multiple tables: rate_product, rating, reservation, cart, and customer.


            $commentsResult = mysqli_query($con, $commentsQuery);
            if ($commentsResult && mysqli_num_rows($commentsResult) > 0) : 
                // Check if there's more than one comment
                $numComments = mysqli_num_rows($commentsResult);
        ?>
            <div class="testimonail-section mt-150 mb-150">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-10 offset-lg-1 text-center">
                            <!-- display the comments in testimoni form if comments greater than 1 -->
                            <?php if ($numComments > 1) : ?>
                                <div class="comment-title">    
                                    <h3><span class="comment-text">What customers think</span></h3>
                                </div>
                                <div class="testimonial-sliders">
                                    <?php
                                    while ($row = mysqli_fetch_assoc($commentsResult)) {
                                        $comment = $row['Comment_Product'];
                                        $customerName = $row['C_Firstname'] . " " . $row['C_Lastname'];
                                    ?>
                                        <div class="single-testimonial-slider">
                                            <div class="client-meta">
                                                <h3><?php echo $customerName; ?> :</h3>
                                                <p class="testimonial-body">
                                                    <i class="fas fa-quote-left"></i>
                                                    <span class="comment"><?php echo $comment; ?></span>
                                                    <i class="fas fa-quote-right"></i>
                                                </p>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php else : ?>
                                 <!-- display the comments in normal form if comments = 1 -->
                                <div class="comment-title">    
                                    <h3><span class="comment-text">What customers think</span></h3>
                                </div>
                                <div class="client-meta">
                                    <?php
                                    $row = mysqli_fetch_assoc($commentsResult);
                                    $comment = $row['Comment_Product'];
                                    $customerName = $row['C_Firstname'] . " " . $row['C_Lastname'];
                                    ?>
                                    <h3><?php echo $customerName; ?></h3>
                                    <p class="testimonial-body">
                                        <i class="fas fa-quote-left"></i>
                                        <span class="comment"><?php echo $comment; ?></span>
                                        <i class="fas fa-quote-right"></i>
                                    </p>
                                
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- end testimonail-section -->

        <div class="qty mt-5">
            <span class="minus bg-dark">-</span>
            <input type="number" class="count" name="qty" value="0">
            <span class="plus bg-dark">+</span>
            <button type="submit" id="submitBtn" class="cart-btn" name="add_to_cart" onclick="submitForm()"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
            <p class="cart-limit">*Maximum of 12 items in a cart*</p>
        </div>
</form>

	<!-- more products -->
	<div class="more-products mb-150">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 offset-lg-2 text-center">
					<div class="section-title">	
						<h3><span class="orange-text">Other</span> Foods or Drinks</h3>
						<p>Here are the others product, add into your cart now if you are interest too! Get more, get happier!!</p>
					</div>
				</div>
			</div>
			<div class="row">
                <?php
                // Check if there are any related products in the result set
                if (isset($relatedResult) && mysqli_num_rows($relatedResult) > 0) {
                    // Loop through each row in the result set
                    while ($row = mysqli_fetch_assoc($relatedResult)) {
                        ?>
                        <!-- Display product item -->
                        <div class="col-lg-3 col-md-5 text-center">
                            <div class="single-product-item">
                                <!-- Display product image -->
                                <div class="product-image">
                                    <a href="single-product.php?product_id=<?php echo $row['P_ID']; ?>"><img src="../image/product/<?php echo $row['P_Photo']; ?>" alt=""></a>
                                </div>
                                <!-- Display product name -->
                                <h3><?php echo $row['P_Name']; ?></h3>
                                <!-- Display product price -->
                                <p class="product-price">RM <?php echo $row['P_Price']; ?></p>
                                <!-- Other product details or buttons -->
                                <a href="single-product.php?product_id=<?php echo $row['P_ID']; ?>" class="cart-btn"><i class="fas fa-shopping-cart"></i> View details</a>
                            
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    // If there are no related products in the database
                    echo "<p>No related products found.</p>";
                }
                ?>
			</div>
		</div>
	</div>
	<!-- end more products -->

	<!-- jquery -->
	<script src="assets/js/jquery-1.11.3.min.js"></script>
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

    <!-- for add to cart number + - -->
	<script type="text/javascript">
		$(document).ready(function(){
		$(document).on('click','.plus',function(){
			$('.count').val(parseInt($('.count').val()) + 1);
		});
		$(document).on('click','.minus',function(){
			$('.count').val(Math.max(parseInt($('.count').val()) - 1,0));
		});
	});
	</script>

    <!-- check have sign in or not,and pop the add to cart related alert if sign in -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script>
    // Function to handle form submission
    function submitForm() {
        // Check if user is signed in
        if (<?php echo $isSignedIn ? 'true' : 'false'; ?>) 
        {
            // Get the quantity value
            var quantity = $('.count').val();

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
                return;
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
                return;
            }

        } else {
            // User is not signed in, show a confirmation prompt
            showSignInConfirmation();
            event.preventDefault(); // Prevent form submission
            return;
        }
    }

    // Function to display a SweetAlert confirmation prompt for signing in
    function showSignInConfirmation() {
        swal({
            title: "Sign In Required",
            text: "You need to sign in to add this item to your cart.",
            icon: "warning",
            buttons: {
                cancel: "Cancel",
                signIn: {
                    text: "Sign In",
                    value: "signIn",
                    className: "swal-button swal-button--confirm",
                },
            },
        }).then((value) => {
            if (value === "signIn") {
                redirectToSignIn(); // Redirect to the sign-in page
            }
        });
    }

    // JavaScript function to redirect to the sign-in page
    function redirectToSignIn() {
        window.location.href = "sign-in.php";
    }
    </script>

    <script>
    function validateForm(event) {
        // Get all radio button groups
        var radioGroups = document.querySelectorAll('.options-list input[type="radio"]');
        var checkboxGroups = document.querySelectorAll('.options-list input[type="checkbox"]');

        var uncheckedCategoriesSet = new Set();

        // Loop through each radio button group
        for (var i = 0; i < radioGroups.length; i++) {
            // Get all radio buttons within the current group
            var group = radioGroups[i].getAttribute('name');
            var radioButtons = document.querySelectorAll('input[name="' + group + '"]');

            // Check if any radio button in the group is checked
            var checked = false;
            for (var j = 0; j < radioButtons.length; j++) {
                if (radioButtons[j].checked) {
                    checked = true;
                    break;
                }
            }

            // If no radio button is checked in the group, add the group name to the set of unchecked categories
            if (!checked) {
                var groupName = radioGroups[i].getAttribute('data-group-name');
                if (groupName) {
                    uncheckedCategoriesSet.add(groupName);
                }
            }
        }

        // Loop through each checkbox group
        for (var k = 0; k < checkboxGroups.length; k++) {
            // Get all checkboxes within the current group
            var checkboxGroup = checkboxGroups[k].getAttribute('name');
            var checkboxes = document.querySelectorAll('input[name="' + checkboxGroup + '"]');

            // Check if any checkbox in the group is checked
            var checkboxChecked = false;
            for (var l = 0; l < checkboxes.length; l++) {
                if (checkboxes[l].checked) {
                    checkboxChecked = true;
                    break;
                }
            }

            // If no checkbox is checked in the group, add the group name to the set of unchecked categories
            if (!checkboxChecked) {
                var checkboxGroupName = checkboxGroups[k].getAttribute('data-group-name');
                if (checkboxGroupName) {
                    uncheckedCategoriesSet.add(checkboxGroupName);
                }
            }
        }

        // Convert the set to an array
        var uncheckedCategories = Array.from(uncheckedCategoriesSet);

        // Log unchecked categories to console
        console.log('Unchecked categories:', uncheckedCategories);

    // If there are unchecked categories, show error message and prevent form submission
    if (uncheckedCategories.length > 0) {
        var errorMessage = 'Please ensure the following customization categories have been checked:\n';
        uncheckedCategories.forEach(function(category) {
            errorMessage += '- ' + category + '\n';
        });
        // swal("Error", errorMessage, "error");
        swal({
                icon: 'error',
                title: 'Error',
                text: errorMessage
            });
        event.preventDefault(); // Prevent form submission
        return;
    }

    }

    </script>

    <script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
    <script>
    function gotoSignInPage() {
        // Display a SweetAlert prompt with Sign In and Cancel options
        swal({
            title: 'Sign In Required',
            text: "You need to sign in to add this item to your wishlist.",
            icon: 'warning',
            buttons: {
                cancel: {
                    text: "Cancel",
                    value: false,
                    visible: true,
                    className: "",
                    closeModal: true
                },
                confirm: {
                    text: "Sign In",
                    value: true,
                    visible: true,
                    className: "swal-button swal-button--confirm",
                    closeModal: true
                }
            }
        }).then((result) => {
            // If user clicks "Sign In", redirect to the sign-in page
            if (result) {
                window.location.href = 'sign-in.php';
            }
        });
    }

    function gotoWishlistPage() {
        // Redirect to the wishlist page
        window.location.href = 'wishlist.php';
    }
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
