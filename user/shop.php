
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
//---------------------------------------------------------------------------------
// Number of products per page
$productsPerPage = 12;

// Get current page number
$page = isset($_GET['page']) ? $_GET['page'] : 1;

// Get category ID from URL parameter
$categoryID = isset($_GET['category']) ? $_GET['category'] : null;

// Calculate the offset for the query
$offset = ($page - 1) * $productsPerPage;

$query = "SELECT * FROM `product` ";
if ($categoryID) {
    // Filter products by category if a category ID is provided
    $query .= "WHERE `P_Category` = $categoryID ";
}//If a category ID is provided, filters the products by the specified category.
$query .= "ORDER BY `P_Status` DESC, `P_Name` ASC LIMIT $productsPerPage OFFSET $offset";
//Orders the products by status (P_Status) in descending order and then by name (P_Name) in ascending order.
$result = mysqli_query($con, $query);

// Query to count total number of products
$countQuery = "SELECT COUNT(*) AS total FROM `product` ";
if ($categoryID) {
    // Filter products by category if a category ID is provided
    $countQuery .= "WHERE `P_Category` = $categoryID ";
}
$stmt = mysqli_prepare($con, $countQuery);
if (!$stmt) {
    // Handle the case when prepare fails
    echo "Error: " . mysqli_error($con);
    exit;
}
mysqli_stmt_execute($stmt);
$countResult = mysqli_stmt_get_result($stmt);

if ($countResult) {
    $countRow = mysqli_fetch_assoc($countResult);
    if ($countRow !== null && isset($countRow['total'])) {
        $totalProducts = $countRow['total'];
    } else {
        // Handle the case when count query result is null or total count is not set
        $totalProducts = 0;
    }
} else {
    // Handle the case when count query execution failed
    $totalProducts = 0;
}

// Calculate total number of pages only if totalProducts is greater than productsPerPage
$totalPages = ceil($totalProducts / $productsPerPage);

