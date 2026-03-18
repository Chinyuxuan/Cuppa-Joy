
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

// Initialize an array to store promo IDs used by the current user
$usedPromoIds = array();

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
//-------------------------------------------------------------------------------
// If the user is signed in, fetch the promo IDs used by the user
if ($isSignedIn) {
    $sqlUsedPromoIds = "SELECT Promo_ID FROM `promo_history` WHERE Cus_ID = ?";
    $stmtUsedPromoIds = mysqli_prepare($con, $sqlUsedPromoIds);
    mysqli_stmt_bind_param($stmtUsedPromoIds, "s", $currectuser);
    mysqli_stmt_execute($stmtUsedPromoIds);
    $resultUsedPromoIds = mysqli_stmt_get_result($stmtUsedPromoIds);

    if ($resultUsedPromoIds && mysqli_num_rows($resultUsedPromoIds) > 0) {
        while ($row = mysqli_fetch_assoc($resultUsedPromoIds)) {
            // Store the used promo IDs in the array
            $usedPromoIds[] = $row['Promo_ID'];//stor the used promo code in array
        }
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
	<title>Promos - Cuppa Joy</title>

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
	<link rel="stylesheet" href="promo.css">
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
								<li class="current-list-item"><a href="promo.php"> Show Promo</a></li>
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
						<h1>Our Coupons</h1>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end breadcrumb section -->


	<div class="container-promo">

	<?php
	// Connect to your database
	include("db_connection.php");

	$currentDate = date("Y-m-d"); // Get the current date

	// Base SQL query to fetch promos based on end date
	$sqlCoupon = "SELECT * FROM promo WHERE End_By >= ?";

	// If the user is signed in and has used promos, exclude those promos from the result
	if ($isSignedIn && !empty($usedPromoIds)) {
		$placeholders = implode(",", array_fill(0, count($usedPromoIds), "?"));
		$sqlCoupon .= " AND Promo_ID NOT IN ($placeholders)";
	}

	$stmtCoupon = $con->prepare($sqlCoupon);

	if ($isSignedIn && !empty($usedPromoIds)) {
		$params = array_merge([$currentDate], $usedPromoIds);
		$stmtCoupon->bind_param(str_repeat('s', count($params)), ...$params);
	} else {
		$stmtCoupon->bind_param('s', $currentDate);
	}

	$stmtCoupon->execute();
	$result_Coupon = $stmtCoupon->get_result();

	//If there are rows, it loops through each row and formats the dates. It then outputs HTML to display the promo details.
	if ($result_Coupon->num_rows > 0) 
	{
		// Output data of each row
		while ($row = $result_Coupon->fetch_assoc()) {
			// Convert dates to the desired format
			$startFrom = date("d M Y", strtotime($row["Start_From"]));
			$validTill = date("d M Y", strtotime($row["End_By"]));

			echo '
				<div class="coupon-card">
					<img src="assets/img/full-black.png" class="logo">
					<h3>Discount offer : ' . intval($row["Discount"]) . '% </h3>
					<div class="coupon-row">
						<span class="cpnCode">' . $row["Promo_Name"] . '</span>
						<span class="cpnBtn">Copy Code</span>
					</div>
					<p class="start">Start From: ' . $startFrom . '</p>
					<p class="end">Valid Till: ' . $validTill . '</p>
					<div class="circle1"></div>
					<div class="circle2"></div>
				</div>
			';
		}
	} else {
		echo '<div class="empty-list">';
		echo '<h2>No coupons</h2>';
		echo '<img src="assets/img/coupon.png">';
		echo '</div>';
	}

	// Close the database connection
	$stmtCoupon->close();
	$con->close();
	?>


</div>


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
var cpnBtns = document.querySelectorAll(".cpnBtn");
var cpnCodes = document.querySelectorAll(".cpnCode");

console.log("Buttons:", cpnBtns);
console.log("Codes:", cpnCodes);

cpnBtns.forEach(function(btn, index) {
    btn.addEventListener("click", function() {
        console.log("Button clicked");
        navigator.clipboard.writeText(cpnCodes[index].innerHTML)
        .then(function() {
            console.log("Coupon code copied:", cpnCodes[index].innerHTML);
            btn.innerHTML ="COPIED";
            setTimeout(function(){
                btn.innerHTML="COPY CODE";
            }, 3000);
        })
        .catch(function(error) {
            console.error("Clipboard copy failed:", error);
        });
    });
});

</script>
</body>
</html>