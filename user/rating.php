<?php
	include("db_connection.php");
	session_start();
	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	$custid = $_SESSION['customer_id'];

	if (!isset($custid)){
		header("location:sign-in.php");
		exit;
	}

	$sql = "SELECT * FROM `customer` WHERE C_ID = '$custid'";
	$gotResult2 = mysqli_query($con, $sql);

	if($gotResult2){
		if(mysqli_num_rows($gotResult2)>0){
			while($row = mysqli_fetch_array($gotResult2)){

				$firstname = $row['C_Firstname'];
				$lastname = $row['C_Lastname'];
				$phno = $row['C_ContactNumber'];
				$Email = $row['C_Email'];
				$password = $row['C_PW'];
			}
		}
	}
	$money = 0;
	$total = 0;
	$ratingfound = 0;
	if(isset($_GET['orderid'])){
		$orderid = mysqli_real_escape_string($con, $_GET['orderid']);
		$sql12 = "SELECT * FROM rating WHERE O_ID = ?";
		$stmt12 = $con->prepare($sql12);
		$stmt12->bind_param("i", $orderid);
		$stmt12->execute();
		$result12 = $stmt12 -> get_result();
		if($result12->num_rows == 0){
			$sql2 = "SELECT * FROM reservation WHERE O_ID = ? LIMIT 1";
			$stmt2 = $con->prepare($sql2);
			$stmt2 -> bind_param("i", $orderid);
			$stmt2 -> execute();
			$result2 = $stmt2->get_result();
			if($row2 = $result2->fetch_assoc()){
				$ctid = $row2['CT_ID'];
				$rid = $row2['R_ID'];
				$total = (float)$row2['Total'];
			}

			$sql3 = "SELECT * FROM Cart_Item WHERE CT_ID = ? GROUP BY P_ID";
			$stmt3 = $con->prepare($sql3);
			$stmt3 -> bind_param("i", $ctid);
			$stmt3 ->execute();
			$result3 = $stmt3 -> get_result();
			$cartitems = [];
			if($result3->num_rows > 0){
				while($row3 = $result3->fetch_assoc()){
					$cartitems[] = $row3;
				}
			}
			$sql4 = "SELECT * FROM rider WHERE R_ID = ?";
			$stmt4 = $con->prepare($sql4);
			$stmt4 -> bind_param("s", $rid);
			$stmt4->execute();
			$result4 = $stmt4->get_result();
			if($row4 = $result4->fetch_assoc()){
				$rimage = $row4['R_Photo'];
				$rname = $row4['R_Name'];
				$money = (float)$row4['Money_Earned'];
			}
			
			$sql6 = "INSERT INTO rating (O_ID, R_ID, Rating_R) VALUES (?, ?, null)";
			$stmt6 = $con->prepare($sql6);
			$stmt6 -> bind_param("is", $orderid, $rid);
			$result6 = $stmt6 -> execute();
			if($result6){
				?>
				<!-- <script>
					alert("Create successful");
				</script> -->
				<?php
			}
			
			$stmt6->close();
		} else if($result12 ->num_rows > 0){
			$ratingfound = 1;
			while($row12 = $result12->fetch_assoc()){
				$ratingid = $row12['Ra_ID'];
				$riderid2 = $row12['R_ID'];
				$rating_r = $row12['Rating_R'];
				$comment_r = $row12['Comment_R'];
			}
			$sql2 = "SELECT * FROM reservation WHERE O_ID = ? LIMIT 1";
			$stmt2 = $con->prepare($sql2);
			$stmt2 -> bind_param("i", $orderid);
			$stmt2 -> execute();
			$result2 = $stmt2->get_result();
			if($row2 = $result2->fetch_assoc()){
				$ctid = $row2['CT_ID'];
				$rid = $row2['R_ID'];
				$total = (float)$row2['Total'];
			}

			$sql3 = "SELECT * FROM Cart_Item WHERE CT_ID = ? GROUP BY P_ID";
			$stmt3 = $con->prepare($sql3);
			$stmt3 -> bind_param("i", $ctid);
			$stmt3 ->execute();
			$result3 = $stmt3 -> get_result();
			$cartitems = [];
			if($result3->num_rows > 0){
				while($row3 = $result3->fetch_assoc()){
					$cartitems[] = $row3;
				}
			}
			$sql4 = "SELECT * FROM rider WHERE R_ID = ?";
			$stmt4 = $con->prepare($sql4);
			$stmt4 -> bind_param("s", $rid);
			$stmt4->execute();
			$result4 = $stmt4->get_result();
			if($row4 = $result4->fetch_assoc()){
				$rimage = $row4['R_Photo'];
				$rname = $row4['R_Name'];
				$money = (float)$row4['Money_Earned'];
			}
		}
		
	}
	echo '<script>console.log("total: " + '.$total.'); console.log("money: " + '.$money.')</script>';
	$Ra_ID = 0;
	$Ra_ID = intval(mysqli_insert_id($con));
	// echo $Ra_ID;
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if(isset($_POST['pid'])&&(isset($_POST['starvalue']))&&(isset($_POST['comment']))){
			$pid = $_POST['pid'];
			$raid1 = $_POST['raid'];
			$starvalue1 = $_POST['starvalue'];
			$comment1 = isset($_POST['comment']) ? $_POST['comment'] : ''; // Check if comment is set
			$sql7 = "INSERT INTO rate_product (Ra_ID, P_ID, Rating_product, Comment_Product) VALUES (?, ?, ?, ?)";
			$stmt7  = $con->prepare($sql7);
			$stmt7->bind_param("iiss", $raid1, $pid, $starvalue1, $comment1);
			$result7 = $stmt7->execute();
			if(!$stmt7){
				echo "Error adding rating to product".$stm7->error_log();
			}
			$stmt7->close();
		}
	}
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		try {
			if (isset($_POST['riderid'])){
				$riderid = $_POST['riderid'];
				$raid2 = $_POST['raid'];
				$starvalue2 = (int)$_POST['starvalue2'];
				$comment2 = isset($_POST['comment2']) ? $_POST['comment2'] : ''; // Check if comment is set
				$addingfee = 0;
				$newmoney = 0;
				$sql8 = "UPDATE rating SET Rating_R = ?, Comment_R = ? WHERE Ra_ID = ?";
				$stmt8 = $con->prepare($sql8);
				if (!$stmt8) {
					throw new Exception("Failed to prepare statement: " . $con->error);
				}
				$stmt8->bind_param("isi", $starvalue2, $comment2, $raid2);
				$result8 = $stmt8->execute();
				if(!$result8){
					throw new Exception("Error adding rating to rider: " . $stmt8->error);
				}
				$affected_rows = $stmt8->affected_rows;
				if ($affected_rows < 1) {
					throw new Exception("No rows updated. Possibly no matching rows found.");
				}
				if($starvalue2 == 5){
					$addingfee = (float)($_POST['ordertotal'] * 0.05);
					$newmoney = (float)($_POST['ridermoney'] + $addingfee);
					$sql11 = "UPDATE rider SET Money_Earned = ? WHERE R_ID = ?";
					$stmt11 = $con->prepare($sql11);
					$stmt11 -> bind_param("ds", $newmoney, $riderid);
					$stmt11->execute();
					$affected_rows11 = $stmt11->affected_rows;
					if ($affected_rows11 < 1) {
						throw new Exception("No rows updated for rider. Possibly no matching rows found.");
					}
				}
				$stmt8->close();
			}
		} catch (Exception $e) {
			echo "Error: " . $e->getMessage();
		}
	}
	if (isset($_POST['row_id'])) {
		$raid = $_POST['row_id'];
		$sql10 = "DELETE FROM rating WHERE Ra_ID = ?";
		$stmt10 = $con->prepare($sql10);
		$stmt10->bind_param("i", $raid);
		if ($stmt10->execute()) {
			http_response_code(200);
			echo "Rating row deleted successfully.";
		} else {
			http_response_code(500);
			echo "Error deleting rating row: " . $stmt10->error;
		}

		// Close the database connection
		$stmt10->close();
		mysqli_close($con);
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Responsive Bootstrap4 Shop Template, Created by Imran Hossain from https://imransdesign.com/">

    <title>Rating</title>
    
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

    <!-- icon -->
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
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
								<img src="assets/img/full-white.png" alt="">
							</a>
						</div>
						<!-- logo -->

						<!-- menu start -->
						<nav class="main-menu">
							<ul>
								<li class=""><a href="index.php">Home</a></li>
								<li><a href="shop.php">Menu</a></li>
								<li><a href="promo.php"> Show promo</a></li>
								<li><a href="about.php">About Us</a></li>
								<li class="current-list-item"><a href="history.php">Order History</a></li>
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
										<a class="shopping-cart" href="profile.php"><i class="fas fa-user"></i><span id="firstname"> Welcome <?php echo $firstname; ?></span></a>
										<a class="shopping-cart logout" href="logout.php" onclick="return confirmLogout();"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
										
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
						<h1>Rating</h1>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end breadcrumb section -->

    <!-- Rating Container -->
    <div class="rating-session mt-150 mb-150">
        <div class="container">
                <div class="col-lg-12">
                    <div class="rateproductwrap">
                        <h4>Rate for Products</h4>
                        <div class="rateproductbody">
							<?php
								if(!$ratingfound){
									foreach($cartitems as $index => $cartitem){
										if ($index % 2 == 0) {
											echo '<div class="row">';
										}
										$sql5 = "SELECT * FROM product WHERE P_ID = ?";
										$stmt5 = $con->prepare($sql5);
										$stmt5 -> bind_param("i", $cartitem['P_ID']);
										$stmt5 -> execute();
										$result5 = $stmt5->get_result();
										if($row5 = $result5->fetch_assoc()){
											$pimage = $row5['P_Photo'];
											$pname = $row5['P_Name'];
										}
										echo '<div class="col-md-6">';
										echo '<div class="ratebox product'.$cartitem['P_ID'].'">';
										echo '<div class="productdetail">';
										echo '<input type="hidden" class="ratetype" value="product">';
										echo '<input type="hidden" class="rateid" value="' . $cartitem['P_ID'] . '">';
										echo '<div class="pphoto"><img src="../image/product/'.$pimage.'" alt="'.$pname.'-image"></div>';
										echo '<div class="pname">'.$pname.'</div>';
										echo '</div>';
										echo '<form class="ratedetail">';
										echo '<div class="stars">';
										echo '<input type="number" name="rating" hidden>';
										echo '<i class="bx bx-star star" data-value="1" style="--i: 0;"></i>';
										echo '<i class="bx bx-star star" data-value="2" style="--i: 1;"></i>';
										echo '<i class="bx bx-star star" data-value="3" style="--i: 2;"></i>';
										echo '<i class="bx bx-star star" data-value="4" style="--i: 3;"></i>';
										echo '<i class="bx bx-star star" data-value="5" style="--i: 4;"></i>';
										echo '</div>';
										echo '<textarea name="opinion" class="opinion" cols="30" rows="5" placeholder="Your opinion..."></textarea>';
										echo '</form>';
										echo '</div>';
										echo '</div>';
										if ($index % 2 != 0 || $index == count($cartitems) - 1) {
											echo '</div>';
										}
									}
								}else if($ratingfound){
									foreach($cartitems as $index => $cartitem){
										if ($index % 2 == 0) {
											echo '<div class="row">';
										}
										$sql5 = "SELECT * FROM product WHERE P_ID = ?";
										$stmt5 = $con->prepare($sql5);
										$stmt5 -> bind_param("i", $cartitem['P_ID']);
										$stmt5 -> execute();
										$result5 = $stmt5->get_result();
										if($row5 = $result5->fetch_assoc()){
											$pimage = $row5['P_Photo'];
											$pname = $row5['P_Name'];
										}
										$sql13 = "SELECT * FROM rate_product WHERE P_ID = ? AND Ra_ID = ?";
										$stmt13 = $con->prepare($sql13);
										$stmt13->bind_param("ii", $cartitem['P_ID'], $ratingid);
										$stmt13->execute();
										$result13 = $stmt13->get_result();
										if($result13->num_rows == 1){
											while($row13 = $result13 -> fetch_assoc()){
												$rating_p = $row13['Rating_Product'];
												$comment_p = $row13['Comment_Product'];
											}
										}
										echo '<div class="col-md-6">';
										echo '<div class="ratebox product'.$cartitem['P_ID'].'">';
										echo '<div class="productdetail">';
										echo '<input type="hidden" class="ratetype" value="product">';
										echo '<input type="hidden" class="rateid" value="' . $cartitem['P_ID'] . '">';
										echo '<div class="pphoto"><img src="../image/product/'.$pimage.'" alt="'.$pname.'-image"></div>';
										echo '<div class="pname">'.$pname.'</div>';
										echo '</div>';
										echo '<form class="ratedetail">';
										echo '<div class="stars">';
										echo '<input type="number" name="rating" hidden>';
										for($i = 1; $i <= $rating_p; $i++) {
											echo '<i class="bx bxs-star star active" data-value="' . $i . '" style="--i: ' . ($i - 1) . '"></i>';
										}
										for($i = $rating_p + 1; $i <= 5; $i++) {
											echo '<i class="bx bx-star star" data-value="' . $i . '" style="--i: ' . ($i - 1) . '"></i>';
										}
										echo '</div>';
										echo '<textarea name="opinion" class="opinion" cols="30" rows="5" placeholder="Your opinion..." readonly>'. $comment_p .'</textarea>';
										echo '</form>';
										echo '</div>';
										echo '</div>';
										if ($index % 2 != 0 || $index == count($cartitems) - 1) {
											echo '</div>';
										}
									}
								}
							?>
							
                        </div>
                    </div>
                    <div class="rateriderwrap">
                        <h4>Rate your rider</h4>
                        <div class="ratebox raterider">
							<?php if(!$ratingfound){ ?>
								<div class="productdetail">
									<input type="hidden" class="ratetype" value="rider">
									<input type="hidden" class="rateid" value="<?php echo $rid; ?>">
									<div class="pphoto"><img src="../image/rider/<?php echo $rimage; ?>" alt="image"></div>
									<div class="pname"><?php echo $rname; ?></div>
								</div>
								<form class="ratedetail">
									<input type="number" name="rating" class="rating" hidden>
									<div class="stars">
										<i class='bx bx-star star' data-value="1" style="--i: 0;"></i>
										<i class='bx bx-star star' data-value="2" style="--i: 1;"></i>
										<i class='bx bx-star star' data-value="3" style="--i: 2;"></i>
										<i class='bx bx-star star' data-value="4" style="--i: 3;"></i>
										<i class='bx bx-star star' data-value="5" style="--i: 4;"></i>
									</div>
									<textarea name="opinion" class="opinion" placeholder="Your opinion..."></textarea>
									
								</form>
							<?php } else { ?>
								<div class="productdetail">
									<input type="hidden" class="ratetype" value="rider">
									<input type="hidden" class="rateid" value="<?php echo $rid; ?>">
									<div class="pphoto"><img src="../image/rider/<?php echo $rimage; ?>" alt="image"></div>
									<div class="pname"><?php echo $rname; ?></div>
								</div>
								<form class="ratedetail">
									<input type="number" name="rating" class="rating" hidden>
									<div class="stars">
										<?php
											for($i = 1; $i <= $rating_r; $i++) {
												echo '<i class="bx bxs-star star active" data-value="' . $i . '" style="--i: ' . ($i - 1) . '"></i>';
											}
											for($i = $rating_r + 1; $i <= 5; $i++) {
												echo '<i class="bx bx-star star" data-value="' . $i . '" style="--i: ' . ($i - 1) . '"></i>';
											}
										?>
									</div>
									<textarea name="opinion" class="opinion" placeholder="Your opinion..." readonly><?php echo $comment_r; ?></textarea>
									
								</form>
							<?php } ?>
							</div>
                    </div>
					<?php if(!$ratingfound){ ?>
                    <div class="btn-group">
                        <button class="btn submit">Submit</button>
                    </div>
					<?php } ?>
                </div>
            
        </div>
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
	<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
	<script>
		$(document).ready(function(){
			if(<?php echo $ratingfound ?> == 0){
				// Select all rateboxes
				const allRateboxes = document.querySelectorAll('.ratebox');

				// Iterate over each ratebox
				allRateboxes.forEach(ratebox => {
					// Find stars and input elements within this ratebox
					const stars = ratebox.querySelectorAll('.stars .star');
					const ratingValue = ratebox.querySelector('.ratedetail input');

					// Add click event listener to stars within this ratebox
					stars.forEach((star, idx) => {
						star.addEventListener('click', function () {
							let click = 0;

							// Set rating value for this ratebox
							ratingValue.value = idx + 1;

							// Update star classes for this ratebox
							stars.forEach((s, i) => {
								if (i <= idx) {
									s.classList.replace('bx-star', 'bxs-star');
									s.classList.add('active');
								} else {
									s.classList.replace('bxs-star', 'bx-star');
									s.classList.remove('active');
								}
							});
						});
					});
				});
			}
		});
		let submitButtonClicked = false;
		$(document).ready(function(){
			$('.submit').on('click', function(e){
				e.preventDefault();
				let submitButtonClicked = true;
				let allRateboxesFilled = true;
				$('.ratebox').each(function(){
					var $box = $(this);
					var $rtype = $box.find('.ratetype').val();
					var starvalue = null; // Declare starvalue outside the event handler
					var icon = $box.find('.star'); // Define icon here
					icon.each(function() {
						if ($(this).hasClass('active')) { // Use $(this) instead of icon
							var starValue = parseInt($(this).data('value')); // Use $(this) instead of icon
							starvalue = parseInt(starValue); // Assign starValue to starvalue variable
						}
					});
					if(starvalue === null){
						allRateboxesFilled = false;
						// alert("Please fill all the ratings.");
						swal({
							title: 'Rating needed',
							text: 'Please fill all the ratings',
							icon: 'warning',
							button: 'OK'
						});
						return false; // Exit the loop if any ratebox is incomplete
					}
				});
				// Check if all rateboxes are filled
				if (!allRateboxesFilled) {
					return; // Prevent form submission if any ratebox is incomplete
				}
				$('.ratebox').each(function(){
					var $box = $(this);
					var $rtype = "";
					$rtype = $box.find('.ratetype').val();
					var $rateid = $box.find('.rateid').val();
					var starvalue = null; // Declare starvalue outside the event handler
					var icon = $box.find('.star'); // Define icon here
					icon.each(function() {
						if ($(this).hasClass('active')) { // Use $(this) instead of icon
							var starValue = parseInt($(this).data('value')); // Use $(this) instead of icon
							// console.log('Clicked star value:', starValue);
							starvalue = parseInt(starValue); // Assign starValue to starvalue variable
						}
					});
					var raid = parseInt(<?php echo $Ra_ID; ?>);
					console.log('Clicked star value:', starvalue);
					var comment = $box.find('.opinion').val();
					if($rtype == "order") {
						var $oid = parseInt($rateid);
						$.ajax({
							url: 'rating.php',
							type: 'POST',
							data: {
								// oid: $oid,
								raid: raid,
								starvalue3: starvalue,
								comment3: comment,
							},
							success: function(response){
								console.log("order: "+starvalue);
								// console.log($oid);s
								console.log(raid);
								console.log("order:"+$rtype);
								console.log("commentorder: "+comment);
								// console.log(response);
							}
						});
					}
					if($rtype == "rider") {
						var $riderid = $rateid;
						var ordertotal = <?php echo $total; ?>;
						var ridermoney = <?php echo $money; ?>;
						$.ajax({
							url: 'rating.php',
							type: 'POST',
							data: {
								riderid: $riderid,
								raid: raid,
								starvalue2: starvalue,
								comment2: comment,
								ordertotal: ordertotal,
								ridermoney: ridermoney,
							},
							success: function(response){
								console.log("rider:"+starvalue);
								console.log($riderid);
								console.log(raid);
								console.log("rider:"+$rtype);
								console.log("commentrider: "+comment);
								// console.log(response);
							}
						});
					}
					if ($rtype == "product") {
						var $pid = parseInt($rateid);
						$.ajax({
							url: 'rating.php',
							type: 'POST',
							data: {
								pid: $pid,
								raid: raid,
								starvalue: starvalue, // Use the 'starvalue' variable here
								comment: comment,
							},
							success: function(response) {
								console.log(starvalue);
								console.log("commentproduct: "+comment);
								// console.log(response);
							}
						});
					}
				});
				window.location.href="history.php";
			});
		});
		$(window).on('beforeunload', function() {
			// Make an AJAX request to delete the row
			if(!submitButtonClicked){
				var raid = parseInt(<?php echo $Ra_ID ?>);
				$.ajax({
					url: 'rating.php',
					type: 'POST',
					data: {
						row_id: raid, // You need to pass the identifier of the row to delete
					},
					success: function(response){
						console.log('Row deleted successfully');
					},
					error: function(xhr, status, error) {
						console.error('Error deleting row:', error);
					}
				});
			}
		});
		function confirmLogout() {
			// Display a confirmation dialog using SweetAlert
			swal({
				title: 'Are you sure?',
				text: "You will be logged out!",
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
						text: "Yes, log me out!",
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