// Check if the current page is greater than the total number of pages for the selected category
if ($page > $totalPages && $totalPages > 0) {
    // Construct the redirection URL
	$pageLink = "shop.php?page=$i"; // Change this line
	if (!empty($categoryID)) {
		$pageLink .= "&category=$categoryID";
	}
	
    // Redirect to the appropriate URL
    header("Location: $pageLink");
    exit; // Ensure that the script stops execution after redirection
}
//-----------------------------------------------------------------------------------
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Responsive Bootstrap4 Shop Template, Created by Imran Hossain from https://imransdesign.com/">

	<!-- title -->
	<title>Menu - Cuppa Joy</title>

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
	<!--for category filter slider-->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesom/6.2.0/css/all.min.css">
	<!-- product own css -->
	<link rel="stylesheet" href="product.css">


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
								<li class="current-list-item"><a href="shop.php">Menu</a></li>
								<li><a href="promo.php"> Show Promo</a></li>
								<li><a href="about.php">About Us</a></li>
								<?php
										if(isset($_SESSION["customer_id"])) {
											echo '<li><a href="history.php">Order History</a></li>';
										} else {
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
						<h1>Menu</h1>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end breadcrumb section -->

	<!-- products -->
	<div class="product-section mt-150 mb-150">
		<div class="container">	
			<div class="row">
				<div class="col-md-12">
					<div class="wrapper">
						<div class="icon"><i id="left" class="fas fa-arrow-left"></i></div>
							<div class="product-filters">
								<ul class="tabs-box">
									<?php
										// Define the URL for the 'All' category link
										$allCategoryURL = "?";
										if ($page > 1 || $categoryID) {
											$allCategoryURL .= "page=1"; // Set page parameter to 1
										}
									?>
									<!-- Display the 'All' category -->
									<li class="<?php echo (!$categoryID && (isset($_GET['page']) || !isset($_GET['page'])) ? 'active' : ''); ?>" data-filter="*"><a href="<?php echo $allCategoryURL; ?>">All</a></li>
									<?php
										// Fetch categories from the database
										$categoryQuery = "SELECT * FROM `category`";
										$categoryResult = mysqli_query($con, $categoryQuery);
										$categoryCount = 0; // Initialize category count

										if (mysqli_num_rows($categoryResult) > 0) {
											while ($categoryRow = mysqli_fetch_assoc($categoryResult)) {
												$catID = $categoryRow['CA_ID'];
												$categoryName = $categoryRow['CA_Name'];

												// Query to check if there are any products with status 'yes' for this category
												$productCheckQuery = "SELECT COUNT(*) AS total FROM `product` WHERE `P_Category` = $catID";
												$productCheckResult = mysqli_query($con, $productCheckQuery);
												$productCheckRow = mysqli_fetch_assoc($productCheckResult);
												$totalProductsForCategory = $productCheckRow['total'];

												// Check if there are products with status 'yes' for this category
												if ($totalProductsForCategory > 0) {
													$categoryCount++; // Increment category count

													// Build the category filter URL
													$categoryURL = "?";
													if ($catID) {
														$categoryURL .= "category=$catID";
														if ($page > 1 && !$categoryID) {
															$categoryURL .= "&page=1"; // Reset page to 1
														}
													}
													$activeClass = isset($_GET['category']) && $_GET['category'] == $catID ? 'active' : '';
													echo "<li class=\"$activeClass\" data-filter=\".$categoryName\"><a href=\"$categoryURL\">$categoryName</a></li>";
												}
											}
										}
									?>
								</ul>
							</div>
						<?php if ($categoryCount > 3) { ?>
						<div class="icon"><i id="right" class="fas fa-arrow-right"></i></div>
						<?php } ?>
					</div>
				</div>
			</div>

			<!-- start search field -->
			<div class="sidebar">
			<!--search--> 
			<form class="search">
				<div class="search__wrapper">
					<input type="text" id="live_search" placeholder="Search for..." class="search__field">
				</div>
			</form>
			<!--end search-->

			<!--<div class="categories">
			<ul  class="tabs-box">
				<?php
					// Define the URL for the 'All' category link
					$allCategoryURL = "?";
					if ($page > 1 || $categoryID) {
						$allCategoryURL .= "page=1"; // Set page parameter to 1
					}
				?>
				<-- Display the 'All' category --
				<li class=" <?php echo (!$categoryID && (isset($_GET['page'])|| (!isset($_GET['page'])) )? 'active' : ''); ?>" data-filter="*"><a href="<?php echo $allCategoryURL; ?>">All</a></li>
				<?php
					// Fetch categories from the database
					$categoryQuery = "SELECT * FROM `category` ORDER BY `CA_Name` ASC";
					$categoryResult = mysqli_query($con, $categoryQuery);
					if (mysqli_num_rows($categoryResult) > 0) {
						while ($categoryRow = mysqli_fetch_assoc($categoryResult)) {
							$catID = $categoryRow['CA_ID'];
							if ($categoryRow) {
								$categoryName = $categoryRow['CA_Name'];
								echo "<p class='category-name'>\" " . $categoryName . " \"</p>";
							} else {
								// Handle the case where no category row is found
								echo "<p class='category-name'>Category Not Found</p>";
							}
							

							// Query to check if there are any products with status 'yes' for this category
							$productCheckQuery = "SELECT COUNT(*) AS total FROM `product` WHERE `P_Category` = $catID ";
							$productCheckResult = mysqli_query($con, $productCheckQuery);
							$productCheckRow = mysqli_fetch_assoc($productCheckResult);
							$totalProductsForCategory = $productCheckRow['total'];

							// Check if there are products with status 'yes' for this category
							//count the total number of products in each category. if 0 so no need display the category.
							if ($totalProductsForCategory > 0) {
								// Build the category filter URL
								$categoryURL = "?";
								if ($catID) {
									$categoryURL .= "category=$catID";
									if ($page > 1 && !$categoryID) {
										$categoryURL .= "&page=1"; // Reset page to 1
									}
								}
								$activeClass = isset($_GET['category']) && $_GET['category'] == $catID ? 'active' : '';
								echo "<li class=\"$activeClass\" data-filter=\".$categoryName\"><a href=\"$categoryURL\">$categoryName</a></li>";
							}
						}
					}
					?>
			</ul>
			</div>
			</div>-->

		<div id="searchresult"></div>

		<div id="nosearchresult">
			<div class="product-area">
				<?php
				// Retrieve the category ID from the URL parameters
				$categoryID = isset($_GET['category']) ? $_GET['category'] : null;

				// If category ID is set, display the category name
				if ($categoryID) {
					$categoryQuery = "SELECT `CA_Name` FROM `category` WHERE `CA_ID` = $categoryID";
					$categoryResult = mysqli_query($con, $categoryQuery);
					$categoryRow = mysqli_fetch_assoc($categoryResult);
					$categoryName = $categoryRow['CA_Name'];
					echo "<p class='category-name'>\" " . $categoryName . " \"</p>";

				}
				?>
				<div class="row product-lists">

					<?php
					// Check if there are any products in the result set
					if (mysqli_num_rows($result) > 0) {
						// Loop through each row in the result set
						while ($row = mysqli_fetch_assoc($result)) {
							// Check the status of the product
							if ($row['P_Status'] == 'yes') {
								// Display product item for products with 'yes' status
								?>
								<div class="col-lg-3 col-md-5 text-center">
									<div class="single-product-item">
										<div class="product-image">
											<a href="single-product.php?product_id=<?php echo $row['P_ID']; ?>"><img src="../image/product/<?php echo $row['P_Photo']; ?>" alt=""></a>
										</div>
										<div class="details">
											<h3><?php echo $row['P_Name']; ?></h3>
											<p class="product-price">RM <?php echo number_format($row['P_Price'], 2); ?></p>
											<a href="single-product.php?product_id=<?php echo $row['P_ID']; ?>" class="cart-btn"><i class="fas fa-shopping-cart"></i> View details</a>
										</div>
									</div>
								</div>
								<?php
							} elseif ($row['P_Status'] == 'no') {
								// Display product item for products with 'no' status
								?>
								 <div class="col-lg-3 col-md-5 text-center">
									<div class="no-status single-product-item ">
									<div class="product-image">
										<?php if ($row['P_Status'] == 'no') { ?>
											<div class="overlay">Not available</div> <!-- Add overlay for products with status 'no' -->
										<?php } ?>
										<a href="<?php echo ($row['P_Status'] == 'no') ? 'javascript:void(0);' : 'single-product.php?product_id=' . $row['P_ID']; ?>" <?php echo ($row['P_Status'] == 'no') ? 'onclick="return false;"' : ''; ?> class="<?php echo ($row['P_Status'] == 'no') ? 'disabled-link' : ''; ?>"><img src="../image/product/<?php echo $row['P_Photo']; ?>" alt=""></a>
									</div>
										<div class="details">
											<h3><?php echo $row['P_Name']; ?></h3>
											<p class="product-price">RM <?php echo number_format($row['P_Price'], 2); ?></p>
											<a href="javascript:void(0);" onclick="return false;" class="cart-btn disabled"><i class="fas fa-shopping-cart"></i> View details</a>
										</div>
									</div>
								</div>
								<?php
							}
						}
					} else {
						// If there are no products in the database
						echo "<p>No products found.</p>";
					}
					?>
					</div>
				</div>

				<!-- start pagination -->
				<div class="row">
					<div class="col-lg-12 text-center">
						<?php
						if($totalPages>1)
						{
							echo'<div class="pagination-wrap">';
							echo '<ul>';
							// Check if total pages is greater than 1
							$orangeColorClass = $totalPages >= 1 ? 'orange' : '';

							//if more thatn one page, so the prev will be displayed
							if ($page > 1) {
								echo "<li><a href='?page=".($page - 1).($categoryID ? "&category=$categoryID" : "")."' class='";
								echo $page == 1 ? $orangeColorClass : '';
								echo "'>Prev</a></li>";
							}
						
							// Display page numbers
							for ($i = 1; $i <= $totalPages; $i++) {
								echo "<li " . ($page == $i ? "class='active $orangeColorClass'" : "") . "><a href='?page=" . $i . ($categoryID ? "&category=$categoryID" : "") . "'>" . $i . "</a></li>";
							}
						
							// Display next page link 
							if ($page < $totalPages) {
								echo "<li><a href='?page=".($page + 1).($categoryID ? "&category=$categoryID" : "")."' class='";
								echo $page == $totalPages ? $orangeColorClass : '';
								echo "'>Next</a></li>";
							}
						}
						echo'</ul>';
						echo'</div>';
						?>
					</div>
				</div>	
				<!-- end pagination -->					
			
			</div>
		</div>
	</div>
	<!-- end products -->

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

	<!-- dispay the category filter that can move right or left -->
	<script>
	const tabsBox = document.querySelector(".tabs-box"),
		allTabs = tabsBox.querySelectorAll(".tab"),
		arrowIcons = document.querySelectorAll(".icon i");

	let isDragging = false;

	const handleIcons = (scrollVal) => {
		let maxScrollableWidth = tabsBox.scrollWidth - tabsBox.clientWidth;
		arrowIcons[0].parentElement.style.display = scrollVal <= 0 ? "none" : "flex";
		arrowIcons[1].parentElement.style.display = maxScrollableWidth - scrollVal <= 1 ? "none" : "flex";
	}

	arrowIcons.forEach(icon => {
		icon.addEventListener("click", () => {
			// if clicked icon is left, reduce 350 from tabsBox scrollLeft else add
			let scrollWidth = tabsBox.scrollLeft += icon.id === "left" ? -340 : 340;
			handleIcons(scrollWidth);
		});
	});

	allTabs.forEach(tab => {
		tab.addEventListener("click", () => {
			tabsBox.querySelector(".active").classList.remove("active");
			tab.classList.add("active");
		});
	});

	const dragging = (e) => {
		if (!isDragging) return;
		tabsBox.classList.add("dragging");
		tabsBox.scrollLeft -= e.movementX;
		handleIcons(tabsBox.scrollLeft)
	}

	const dragStop = () => {
		isDragging = false;
		tabsBox.classList.remove("dragging");
	}

	tabsBox.addEventListener("mousedown", () => isDragging = true);
	tabsBox.addEventListener("mousemove", dragging);
	document.addEventListener("mouseup", dragStop);

	// Function to scroll to selected category
	const scrollToSelectedCategory = () => {
		// Get the selected category
		const selectedCategory = "<?php echo $categoryID; ?>";
		// Scroll to the selected category
		if (selectedCategory) {
			const categoryTab = tabsBox.querySelector(`.tab[data-filter=".${selectedCategory}"]`);
			if (categoryTab) {
				// Ensure the selected category is visible
				const tabOffset = categoryTab.offsetLeft;
				tabsBox.scrollTo({ left: tabOffset, behavior: "smooth" });
				console.log("Scrolled to selected category:", selectedCategory);
			}
		}
	};

	// Call the function to scroll to selected category
	scrollToSelectedCategory();

	</script>


	<!--jquery-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

	<script type="text/javascript">
    $(document).ready(function() {
        $("#live_search").keyup(function() {
            var input = $(this).val(); // Get the value of the input field

			//if have input in  search field
            if (input != "") 
			{
                $.ajax({
                    url: "live-search.php",
                    method: "POST",
                    data: {
                        input: input
                    },
                    success: function(data) {
                        // If search results are found, show the search result section and hide the no search result section
                        if (data.trim() !== "") {
                            $("#searchresult").html(data).show();
                            $("#nosearchresult").hide();
                        } else {
                            // If no search results are found, hide the search result section and show the no search result section
                            $("#searchresult").hide();
                            $("#nosearchresult").html("<p>No products found.</p>").show();
                        }
                    }
                });
            } else 
			{
                // If the input field is empty, hide the search result section and show the no search result section
                $("#searchresult").hide();
                $("#nosearchresult").show();
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
