<?php
// Include the database connection code
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include("db_connection.php");

// Number of products per page
$productsPerPage = 12;

// Get current page number
$page = isset($_GET['page']) ? $_GET['page'] : 1;

// Get category ID from URL parameter
$categoryID = isset($_GET['category']) ? $_GET['category'] : null;

// Calculate the offset for the query
$offset = ($page - 1) * $productsPerPage;

// Build the SQL query
$query = "SELECT * FROM `product`";
if ($categoryID) {
    // Filter products by category if a category ID is provided
    $query .= " WHERE `P_Category` = $categoryID";
}
$query .= " LIMIT $productsPerPage OFFSET $offset";

// Execute the query
$result = mysqli_query($con, $query);

// Query to count total number of products
$countQuery = "SELECT COUNT(*) AS total FROM `product`";
if ($categoryID) {
    // Count total number of products for the specific category if a category ID is provided
    $countQuery .= " WHERE `P_Category` = $categoryID";
}
$countResult = mysqli_query($con, $countQuery);
$countRow = mysqli_fetch_assoc($countResult);
$totalProducts = $countRow['total'];

// Calculate total number of pages
$totalPages = ceil($totalProducts / $productsPerPage);
// Check if the current page is greater than the total number of pages for the selected category
if ($page > $totalPages && $totalPages > 0) {
    // Redirect to the first page of the selected category
    header("Location: ?category=$categoryID");
    exit; // Ensure that the script stops execution after redirection
}

//search//
if(isset($_POST['input']))
{
    $input=$_POST['input'];
    $query_search="SELECT * FROM `product` WHERE P_Name LIKE '%$input%' ORDER BY `P_Name` ASC";

    $result_search=mysqli_query($con,$query_search);

    if(mysqli_num_rows($result_search) > 0) 
    {
        $counter = 0; // Initialize a counter variable
        while ($row = mysqli_fetch_assoc($result_search)) 
        {
            // Check the status of the product
            if ($row['P_Status'] == 'yes') {
                // Display product item for products with 'yes' status
                ?>
                <?php if ($counter % 4 == 0) { ?><div class="row"><?php } ?>
                <!-- Display product item -->
                <div class="col-lg-3 col-md-5 text-center">
                    <div class="single-product-item">
                        <div class="product-image">
                        <a href="single-product.php?product_id=<?php echo $row['P_ID']; ?>"><img src="../image/product/<?php echo $row['P_Photo']; ?>" alt=""></a>
                        </div>
                        <h3><?php echo $row['P_Name']; ?></h3>
                        <p class="product-price">RM <?php echo $row['P_Price']; ?></p>
                        <a href="single-product.php?product_id=<?php echo $row['P_ID']; ?>" class="cart-btn"><i class="fas fa-shopping-cart"></i> View details</a>
                    </div>
                </div>
                <?php if (($counter + 1) % 4 == 0 || $counter + 1 == mysqli_num_rows($result_search) || $counter + 1 == $productsPerPage) { ?></div><?php } ?>
                <?php
            } elseif ($row['P_Status'] == 'no') {
                // Display product item for products with 'no' status
                ?>
                <?php if ($counter % 4 == 0) { ?><div class="row"><?php } ?>
               
                <div class="col-lg-3 col-md-5 text-center">
                    <div class="single-product-item no-status">
                        
                        <div class="product-image">
                            <a href="javascript:void(0);">
                                <img src="../image/product/<?php echo $row['P_Photo']; ?>" alt="">
                                <div class="overlay">Not available</div> <!-- Add overlay for products with status 'no' -->
                            </a>
                        </div>
                        <h3><?php echo $row['P_Name']; ?></h3>
                        <p class="product-price">RM <?php echo number_format($row['P_Price'],2); ?></p>
                        <!-- Disable link for products with 'no' status -->
                        <a href="javascript:void(0);" onclick="return false;" class="cart-btn disabled"><i class="fas fa-shopping-cart"></i> View details</a>
                    </div>
                </div>
                <?php if (($counter + 1) % 4 == 0 || $counter + 1 == mysqli_num_rows($result_search) || $counter + 1 == $productsPerPage) { ?></div><?php } ?>
                <?php
            }
            // Increment the counter
            $counter++;
        }
    } else {
        // If there are no products found matching the search query
        echo "<p>No products found.</p>";
    }
}

?>
