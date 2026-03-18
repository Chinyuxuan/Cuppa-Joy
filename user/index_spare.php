<?php
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

// Number of products per page
$productsPerPage = 4;

// Get the current page number
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Calculate the offset for the query
$offset = ($page - 1) * $productsPerPage;

// Build the SQL query (using prepared statement to prevent SQL injection)
$query = "SELECT * FROM `product` WHERE `P_Status` = 'yes' ORDER BY `P_ID` DESC LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "ii", $productsPerPage, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

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
        $bod = $row['C_DOB'];
        $password = $row['C_PW'];
    }

    // Create cart if not exists
    $checkCart = "SELECT * FROM `cart` WHERE C_ID = ? AND C_Status = 'No-paid'";
    $stmtCheckCart = mysqli_prepare($con, $checkCart);
    mysqli_stmt_bind_param($stmtCheckCart, "s", $currectuser);
    mysqli_stmt_execute($stmtCheckCart);
    $resultCheckCart = mysqli_stmt_get_result($stmtCheckCart);
    
    if (!$resultCheckCart || mysqli_num_rows($resultCheckCart) == 0) {
        $create_cart = "INSERT INTO `cart` (C_ID, C_Status) VALUES (?, 'No-paid')";
        $stmtCreateCart = mysqli_prepare($con, $create_cart);
        mysqli_stmt_bind_param($stmtCreateCart, "s", $currectuser);
        $gotCart = mysqli_stmt_execute($stmtCreateCart);
        if (!$gotCart) {
            echo "Error: " . mysqli_error($con);
        }
    } 

    // Create wishlist if not exists
    $checkWish = "SELECT * FROM `wishlist` WHERE C_ID = ?";
    $stmtCheckWish = mysqli_prepare($con, $checkWish);
    mysqli_stmt_bind_param($stmtCheckWish, "s", $currectuser);
    mysqli_stmt_execute($stmtCheckWish);
    $resultCheckWish = mysqli_stmt_get_result($stmtCheckWish);

    if(!$resultCheckWish || mysqli_num_rows($resultCheckWish) == 0) {
        $create_wish = "INSERT INTO `wishlist` (C_ID) VALUES (?)";
        $stmtCreateWish = mysqli_prepare($con, $create_wish);
        mysqli_stmt_bind_param($stmtCreateWish, "s", $currectuser);
        $gotWish = mysqli_stmt_execute($stmtCreateWish);
        if (!$gotWish) {
            echo "Error: " . mysqli_error($con);
        }
    }
}

// Close the prepared statements if they are not null
if ($stmt !== null) {
    mysqli_stmt_close($stmt);
}
if ($stmtUser !== null) {
    mysqli_stmt_close($stmtUser);
}
if ($stmtCheckCart !== null) {
    mysqli_stmt_close($stmtCheckCart);
}
if ($stmtCreateCart !== null) {
    mysqli_stmt_close($stmtCreateCart);
}
if ($stmtCheckWish !== null) {
    mysqli_stmt_close($stmtCheckWish);
}
if ($stmtCreateWish !== null) {
    mysqli_stmt_close($stmtCreateWish);
}

