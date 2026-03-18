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

// fetch newest products
$query = "SELECT * FROM `product` WHERE `P_Status` = 'yes' ORDER BY `P_ID` DESC LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "ii", $productsPerPage, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
//------------------------------------------------------------------------------------
// Retrieve user's information if signed in
if ($isSignedIn)
{
    $sql = "SELECT * FROM `customer` WHERE C_ID = ?";
    $stmtUser = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmtUser, "s", $currectuser);
    mysqli_stmt_execute($stmtUser);
    $gotResult = mysqli_stmt_get_result($stmtUser);

    if ($gotResult && mysqli_num_rows($gotResult) > 0) 
	{
        $row = mysqli_fetch_array($gotResult);
        $firstname = $row['C_Firstname'];
        $lastname = $row['C_Lastname'];
        $phno = $row['C_ContactNumber'];
        $Email = $row['C_Email'];
        $password = $row['C_PW'];
    }
	//-------------------------------------------------------------------------------
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
	//------------------------------------------------------------------------------
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
	<title>Home Page - Cuppa Joy</title>

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

	<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
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
								<li class="current-list-item"><a href="index.php">Home</a></li>
								<li><a href="shop.php">Menu</a></li>
								<li><a href="promo.php"> Show Promo</a></li>
								<li><a href="about.php">About Us</a></li>
								<!-- <li><a href="history.php">History</a></li> -->

								<?php
										if(isset($_SESSION["customer_id"])) 
										{
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
															text: "You need to sign in to access your wishlist.",
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
	<!-- end search area -->

	<!-- hero area -->
	<div class="hero-area hero-bg">
		<div class="container">
			<div class="row">
				<div class="col-lg-9 offset-lg-2 text-center">
					<div class="hero-text">
						<div class="hero-text-tablecell">
							<p class="subtitle">Delightful & Delicious</p>
							<h1>A Cup of Comfort, A Dash of Joy</h1>
							<div class="hero-btns">
								<a href="shop.php" class="boxed-btn">Menu Collection</a>
								<a href="contact.php" class="bordered-btn" >Contact Us</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end hero area -->

	<!-- features list section -->
<div class="list-section pt-80 pb-80">
<div class="container">

<div class="row">
	<div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
		<div class="list-box d-flex align-items-center">
			<div class="list-icon">
				<i class="fas fa-gift"></i>
			</div>
			<div class="content">
				<h3>Welcome Gift</h3>
				<p>We have gift an one-time-claim coupon for new customer</p>
			</div>
		</div>
	</div>
	<div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
		<div class="list-box d-flex align-items-center">
			<div class="list-icon">
				<i class="fas fa-phone-volume"></i>
			</div>
			<div class="content">
				<h3>Last Order</h3>
				<p>Close order at 9.30 p.m.</p>
			</div>
		</div>
	</div>
	<div class="col-lg-4 col-md-6">
		<div class="list-box d-flex justify-content-start align-items-center">
			<div class="list-icon">
				<i class="fas fa-shopping-cart"></i>
			</div>
			<div class="content">
				<h3>Order Limited</h3>
				<p>Only able to order 12 item each time</p>
			</div>
		</div>
	</div>
</div>

</div>
</div>
<!-- end features list section -->



	<!-- product section -->
	<div class="product-section mt-150 mb-150">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 offset-lg-2 text-center">
					<div class="section-title">	
						<h3><span class="orange-text">New</span> Drinks or Foods</h3>
						<p>Explore new and favorite Cuppa Joy drink and food products. Order online and get your favourites now</p>
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
	
	

<!-- cart banner section -->
<?php
include("db_connection.php");

$query_cart_items = "SELECT * FROM cart_item WHERE CT_ID IN 
                        (SELECT CT_ID FROM cart WHERE C_Status = 'paid')";
$result_cart_items = mysqli_query($con, $query_cart_items);

// Check if cart items exist
if (mysqli_num_rows($result_cart_items) > 0) 
{
?>
    <!-- cart banner section -->
    <section class="cart-banner pt-100 pb-100">
        <div class="container">
            <!-- Content Column -->
            <div class="content-column col-lg-3 col-md-2">
                <h3><span class="orange-text"> Popular Drinks or Foods</span></h3>
                <div class="row">
                    <?php
                    // Step 2: Calculate Quantity Sold per Product
                    $product_quantities = array();
                    $count = 0; // Initialize a counter
                    while ($row_cart_item = mysqli_fetch_assoc($result_cart_items)) {
                        $p_id = $row_cart_item['P_ID'];
                        $quantity_sold = $row_cart_item['Qty'];

                        if (!isset($product_quantities[$p_id])) {
                            $product_quantities[$p_id] = 0;
                        }

                        $product_quantities[$p_id] += $quantity_sold;

                        // Increment the counter
                        $count++;
                        // Check if the counter reaches 5
                        if ($count % 6 == 0) {
                            echo "</div><div class='row'>";
                        }
                    }

                    // Step 3: Rank Products by Quantity Sold
                    arsort($product_quantities);

                    // Step 4: Retrieve Product Information for Top 8 Products
                    $top_products = array_slice($product_quantities, 0, 6, true);

                    // Step 5: Display the Results
                    foreach ($top_products as $product_id => $quantity_sold) {
                        $query_product = "SELECT * FROM product WHERE P_ID = $product_id";
                        $result_product = mysqli_query($con, $query_product);
                        $product_info = mysqli_fetch_assoc($result_product);
                    ?>
                        <div class="col-md-2">
                            <div class="popular">
                                <div class="popular-img">
                                    <a href="single-product.php?product_id=<?php echo $product_info['P_ID']; ?>"><img src="../image/product/<?php echo $product_info['P_Photo']; ?>" alt=""></a>
                                </div>
                                <div class="popular-desc">
                                    <h4><a href="single-product.php?product_id=<?php echo $product_info['P_ID']; ?>"><?php echo $product_info['P_Name']; ?></a></h4>
									   <p class="description"><?php echo $product_info['P_Desc']; ?></p>
                                    <div class="text">RM <?php echo $product_info['P_Price']; ?><a href="single-product.php?product_id=<?php echo $product_info['P_ID']; ?>" class="cart-btn cart mt-3"><i class="fas fa-shopping-cart"></i></a></div>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
                <a href="shop.php" class="cart-btn popular-cart mt-3"><i class="fas fa-arrow-right"></i> Show Menu</a>
            </div> <!-- End content-column -->
        </div> <!-- End container -->
    </section> <!-- End cart-banner -->
<?php
} // End if statement checking for cart items
?>
 <!-- End cart-banner -->

 <!-- latest news -->
 <div class="latest-news pt-150 pb-150">
		<div class="container">

		<div class="row">
            <div class="col-lg-8 offset-lg-2 text-center">
                <div class="section-title">
                    <h3>Our <span class="orange-text">Baristas</span></h3>
                    <p>Hi, we're the baristas at Cuppa Joy, dedicated to crafting the perfect drinks for everyone.</p>
                </div>
            </div>
        </div>

			<div class="row owl-carousel owl-theme" id="barista-carousel">
				<?php
				// PHP code to fetch and display staff members
				include("db_connection.php");

				$query_staff = "SELECT * FROM `barista` WHERE `barista_status`='Active' "; // Fetch only barista staff
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
	<!-- end latest news -->

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