
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
	<title>About Us - Cuppa Joy</title>

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
	<link rel="stylesheet" href="about.css">
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
								<li><a href="promo.php"> Show Promo</a></li>
								<li class="current-list-item"><a href="about.php">About Us</a></li>
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

	<!--search area -->
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
	<!-- end search arewa-->
	
	<!-- breadcrumb-section -->
	<div class="breadcrumb-section breadcrumb-bg">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 offset-lg-2 text-center">
					<div class="breadcrumb-text">
						<p>Delightful & Delicious</p>
						<h1>About Us</h1>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end breadcrumb section -->

	<!-- featured section --
	<div class="feature-bg">
		<div class="container">
			<div class="row">
				<div class="col-lg-7">
					<div class="featured-text">
						<h2 class="pb-3">Why <span class="orange-text">Fruitkha</span></h2>
						<div class="row">
							<div class="col-lg-6 col-md-6 mb-4 mb-md-5">
								<div class="list-box d-flex">
									<div class="list-icon">
										<i class="fas fa-shipping-fast"></i>
									</div>
									<div class="content">
										<h3>Home Delivery</h3>
										<p>sit voluptatem accusantium dolore mque laudantium, totam rem aperiam, eaque ipsa quae ab illo.</p>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 mb-5 mb-md-5">
								<div class="list-box d-flex">
									<div class="list-icon">
										<i class="fas fa-money-bill-alt"></i>
									</div>
									<div class="content">
										<h3>Best Price</h3>
										<p>sit voluptatem accusantium dolore mque laudantium, totam rem aperiam, eaque ipsa quae ab illo.</p>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 mb-5 mb-md-5">
								<div class="list-box d-flex">
									<div class="list-icon">
										<i class="fas fa-briefcase"></i>
									</div>
									<div class="content">
										<h3>Custom Box</h3>
										<p>sit voluptatem accusantium dolore mque laudantium, totam rem aperiam, eaque ipsa quae ab illo.</p>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6">
								<div class="list-box d-flex">
									<div class="list-icon">
										<i class="fas fa-sync-alt"></i>
									</div>
									<div class="content">
										<h3>Quick Refund</h3>
										<p>sit voluptatem accusantium dolore mque laudantium, totam rem aperiam, eaque ipsa quae ab illo.</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<-- end featured section -->

	<!-- shop banner --
	<section class="shop-banner">
    	<div class="container">
        	<h3>December sale is on! <br> with big <span class="orange-text">Discount...</span></h3>
            <div class="sale-percent"><span>Sale! <br> Upto</span>50% <span>off</span></div>
            <a href="shop.html" class="cart-btn btn-lg">Shop Now</a>
        </div>
    </section>
	<-- end shop banner -->

	<div class="vmo-container">
			<div class="text3">
			<img src="assets\img\shared-vision.png">
			<span class="vision">VISION</span><br>
			<span class="v-desc">To be the preferred community gathering spot known for exceptional coffee, warm ambiance,  and outstanding customer service</span>
			</div>

			<div class="text4">
			
			<span class="mission">MISSION</span>
			<span class="m-desc">Our mission is to provide a cozy and welcoming environment where customers can enjoy freshly brewed coffee, delicious pastries, and genuine 
				connections with friends and neighbors</span>
				<img src="assets\img\mission.png">
			</div>

			<div class="text5">
				<img src="assets\img\value.png">
				<span class="strategy">OBJECTIVE</span><br>
				<span class="s-desc">
				<ul>
					<li>Improve service and product quality to increase customer satisfaction by 15% within six months.</li>
					<li>Launch a social media campaign to boost Instagram followers by 20% in the next quarter.</li>
					<li>Strengthen employee training for better coffee preparation and customer interactions.</li>
				</ul>
				</span>
			</div>
	</div>


	<!-- team section -->
<div class="mt-150">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2 text-center">
                <div class="section-title about" >
                    <h3>Our <span class="orange-text">Team</span></h3>
                    <p>Hi, we're the baristas at Cuppa Joy, dedicated to crafting the perfect drink for everyone.</p>
                </div>
            </div>
        </div>
		<div class="row owl-carousel owl-theme" id="barista-carousel">
				<?php
				// PHP code to fetch and display staff members
				include("db_connection.php");

				$query_staff = "SELECT * FROM `barista` WHERE `barista_status`='Active'"; // Fetch only barista staff
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
						// echo '<ul class="social-link-team">';
						// echo '<li><a href="#" target="_blank"><i class="fab fa-facebook-f"></i></a></li>';
						// echo '<li><a href="#" target="_blank"><i class="fab fa-twitter"></i></a></li>';
						// echo '<li><a href="#" target="_blank"><i class="fab fa-instagram"></i></a></li>';
						// echo '</ul>';
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
    </div>
</div>
<!-- end team section -->


	<!-- testimonail-section --
	<div class="testimonail-section mt-80 mb-150">
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
	<-- end testimonail-section -->

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

	<script src="about.js"></script>

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