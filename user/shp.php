<?php
// Include the database connection code
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include("db_connection.php");

// Number of products per page
$productsPerPage = 9;

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


?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Responsive Bootstrap4 Shop Template, Created by Imran Hossain from https://imransdesign.com/">

	<!-- title -->
	<title>Shop</title>

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
	<link rel="stylesheet" href="poduct.css">


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
								<li class="current-list-item"><a href="#">Home</a>
									<ul class="sub-menu">
										<li><a href="index.php">Static Home</a></li>
										<li><a href="index_2.html">Slider Home</a></li>
									</ul>
								</li>
								<li><a href="shop.php">Product</a></li>
								<li><a href="about.php">About Us</a></li>
								<li><a href="contact.php">Contact Us</a></li>
								<li><a href="#">Pages</a>
									<ul class="sub-menu">
										<li><a href="404.html">404 page</a></li>
										<li><a href="about.php">About</a></li>
										<li><a href="cart.html">Cart</a></li>
										<li><a href="checkout.html">Check Out</a></li>
										<li><a href="contact.php">Contact</a></li>
										<li><a href="news.html">News</a></li>
										<li><a href="single-news.html">Single News</a></li>
										<li><a href="shop.php">Shop</a></li>
										<li><a href="single-product.php">Single Product</a></li>
									</ul>
								</li>
								<!--
									<li><a href="news.html">News</a>
									<ul class="sub-menu">
										<li><a href="news.html">News</a></li>
										<li><a href="single-news.html">Single News</a></li>
									</ul>
								</li>
								<li><a href="contact.html">Contact</a></li>
								<li><a href="shop.html">Shop</a>
									<ul class="sub-menu">
										<li><a href="shop.html">Shop</a></li>
										<li><a href="checkout.html">Check Out</a></li>
										<li><a href="single-product.html">Single Product</a></li>
										<li><a href="cart.html">Cart</a></li>
									</ul>
								</li>
								-->	
								
								<li>
									<div class="header-icons">
										<a class="shopping-cart" href="cart.html"><i class="fas fa-shopping-cart"></i></a>
										<a class="mobile-hide search-bar-icon" href="#"><i class="fas fa-search"></i></a>
										<a class="shopping-cart" href="cart.html"><i class="fas fa-th-list"></i></a>
										<a class="shopping-cart" href="profile.php"><i class="fas fa-user"></i></a>
									</div>
								</li>
							</ul>
						</nav>
						<a class="mobile-show search-bar-icon" href="#"><i class="fas fa-search"></i></a>
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
						<p>Fresh and Organic</p>
						<h1>Shop</h1>
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
							<ul  class="tabs-box">
								<?php
									// Define the URL for the 'All' category link
									$allCategoryURL = "?";
									if ($page > 1 || $categoryID) {
										$allCategoryURL .= "page=1"; // Set page parameter to 1
									}
								?>
								<!-- Display the 'All' category -->
								<li class=" <?php echo (!$categoryID && (isset($_GET['page'])|| (!isset($_GET['page'])) )? 'active' : ''); ?>" data-filter="*"><a href="<?php echo $allCategoryURL; ?>">All</a></li>
								<?php
									// Fetch categories from the database
									$categoryQuery = "SELECT * FROM `category`";
									$categoryResult = mysqli_query($con, $categoryQuery);
									if (mysqli_num_rows($categoryResult) > 0) {
										while ($categoryRow = mysqli_fetch_assoc($categoryResult)) {
											$catID = $categoryRow['CA_ID'];
											$categoryName = $categoryRow['CA_Name'];

											// Query to check if products exist for this category
											$productCheckQuery = "SELECT COUNT(*) AS total FROM `product` WHERE `P_Category` = $catID";
											$productCheckResult = mysqli_query($con, $productCheckQuery);
											$productCheckRow = mysqli_fetch_assoc($productCheckResult);
											$totalProductsForCategory = $productCheckRow['total'];

											// Check if there are products for this category
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
						<div class="icon"><i id="right" class="fas fa-arrow-right"></i></div>
					</div>
						
				</div>
			</div>

			<!--search--> 
			<input type="text" class="search-box" id="live_search" placeholder="Search..." autocomplete="off">
			<div id="searchresult"></div>
			<!--end search-->



			<div class="row product-lists">
			<?php
                // Check if there are any products in the result set
                if (mysqli_num_rows($result) > 0) {
                    // Loop through each row in the result set
                    while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <!-- Display product item -->
                        <div class="col-lg-4 col-md-6 text-center">
                            <div class="single-product-item">
                                <!-- Display product image -->
                                <div class="product-image">
                                    <a href="single-product.html"><img src="assets/img/<?php echo $row['P_Photo']; ?>" alt=""></a>
                                </div>
                                <!-- Display product name -->
                                <h3><?php echo $row['P_Name']; ?></h3>
                                <!-- Display product price -->
                                <p class="product-price">$<?php echo $row['P_Price']; ?></p>
                                <!-- Other product details or buttons -->
                                <a href="cart.html" class="cart-btn"><i class="fas fa-shopping-cart"></i> View details</a>
                                <a href="wishlist.html" class="cart-btn"><i class="fas fa-th-list"></i> </a>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    // If there are no products in the database
                    echo "<p>No products found.</p>";
                }
                ?>
			</div>

			<div class="row">
				<div class="col-lg-12 text-center">
				<div class="pagination-wrap">
					<ul>
					<?php
					// Check if total pages is greater than 1
					$orangeColorClass = $totalPages >= 1 ? 'orange' : '';

					// Display previous page link
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
					?>

					</ul>
				</div>


				</div>
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
								<img src="assets\img\full-black.png" alt="">
							</div>
							<div class="single-logo-item">
								<img src="assets\img\full-black.png" alt="">
							</div>
							<div class="single-logo-item">
								<img src="assets\img\full-black.png" alt="">
							</div>
							<div class="single-logo-item">
								<img src="assets\img\full-black.png" alt="">
							</div>
							<div class="single-logo-item">
								<img src="assets\img\full-black.png" alt="">
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
				<div class="col-lg-3 col-md-6">
					<div class="footer-box about-widget">
						<h2 class="widget-title">About us</h2>
						<p>Ut enim ad minim veniam perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae.</p>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div class="footer-box get-in-touch">
						<h2 class="widget-title">Get in Touch</h2>
						<ul>
							<li>34/8, East Hukupara, Gifirtok, Sadan.</li>
							<li>support@fruitkha.com</li>
							<li>+00 111 222 3333</li>
						</ul>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div class="footer-box pages">
						<h2 class="widget-title">Pages</h2>
						<ul>
							<li><a href="index.html">Home</a></li>
							<li><a href="about.html">About</a></li>
							<li><a href="services.html">Shop</a></li>
							<li><a href="news.html">News</a></li>
							<li><a href="contact.html">Contact</a></li>
						</ul>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div class="footer-box subscribe">
						<h2 class="widget-title">Subscribe</h2>
						<p>Subscribe to our mailing list to get the latest updates.</p>
						<form action="index.html">
							<input type="email" placeholder="Email">
							<button type="submit"><i class="fas fa-paper-plane"></i></button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end footer -->
	
	<!-- copyright -->
	<div class="copyright">
		<div class="container">
			<div class="row">
				<div class="col-lg-6 col-md-12">
					<p>Copyrights &copy; 2019 - <a href="https://imransdesign.com/">Imran Hossain</a>,  All Rights Reserved.<br>
						Distributed By - <a href="https://themewagon.com/">Themewagon</a>
					</p>
				</div>
				<div class="col-lg-6 text-right col-md-12">
					<div class="social-icons">
						<ul>
							<li><a href="#" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
							<li><a href="#" target="_blank"><i class="fab fa-twitter"></i></a></li>
							<li><a href="#" target="_blank"><i class="fab fa-instagram"></i></a></li>
							<li><a href="#" target="_blank"><i class="fab fa-linkedin"></i></a></li>
							<li><a href="#" target="_blank"><i class="fab fa-dribbble"></i></a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end copyright -->
	
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

	<script>const tabsBox = document.querySelector(".tabs-box"),
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
		if(!isDragging) return;
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
	</script>

	<!--jquery-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

	<script type="text/javascript">
		$(document).ready(function() {
    $("#live_search").keyup(function() {
        var input = $(this).val(); // Get the value of the input field
        // alert(input); // You can remove this line if you don't need the alert

        if (input != "") {
            $.ajax({
                url: "shop.php",
                method: "POST",
                data: {
                    input: input
                },
                success: function(data) {
                    $("#searchresult").html(data);
                }
            });
        } else {
            $("#searchresult").html(""); // Clear the search result if the input is empty
        }
    });
});


	</script>
</body>
</html>