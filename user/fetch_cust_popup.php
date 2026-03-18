<?php
include("db_connection.php");

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
            // Query to fetch customization options for the product
            $query = "SELECT opt.*, cc.CC_Group, cc.Compulsory_Status, c.Custom_ID, c.Custom_Name, c.Custom_Price
                      FROM `opt` AS opt 
                      INNER JOIN `customize_category` AS cc ON opt.CC_ID = cc.CC_ID 
                      LEFT JOIN `customization` AS c ON opt.CC_ID = c.CC_ID
                      WHERE opt.`P_ID` = $productID AND c.available_status = 'Available'
                      ORDER BY opt.CC_ID, c.Custom_ID ASC";
            $result = mysqli_query($con, $query);

            $customizationData = array();
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $ccID = $row['CC_ID'];
                    $customizationData[$ccID]['CC_Group'] = $row['CC_Group'];
                    $customizationData[$ccID]['Compulsory_Status'] = $row['Compulsory_Status'];
                    if (!isset($customizationData[$ccID]['Customizations'])) {
                        $customizationData[$ccID]['Customizations'] = array();
                    }
                    $customizationData[$ccID]['Customizations'][] = $row;
                }
                echo json_encode($customizationData);
            } else {
                echo "<p><strong>This product does not have any customization options available.</strong></p>";
            }
        } else {
            echo "<p><strong>This product does not have any customization options available.</strong></p>";
        }
    } else {
        // Product not found
        echo "Product not found.";
    }
} else {
    // Product ID not provided in the URL
    echo "Product not provided.";
}
?>