// Close the database connection
mysqli_close($con);
?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Responsive Bootstrap4 Shop Template, Created by Imran Hossain from https://imransdesign.com/">

	<!-- title -->
	<title>Home page - Cuppa Joy</title>

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

	<link rel="stylesheet" href="index.css">
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
										<li><a href="cart.php">Cart</a></li>
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
										<a class="shopping-cart" href="cart.php"><i class="fas fa-shopping-cart"></i></a>
										<!--<a class="mobile-hide search-bar-icon" href="#"><i class="fas fa-search"></i></a>-->
										<a class="shopping-cart" href="wishlist.php"><i class="fas fa-heart"></i></a>
										<?php
											if(isset($_SESSION["customer_id"])) {
												echo '<a class="shopping-cart" href="profile.php">
														<i class="fas fa-user"></i>
														<span id="firstname"> Welcome! ' . ($isSignedIn ? htmlspecialchars($firstname) : "Guest") . '</span>
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
											echo '<a class="shopping-cart logout" href="logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a>';
										} else {
											// User is not logged in, do nothing or display alternative content
										}
										
										?>
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
	<!-- end search area -->

	<!-- hero area -->
	<div class="hero-area hero-bg">
		<div class="container">
			<div class="row">
				<div class="col-lg-9 offset-lg-2 text-center">
					<div class="hero-text">
						<div class="hero-text-tablecell">
							<p class="subtitle">Fresh & Delicious</p>
							<h1>A cup of comfort, A dash of joy</h1>
							<div class="hero-btns">
								<a href="shop.php" class="boxed-btn">Product Collection</a>
								<a href="contact.php" class="bordered-btn" >Contact Us</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end hero area -->

	<!-- features list section --
	<div class="list-section pt-80 pb-80">
		<div class="container">

			<div class="row">
				<div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
					<div class="list-box d-flex align-items-center">
						<div class="list-icon">
							<i class="fas fa-shipping-fast"></i>
						</div>
						<div class="content">
							<h3>Free Shipping</h3>
							<p>When order over $75</p>
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
					<div class="list-box d-flex align-items-center">
						<div class="list-icon">
							<i class="fas fa-phone-volume"></i>
						</div>
						<div class="content">
							<h3>24/7 Support</h3>
							<p>Get support all day</p>
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-md-6">
					<div class="list-box d-flex justify-content-start align-items-center">
						<div class="list-icon">
							<i class="fas fa-sync"></i>
						</div>
						<div class="content">
							<h3>Refund</h3>
							<p>Get refund within 3 days!</p>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
	<-- end features list section -->

	<!-- product section -->
	<div class="product-section mt-150 mb-150">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 offset-lg-2 text-center">
					<div class="section-title">	
						<h3><span class="orange-text">New</span> Products</h3>
						<p>Explore new and favorite Cuppa Joy drink and food products. Order online and get your products now!!</p>
					</div>
				</div>
			</div>

			<div class="row">
				<?php
                // Check if there are any products in the result set
                if (mysqli_num_rows($result) > 0) {
                    // Loop through each row in the result set
                    while ($row = mysqli_fetch_assoc($result)) {
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
                                <p class="product-price">RM <?php echo number_format($row['P_Price'], 2); ?></p>

                                <!-- Other product details or buttons -->
                                <a href="single-product.php?product_id=<?php echo $row['P_ID']; ?>" class="cart-btn"><i class="fas fa-shopping-cart"></i> View details</a>
                                
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
		</div>
	</div>
	<!-- end product section -->

	<!-- latest news -->
	<div class="latest-news pt-150 pb-150">
		<div class="container">

		<div class="row">
            <div class="col-lg-8 offset-lg-2 text-center">
                <div class="section-title">
                    <h3>Our <span class="orange-text">Team</span></h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquid, fuga quas itaque eveniet beatae optio.</p>
                </div>
            </div>
        </div>

			<div class="row owl-carousel owl-theme" id="barista-carousel">
				<?php
				// PHP code to fetch and display staff members
				include("db_connection.php");

				$query_staff = "SELECT * FROM `barista`"; // Fetch only barista staff
				$result_staff = mysqli_query($con, $query_staff);

				if ($result_staff) {
					while ($staff_details = mysqli_fetch_assoc($result_staff)) {
						$barista_Photo = $staff_details['B_Photo'];
						$Name = $staff_details['B_Name'];
						$Description = $staff_details['B_Description'];

						// Display staff member HTML block
						echo '<div class="col-lg-4 col-md-6">';
						echo '<div class="single-team-item">';
						echo '<div class="team-bg team-bg-"><img src="../image/barista/' . $barista_Photo . '"></div>';
						
						echo '<div class="team-desc">';
						echo '<h4>' . $Name . ' <span></span></h4>';
						echo '<ul class="social-link-team">';
						echo '<li><a href="#" target="_blank"><i class="fab fa-facebook-f"></i></a></li>';
						echo '<li><a href="#" target="_blank"><i class="fab fa-twitter"></i></a></li>';
						echo '<li><a href="#" target="_blank"><i class="fab fa-instagram"></i></a></li>';
						echo '</ul>';
						echo '<p>" ' . $Description . ' "</p>';

						echo '</div>';
						echo '</div>';
						echo '</div>';
					}
				} else {
					echo "<p>No barista found.</p>";
				}

				mysqli_close($con);
				?>
        	</div>

			<!--<div class="row">
				<div class="col-lg-4 col-md-6">
					<div class="single-latest-news">
						<a href="single-news.html"><div class="latest-news-bg news-bg-1"></div></a>
						<div class="news-text-box">
							<h3><a href="single-news.html">You will vainly look for fruit on it in autumn.</a></h3>
							<p class="blog-meta">
								<span class="author"><i class="fas fa-user"></i> Admin</span>
								<span class="date"><i class="fas fa-calendar"></i> 27 December, 2019</span>
							</p>
							<p class="excerpt">Vivamus lacus enim, pulvinar vel nulla sed, scelerisque rhoncus nisi. Praesent vitae mattis nunc, egestas viverra eros.</p>
							<a href="single-news.html" class="read-more-btn">read more <i class="fas fa-angle-right"></i></a>
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-md-6">
					<div class="single-latest-news">
						<a href="single-news.html"><div class="latest-news-bg news-bg-2"></div></a>
						<div class="news-text-box">
							<h3><a href="single-news.html">A man's worth has its season, like tomato.</a></h3>
							<p class="blog-meta">
								<span class="author"><i class="fas fa-user"></i> Admin</span>
								<span class="date"><i class="fas fa-calendar"></i> 27 December, 2019</span>
							</p>
							<p class="excerpt">Vivamus lacus enim, pulvinar vel nulla sed, scelerisque rhoncus nisi. Praesent vitae mattis nunc, egestas viverra eros.</p>
							<a href="single-news.html" class="read-more-btn">read more <i class="fas fa-angle-right"></i></a>
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-md-6 offset-md-3 offset-lg-0">
					<div class="single-latest-news">
						<a href="single-news.html"><div class="latest-news-bg news-bg-3"></div></a>
						<div class="news-text-box">
							<h3><a href="single-news.html">Good thoughts bear good fresh juicy fruit.</a></h3>
							<p class="blog-meta">
								<span class="author"><i class="fas fa-user"></i> Admin</span>
								<span class="date"><i class="fas fa-calendar"></i> 27 December, 2019</span>
							</p>
							<p class="excerpt">Vivamus lacus enim, pulvinar vel nulla sed, scelerisque rhoncus nisi. Praesent vitae mattis nunc, egestas viverra eros.</p>
							<a href="single-news.html" class="read-more-btn">read more <i class="fas fa-angle-right"></i></a>
						</div>
					</div>
				</div>

				
			</div>
			-->
			<!--
			<div class="row">
				<div class="col-lg-12 text-center">
					<a href="news.html" class="boxed-btn">More News</a>
				</div>
			</div>
			-->
		</div>
	</div>
	<!-- end latest news -->

	<!-- cart banner section -->
	<section class="cart-banner pt-100 pb-100">
    	<div class="container">
        	<div class="row clearfix">
            	<!--Image Column-->
            	<div class="image-column col-lg-6">
                	<div class="image">
                    	<div class="price-box">
                        	<div class="inner-price">
                                <span class="price">
                                    <strong>30%</strong> <br> off per kg
                                </span>
                            </div>
                        </div>
                    	<img src="assets/img/a.jpg" alt="">
                    </div>
                </div>
                <!--Content Column-->
                <div class="content-column col-lg-6">
					<h3><span class="orange-text">Deal</span> of the month</h3>
                    <h4>Hikan Strwaberry</h4>
                    <div class="text">Quisquam minus maiores repudiandae nobis, minima saepe id, fugit ullam similique! Beatae, minima quisquam molestias facere ea. Perspiciatis unde omnis iste natus error sit voluptatem accusant</div>
                    <!--Countdown Timer-->
                    <div class="time-counter"><div class="time-countdown clearfix" data-countdown="2020/2/01"><div class="counter-column"><div class="inner"><span class="count">00</span>Days</div></div> <div class="counter-column"><div class="inner"><span class="count">00</span>Hours</div></div>  <div class="counter-column"><div class="inner"><span class="count">00</span>Mins</div></div>  <div class="counter-column"><div class="inner"><span class="count">00</span>Secs</div></div></div></div>
                	<a href="cart.php" class="cart-btn mt-3"><i class="fas fa-shopping-cart"></i> Add to Cart</a>
                </div>
            </div>
        </div>
    </section>
    <!-- end cart banner section -->

	<!-- testimonail-section -->
	<div class="testimonail-section mt-150 mb-150">
		<div class="container">
			<div class="row">
				<div class="col-lg-10 offset-lg-1 text-center">
					<div class="testimonial-sliders">
						<div class="single-testimonial-slider">
							<div class="client-avater">
								<img src="assets/img/avaters/avatar1.png" alt="">
							</div>
							<div class="client-meta">
								<h3>Saira Hakim <span>Local shop owner</span></h3>
								<p class="testimonial-body">
									" Sed ut perspiciatis unde omnis iste natus error veritatis et  quasi architecto beatae vitae dict eaque ipsa quae ab illo inventore Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium "
								</p>
								<div class="last-icon">
									<i class="fas fa-quote-right"></i>
								</div>
							</div>
						</div>
						<div class="single-testimonial-slider">
							<div class="client-avater">
								<img src="assets/img/avaters/avatar2.png" alt="">
							</div>
							<div class="client-meta">
								<h3>David Niph <span>Local shop owner</span></h3>
								<p class="testimonial-body">
									" Sed ut perspiciatis unde omnis iste natus error veritatis et  quasi architecto beatae vitae dict eaque ipsa quae ab illo inventore Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium "
								</p>
								<div class="last-icon">
									<i class="fas fa-quote-right"></i>
								</div>
							</div>
						</div>
						<div class="single-testimonial-slider">
							<div class="client-avater">
								<img src="assets/img/avaters/avatar3.png" alt="">
							</div>
							<div class="client-meta">
								<h3>Jacob Sikim <span>Local shop owner</span></h3>
								<p class="testimonial-body">
									" Sed ut perspiciatis unde omnis iste natus error veritatis et  quasi architecto beatae vitae dict eaque ipsa quae ab illo inventore Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium "
								</p>
								<div class="last-icon">
									<i class="fas fa-quote-right"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end testimonail-section -->
	
	<!-- advertisement section -->
	<div class="abt-section mb-150">
		<div class="container">
			<div class="row">
				<div class="col-lg-6 col-md-12">
					<div class="abt-bg">
						<a href="https://www.youtube.com/watch?v=DBLlFWYcIGQ" class="video-play-btn popup-youtube"><i class="fas fa-play"></i></a>
					</div>
				</div>
				<div class="col-lg-6 col-md-12">
					<div class="abt-text">
						<p class="top-sub">Since Year 1999</p>
						<h2>We are <span class="orange-text">Fruitkha</span></h2>
						<p>Etiam vulputate ut augue vel sodales. In sollicitudin neque et massa porttitor vestibulum ac vel nisi. Vestibulum placerat eget dolor sit amet posuere. In ut dolor aliquet, aliquet sapien sed, interdum velit. Nam eu molestie lorem.</p>
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sapiente facilis illo repellat veritatis minus, et labore minima mollitia qui ducimus.</p>
						<a href="about.php" class="boxed-btn mt-4">know more</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end advertisement section -->
	
	<!-- shop banner -->
	<section class="shop-banner">
    	<div class="container">
        	<h3>December sale is on! <br> with big <span class="orange-text">Discount...</span></h3>
            <div class="sale-percent"><span>Sale! <br> Upto</span>50% <span>off</span></div>
            <a href="shop.php" class="cart-btn btn-lg">Shop Now</a>
        </div>
    </section>
	<!-- end shop banner -->

	

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
							<li><a href="index.php">Home</a></li>
							<li><a href="about.php">About</a></li>
							<li><a href="services.php">Shop</a></li>
							<li><a href="news.html">News</a></li>
							<li><a href="contact.php">Contact</a></li>
						</ul>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div class="footer-box subscribe">
						<h2 class="widget-title">Subscribe</h2>
						<p>Subscribe to our mailing list to get the latest updates.</p>
						<form action="index.php">
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

	<script src="index.js"></script>

<script>
    window.onload = function() {
        // Check if selected address ID is empty
        var selectedAddressId = "<?php echo isset($_SESSION['selected_address_id']) ? $_SESSION['selected_address_id'] : ''; ?>";
        if (selectedAddressId === '') {
            setTimeout(function() {
                openPopupChoose();
            }, 3000); // Adjust the delay time (in milliseconds) as needed
        }
    };
</script>

<script>
    function handleRadioClick(radioButton) {
        // Check if the radio button is being unchecked
        if (!radioButton.checked) {
            // Unset the session variable synchronously
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'unset_selected_address.php', false); // Synchronous request
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send(); // No need to pass any data since you're unsetting the session variable

            if (xhr.status === 200) {
                // Session variable unset successfully
                console.log('Session variable unset successfully.');
            } else {
                console.log('Error unsetting session variable.');
            }
        }
    }
</script>

<script>
    $(document).ready(function(){
        $("#barista-carousel").owlCarousel({
            items: 3, // Number of items to show at a time
            loop: true, // Infinite loop
            autoplay: true, // Autoplay enabled
            margin: 1, // Margin between items
            responsive:{
                0:{
                    items:1, // Number of items to show on smaller screens
                    nav:false
                },
                600:{
                    items:2, // Number of items to show on medium-sized screens
                    nav:false
                },
                1000:{
                    items:3, // Number of items to show on larger screens
                    nav:false,
                    loop:true
                }
            }
        });
    });
</script>

</body>
</html>