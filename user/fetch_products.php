<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("db_connection.php");

$sortOrder = isset($_GET['sort']) ? $_GET['sort'] : '';
$categoryID = isset($_GET['category']) ? $_GET['category'] : '';
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$productsPerPage = 12;
$offset = ($page - 1) * $productsPerPage;

// Start with a basic query
$query = "SELECT * FROM `product`";

// Initialize the parameters array for safe SQL execution
$parameters = [];

// Conditionally append the category condition
if (!empty($categoryID)) {
    $query .= " WHERE `P_Category` = ?";
    $parameters[] = $categoryID;
}

// Append the sort order by status and optional price sorting
$query .= " ORDER BY `P_Status` DESC"; // 'yes' statuses will come first
if ($sortOrder == '1') {
    $query .= ", `P_Price` ASC";
} elseif ($sortOrder == '2') {
    $query .= ", `P_Price` DESC";
}

// Append pagination conditions
$query .= " LIMIT ? OFFSET ?";
$parameters[] = $productsPerPage;
$parameters[] = $offset;

// Prepare, bind and execute the query
$stmt = mysqli_prepare($con, $query);
if ($stmt) {
    $param_type_string = (!empty($categoryID) ? "s" : "") . "ii";
    mysqli_stmt_bind_param($stmt, $param_type_string, ...$parameters);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $counter = 0; // Initialize a counter variable
        while ($row = mysqli_fetch_assoc($result)) {
            // Check the status of the product
            if ($row['P_Status'] == 'yes') {
                // Display product item for products with 'yes' status
                ?>
                <?php if ($counter % 4 == 0) { ?><div class="row"><?php } ?>
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
                        <p class="product-price">RM<?php echo $row['P_Price']; ?>z</p>
                        <!-- Other product details or buttons -->
                        <a href="single-product.php?product_id=<?php echo $row['P_ID']; ?>" class="cart-btn"><i class="fas fa-shopping-cart"></i> View details</a>
                    </div>
                </div>
                <?php if (($counter + 1) % 4 == 0 || $counter + 1 == mysqli_num_rows($result) || $counter + 1 == $productsPerPage) { ?></div><?php } ?>
                <?php
            } elseif ($row['P_Status'] == 'no') {
                // Display product item for products with 'no' status
                ?>
                <?php if ($counter % 4 == 0) { ?><div class="row"><?php } ?>
                <!-- Display product item -->
                <div class="col-lg-3 col-md-5 text-center">
                    <div class="single-product-item no-status">
                        <!-- Display product image -->
                        <div class="product-image">
                            <a href="javascript:void(0);">
                                <img src="../image/product/<?php echo $row['P_Photo']; ?>" alt="">
                                <div class="overlay">Not available</div> <!-- Add overlay for products with status 'no' -->
                            </a>
                        </div>
                        <!-- Display product name -->
                        <h3><?php echo $row['P_Name']; ?></h3>
                        <!-- Display product price -->
                        <p class="product-price">RM<?php echo $row['P_Price']; ?></p>
                        <!-- Disable link for products with 'no' status -->
                        <a href="javascript:void(0);" onclick="return false;" class="cart-btn disabled"><i class="fas fa-shopping-cart"></i> View details</a>
                    </div>
                </div>
                <?php if (($counter + 1) % 4 == 0 || $counter + 1 == mysqli_num_rows($result) || $counter + 1 == $productsPerPage) { ?></div><?php } ?>
                <?php
            }
            // Increment the counter
            $counter++;
    }
}else {
        echo "<p>No products found.</p>";
    }
    mysqli_stmt_close($stmt);
} else {
    echo "Error preparing SQL: " . mysqli_error($con);
}

// Handle pagination count
$countQuery = "SELECT COUNT(*) AS total FROM `product`";
if (!empty($categoryID)) {
    $countQuery .= " WHERE `P_Category` = ?";
    $countStmt = mysqli_prepare($con, $countQuery);
    mysqli_stmt_bind_param($countStmt, 's', $categoryID);
} else {
    $countStmt = mysqli_prepare($con, $countQuery);
}
mysqli_stmt_execute($countStmt);
$countResult = mysqli_stmt_get_result($countStmt);
$countRow = mysqli_fetch_assoc($countResult);
$totalProducts = $countRow['total'];
$totalPages = ceil($totalProducts / $productsPerPage);

if ($page > $totalPages) {
    header("Location: ?category=$categoryID&page=1&sort=$sortOrder");
    exit;
}

echo "<ul class='pagination'>";
for ($i = 1; $i <= $totalPages; $i++) {
    $activeClass = ($i == $page) ? 'active' : '';
    echo "<li class='page-item $activeClass'><a class='page-link' href='?page=$i&category=$categoryID&sort=$sortOrder'>$i</a></li>";
}
echo "</ul>";

mysqli_close($con);
?>
