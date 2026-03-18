<?php
	include("db_connection.php");
	session_start();
	require_once 'PayPalConfig.php'; 

	$custid = $_SESSION['customer_id'];
	date_default_timezone_set('UTC');
	if (!isset($custid)){
		header("location:sign-in.php");
		exit;
	}

	date_default_timezone_set('UTC');
	$todaydate = date('m-d');
	$currentDate = date("Y-m-d");

	$sql = "SELECT * FROM `customer` WHERE C_ID = '$custid'";
	$gotResult = mysqli_query($con, $sql);

	if($gotResult){
		if(mysqli_num_rows($gotResult)>0){
			while($row = mysqli_fetch_array($gotResult)){

				$firstname = $row['C_Firstname'];
				$lastname = $row['C_Lastname'];
				$phno = $row['C_ContactNumber'];
				$Email = $row['C_Email'];
				// $bod = $row['C_DOB'];
				$password = $row['C_PW'];
				
			}
		}
	}
	// $customerBirthdate = date('m-d', strtotime($bod));
	// $discountamount = 0;
	// if($todaydate == $customerBirthdate){
	// 	$discountamount = 10;
	// }

	$checkCart = "SELECT * FROM `cart` WHERE C_ID = ? AND C_Status = 'No-paid'";
    $stmtCheckCart = mysqli_prepare($con, $checkCart);
    mysqli_stmt_bind_param($stmtCheckCart, "s", $custid);
    mysqli_stmt_execute($stmtCheckCart);
    $resultCheckCart = mysqli_stmt_get_result($stmtCheckCart);
    
    if (!$resultCheckCart || mysqli_num_rows($resultCheckCart) == 0) {
        $create_cart = "INSERT INTO `cart` (C_ID, C_Status) VALUES (?, 'No-paid')";
        $stmtCreateCart = mysqli_prepare($con, $create_cart);
        mysqli_stmt_bind_param($stmtCreateCart, "s", $custid);
        $gotCart = mysqli_stmt_execute($stmtCreateCart);
        if (!$gotCart) {
            echo "Error: " . mysqli_error($con);
        }
    } 

	$CartNo = "SELECT * FROM cart WHERE C_ID = ? AND C_Status = 'No-Paid'";
    $stmt = $con->prepare($CartNo);
    $stmt->bind_param("i", $custid);
    $stmt->execute();
    $result = $stmt->get_result();
	// Fetch the CT_ID from the result set
    $row = $result->fetch_assoc();
    $CT_ID = $row['CT_ID'];
	$promo_id = $row['Promo_ID'];

	// Prepare and execute the query using the fetched CT_ID
    $sql1 = "SELECT * FROM Cart_Item ci JOIN product p ON ci.P_ID = p.P_ID WHERE CT_ID = ? AND P_Status = 'yes'";
    $stmt1 = $con->prepare($sql1);
    $stmt1->bind_param("i", $CT_ID);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $cartitems = [];
	if($result1->num_rows == 0){
		header("Location: cart.php");
	}
    if($result1->num_rows > 0){
        while($row1 = $result1->fetch_assoc()){
            $cartitems[] = $row1;
        }
    }

	$sql5 = "SELECT * FROM address WHERE C_ID = ? AND Address_status = 1";
	$stmt5 = $con->prepare($sql5);
	$stmt5 -> bind_param("i", $custid);
	$stmt5 -> execute();
	$result5 = $stmt5 -> get_result();
	$addresses = [];
	if($result5->num_rows > 0){
		while($row5 = $result5->fetch_assoc()){
			$addresses[] = $row5;
		}
	}
	$discountamount = 0;
	$pcodeid = null;
	$codename = null;
	if(!is_null($promo_id)){
		$sql10 = "SELECT * FROM promo WHERE Promo_ID = ? AND Start_From <= ? AND End_By >= ?";
		$stmt10 = $con->prepare($sql10);
		$stmt10 -> bind_param("iss", $promo_id, $currentDate, $currentDate);
		$stmt10 -> execute();
		$result10 = $stmt10->get_result();
		if($row10 = $result10->fetch_assoc()){
			
			if(strtotime($row10['End_By']) >= strtotime($currentDate)) {
				$pcodeid = $row10['Promo_ID'];
				$codename = $row10['Promo_Name'];
				$discountamount = $row10['Discount'];
			} else {
				
				$sql24 = "UPDATE cart SET Promo_ID = NULL WHERE CT_ID = ? ";
				$stmt24 = $con->prepare($sql24);
				$stmt24 -> bind_param("i", $CT_ID);
				$result24 = $stmt24->execute();
				if($result24){
					// echo "<script> alert('Promo code that using is already unavailable. Please change to another promo code.');window.location.href = window.location.href; </script>";
					echo "
						<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
						<script>
							document.addEventListener('DOMContentLoaded', function() {
								swal({
									title: 'Invalid or Expired Promo Code',
									text: 'Promo code that using is already unavailable. Please change to another promo code.',
									icon: 'error',
									button: 'OK'
								}).then((result) => {
									if(result){
										window.location.href = window.location.href;
									}
								});
							});
						</script>
						";
				}
			}
		}else{
			$sql25 = "UPDATE cart SET Promo_ID = NULL WHERE CT_ID = ? ";
			$stmt25 = $con->prepare($sql25);
			$stmt25 -> bind_param("i", $CT_ID);
			$result25 = $stmt25->execute();
			if($result25){
				// echo "<script> alert('Promo code that using is already unavailable. Please change to another promo code.');window.location.href = window.location.href; </script>";
				echo "
					<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
					<script>
						document.addEventListener('DOMContentLoaded', function() {
							swal({
								title: 'Invalid or Expired Promo Code',
								text: 'Promo code that using is already unavailable. Please change to another promo code.',
								icon: 'error',
								button: 'OK'
							}).then((result) => {
								if(result){
									window.location.href = window.location.href;
								}
							});
						});
					</script>
					";
			}
		}
		
	}

	if(isset($_POST['editAdd1'])){
		$addr1 = $_POST['editAdd1'];
		$addr2 = $_POST['editAdd2'];
		$city = $_POST['editPost'];
		$csc = "Melaka, Malaysia";
		$pc = 75450;

		$sql8 = "INSERT INTO `address` (Address_1, Address_2, Postcode,City, state_country, C_ID, Address_status) VALUES (?, ?, ?, ?, ?, ?, 1)";
		$stmt8 = $con->prepare($sql8);
		$stmt8->bind_param("ssissi", $addr1, $addr2, $pc, $city, $csc, $custid);
		$result8 = $stmt8->execute();

		// $newly_added_id = mysqli_insert_id($con);
		// echo $newly_added_id;
		if($result8){
			echo "
				<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
				<script>
					document.addEventListener('DOMContentLoaded', function() {
						swal({
							title: 'Add Success',
							text: 'Address is be added successfully.',
							icon: 'success',
							button: 'OK'
						});
						
					});
				</script>
			";
			header("Location: checkout.php");
			exit;
		}
		
	}

	if(isset($_POST['rcname'], $_POST['rcphone'], $_POST['addrId'], $_POST['remark'], $_POST['ctid'], $_POST['total'])){
		$rcname = $_POST['rcname'];
		// $rcemail = $_POST['rcemail'];
		$rcphone = $_POST['rcphone'];
		$addrId = $_POST['addrId'];
		$remark = $_POST['remark'];
		$currentTime = date("H:i:s");
		$ctid = $_POST['ctid'];
		$ttl = $_POST['total'];
		$dstatus = "pending";

		$sql9 = "INSERT INTO reservation (CT_ID, ReceiverName, ReceiverPhone, A_ID, Date, Time, Remark, Total, Delivery_Status) VALUES (?, ?, ?, ?, ?, ?,  ?, ?, ?)";
		$stmt9 = $con->prepare($sql9);
		$stmt9->bind_param("ississsds", $ctid, $rcname, $rcphone, $addrId, $currentDate, $currentTime, $remark, $ttl, $dstatus);
		$result9 = $stmt9->execute();
		
		$_SESSION['O_ID'] = mysqli_insert_id($con);
		$_SESSION['promo_id'] = $pcodeid;
		echo $_SESSION['O_ID'];
	}

	if(isset($_POST['rcname2'])){
		$_SESSION['rcname2'] = $_POST['rcname2'];
		$_SESSION['rcphone2'] = $_POST['rcphone2'];
		$_SESSION['addressId2'] = (int)$_POST['addressId2'];
		$_SESSION['remark2'] = $_POST['remark2'];
		$_SESSION['ctid2'] = (int)$_POST['ctid2'];
		$_SESSION['ttl2'] = $_POST['ttl2'];

		echo $_SESSION['ctid2'];
		$sql26 = "SELECT * FROM address WHERE A_ID = ? AND C_ID = $custid";
		$stmt26 = $con->prepare($sql26);
		$stmt26 ->bind_param("i", $_SESSION['addressId2']);
		$stmt26->execute();
		$result26 = $stmt26 ->get_result();
		if($result26){
			if(mysqli_num_rows($result26)>0){
				while($row26 = $result26 -> fetch_assoc){
					$line1 = $row2['Address_1'];
					$line2 = $row2['Address_2'];
					$city = $row2['City'];
				}
			}
		}
	}
	if(isset($_POST['remarkkkkk'])){
		$_SESSION['remark2'] = $_POST['remark2'];
		echo $_SESSION['remark2'];
	}
	if(isset($_POST['reservationid'])){
		$sql8 = "DELETE FROM reservation WHERE O_ID = ?";
		$stmt8 = $con->prepare($sql8);
		$stmt8 -> bind_param("i", $_POST['reservationid']);
		$result8 = $stmt8->execute();
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
	<title>Check Out - Cuppa Joy</title>

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

	<!-- <link rel="stylesheet" href="profile.css"> -->
	<script src="https://kit.fontawesome.com/4dd5e87a71.js" crossorigin="anonymous"></script>
	<script src="https://www.paypal.com/sdk/js?client-id=<?php echo PAYPAL_SANDBOX?PAYPAL_SANDBOX_CLIENT_ID:PAYPAL_PROD_CLIENT_ID; ?>&currency=<?php echo $currency; ?>&components=buttons&disable-funding=card"></script>
</head>
<body>
	
	<!--PreLoader-->
    <div class="loader">
		<div class="loader-inner">
			<div class="circle"></div>
		</div>
    </div>
	<div class="overlay hidden">
		<div class="overlay-content">
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
								<li><a href="history.php">Order History</a></li>
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
						<h1>Check Out Product</h1>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end breadcrumb section -->

	<!-- check out section -->
	<div class="checkout-section mt-150 mb-150">
		<div class="container">
			<div class="row">
				<div class="col-lg-6">
					<div class="checkout-accordion-wrap">
						<div class="accordion" id="accordionExample">
						  <div class="card single-accordion">
						    <div class="card-header" id="headingOne">
						      <h5 class="mb-0">
						        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
						          Delivery Details
						        </button>
						      </h5>
						    </div>

						    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
						      <div class="card-body">
						        <div class="billing-address-form">
						        	<form action="" method="post"> <!-- action="index.html" -->
						        		<p>
											<label for="name">Receiver Name</label>
											<input id="name" type="text" name="name" placeholder="Please enter receiver name" value="<?php echo $firstname ." ". $lastname; ?>" required>
										</p>
						        		<!-- <p>
											<label for="email">Receiver Email</label>
											<input type="email" id="email" name="email" placeholder="Please enter receiver email" value="">
										</p> -->
										<p>
											<label for="phone">Receiver Phone</label>
											<input type="tel" id="phone" name ="phone" placeholder="Please enter receiver phone" value="<?php echo $phno; ?>" required>
										</p>
										<p class="addddrgrp"><a class="add-address-btn" onclick="openAddPopup()">Add Another Address</a></p>
						        		<p class="addressplace"><span>Select Your Address</span>
											<?php 
												$i = 0; // Initialize a counter
												foreach ($addresses as $address) {
													$i++; // Increment the counter
													$id = 'selectaddress_' . $i; // Generate a unique id
													echo '<div class="inputGroup">';
													echo '<input type="radio" id="' . $id . '" name="address" value="' . $address['A_ID'] . '" required>';
													echo '<label for="' . $id . '">' . $address['Address_1'] . ', ' . $address['Address_2'] . ', ' . $address['Postcode'] . ' ' . $address['City'] . ', ' . $address['state_country'] . '</label>';
													echo '</div>';
												}
												if($i == 0){
													echo "<div>Please add a address to delivery.</div>";
												}
											?>
										</p>
						        		<p><label for="remark">Remark</label><textarea name="bill" id="bill" cols="30" rows="10" placeholder="Remarks"></textarea></p>
										
						        	</form>
						        </div>
						      </div>
						    </div>
						  </div>
						  <!-- <div class="card single-accordion">
						    <div class="card-header" id="headingTwo">
						      <h5 class="mb-0">
						        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
						          Shipping Address
						        </button>
						      </h5>
						    </div>
						    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
						      <div class="card-body">
						        <div class="shipping-address-form">
						        	<p>Your shipping address form is here.</p>
						        </div>
						      </div>
						    </div>
						  </div> -->
						  <div class="card single-accordion">
						    <div class="card-header" id="headingThree">
						      <h5 class="mb-0">
						        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
									Payment Method
						        </button>
						      </h5>
						    </div>
						    <div id="collapseThree" class="collapse show" aria-labelledby="headingThree" data-parent="#accordionExample">
						      <div class="card-body">
						        <div class="card-details">
									<button class="dccard-btn"><i class="fa-regular fa-credit-card"></i>Debit / Credit Card</button>
									
									<!--<div class="form__radios">-->
										<!-- <div class="form__radio">
										<label for="visa">
											<svg class="icon">
												<use xlink:href="#icon-visa" />
											</svg>Visa Payment</label>
										<input checked id="visa" name="payment-method" type="radio" value="visa"/>
										</div> -->

										<!-- <div class="form__radio">
										<label for="paypal">
											<svg class="icon">
												<use xlink:href="#icon-paypal" />
											</svg>PayPal</label>
										<input id="paypal" name="payment-method" type="radio" value="PayPal"/>
										</div>

										<div class="form__radio">
										<label for="mastercard">
											<svg class="icon">
												<use xlink:href="#icon-mastercard" />
											</svg>Debit/Credit Card</label>
										<input id="mastercard" name="payment-method" type="radio" value="Card"/>
										</div>
									</div> -->
									<!-- Display status message -->
									<div id="paymentResponse" class="hidden"></div>
									
									<!-- Set up a container element for the button -->
									<div id="paypal-button-container"></div>
						        </div>
						      </div>
						    </div>
						  </div>
						</div>
					</div>
				</div>
				<svg xmlns="http://www.w3.org/2000/svg" style="display: none">
					<symbol id="icon-mastercard" viewBox="0 0 504 504">
						<path d="m504 252c0 83.2-67.2 151.2-151.2 151.2-83.2 0-151.2-68-151.2-151.2 0-83.2 67.2-151.2 150.4-151.2 84.8 0 152 68 152 151.2z" fill="#ffb600" />
						<path d="m352.8 100.8c83.2 0 151.2 68 151.2 151.2 0 83.2-67.2 151.2-151.2 151.2-83.2 0-151.2-68-151.2-151.2" fill="#f7981d" />
						<path d="m352.8 100.8c83.2 0 151.2 68 151.2 151.2 0 83.2-67.2 151.2-151.2 151.2" fill="#ff8500" />
						<path d="m149.6 100.8c-82.4.8-149.6 68-149.6 151.2s67.2 151.2 151.2 151.2c39.2 0 74.4-15.2 101.6-39.2 5.6-4.8 10.4-10.4 15.2-16h-31.2c-4-4.8-8-10.4-11.2-15.2h53.6c3.2-4.8 6.4-10.4 8.8-16h-71.2c-2.4-4.8-4.8-10.4-6.4-16h83.2c4.8-15.2 8-31.2 8-48 0-11.2-1.6-21.6-3.2-32h-92.8c.8-5.6 2.4-10.4 4-16h83.2c-1.6-5.6-4-11.2-6.4-16h-70.4c2.4-5.6 5.6-10.4 8.8-16h53.6c-3.2-5.6-7.2-11.2-12-16h-29.6c4.8-5.6 9.6-10.4 15.2-15.2-26.4-24.8-62.4-39.2-101.6-39.2 0-1.6 0-1.6-.8-1.6z" fill="#ff5050" />
						<path d="m0 252c0 83.2 67.2 151.2 151.2 151.2 39.2 0 74.4-15.2 101.6-39.2 5.6-4.8 10.4-10.4 15.2-16h-31.2c-4-4.8-8-10.4-11.2-15.2h53.6c3.2-4.8 6.4-10.4 8.8-16h-71.2c-2.4-4.8-4.8-10.4-6.4-16h83.2c4.8-15.2 8-31.2 8-48 0-11.2-1.6-21.6-3.2-32h-92.8c.8-5.6 2.4-10.4 4-16h83.2c-1.6-5.6-4-11.2-6.4-16h-70.4c2.4-5.6 5.6-10.4 8.8-16h53.6c-3.2-5.6-7.2-11.2-12-16h-29.6c4.8-5.6 9.6-10.4 15.2-15.2-26.4-24.8-62.4-39.2-101.6-39.2h-.8" fill="#e52836" />
						<path d="m151.2 403.2c39.2 0 74.4-15.2 101.6-39.2 5.6-4.8 10.4-10.4 15.2-16h-31.2c-4-4.8-8-10.4-11.2-15.2h53.6c3.2-4.8 6.4-10.4 8.8-16h-71.2c-2.4-4.8-4.8-10.4-6.4-16h83.2c4.8-15.2 8-31.2 8-48 0-11.2-1.6-21.6-3.2-32h-92.8c.8-5.6 2.4-10.4 4-16h83.2c-1.6-5.6-4-11.2-6.4-16h-70.4c2.4-5.6 5.6-10.4 8.8-16h53.6c-3.2-5.6-7.2-11.2-12-16h-29.6c4.8-5.6 9.6-10.4 15.2-15.2-26.4-24.8-62.4-39.2-101.6-39.2h-.8" fill="#cb2026" />
						<g fill="#fff">
							<path d="m204.8 290.4 2.4-13.6c-.8 0-2.4.8-4 .8-5.6 0-6.4-3.2-5.6-4.8l4.8-28h8.8l2.4-15.2h-8l1.6-9.6h-16s-9.6 52.8-9.6 59.2c0 9.6 5.6 13.6 12.8 13.6 4.8 0 8.8-1.6 10.4-2.4z" />
							<path d="m210.4 264.8c0 22.4 15.2 28 28 28 12 0 16.8-2.4 16.8-2.4l3.2-15.2s-8.8 4-16.8 4c-17.6 0-14.4-12.8-14.4-12.8h32.8s2.4-10.4 2.4-14.4c0-10.4-5.6-23.2-23.2-23.2-16.8-1.6-28.8 16-28.8 36zm28-23.2c8.8 0 7.2 10.4 7.2 11.2h-17.6c0-.8 1.6-11.2 10.4-11.2z" />
							<path d="m340 290.4 3.2-17.6s-8 4-13.6 4c-11.2 0-16-8.8-16-18.4 0-19.2 9.6-29.6 20.8-29.6 8 0 14.4 4.8 14.4 4.8l2.4-16.8s-9.6-4-18.4-4c-18.4 0-36.8 16-36.8 46.4 0 20 9.6 33.6 28.8 33.6 6.4 0 15.2-2.4 15.2-2.4z" />
							<path d="m116.8 227.2c-11.2 0-19.2 3.2-19.2 3.2l-2.4 13.6s7.2-3.2 17.6-3.2c5.6 0 10.4.8 10.4 5.6 0 3.2-.8 4-.8 4s-4.8 0-7.2 0c-13.6 0-28.8 5.6-28.8 24 0 14.4 9.6 17.6 15.2 17.6 11.2 0 16-7.2 16.8-7.2l-.8 6.4h14.4l6.4-44c0-19.2-16-20-21.6-20zm3.2 36c0 2.4-1.6 15.2-11.2 15.2-4.8 0-6.4-4-6.4-6.4 0-4 2.4-9.6 14.4-9.6 2.4.8 3.2.8 3.2.8z" />
							<path d="m153.6 292c4 0 24 .8 24-20.8 0-20-19.2-16-19.2-24 0-4 3.2-5.6 8.8-5.6 2.4 0 11.2.8 11.2.8l2.4-14.4s-5.6-1.6-15.2-1.6c-12 0-24 4.8-24 20.8 0 18.4 20 16.8 20 24 0 4.8-5.6 5.6-9.6 5.6-7.2 0-14.4-2.4-14.4-2.4l-2.4 14.4c.8 1.6 4.8 3.2 18.4 3.2z" />
							<path d="m472.8 214.4-3.2 21.6s-6.4-8-15.2-8c-14.4 0-27.2 17.6-27.2 38.4 0 12.8 6.4 26.4 20 26.4 9.6 0 15.2-6.4 15.2-6.4l-.8 5.6h16l12-76.8zm-7.2 42.4c0 8.8-4 20-12.8 20-5.6 0-8.8-4.8-8.8-12.8 0-12.8 5.6-20.8 12.8-20.8 5.6 0 8.8 4 8.8 13.6z" />
							<path d="m29.6 291.2 9.6-57.6 1.6 57.6h11.2l20.8-57.6-8.8 57.6h16.8l12.8-76.8h-26.4l-16 47.2-.8-47.2h-23.2l-12.8 76.8z" />
							<path d="m277.6 291.2c4.8-26.4 5.6-48 16.8-44 1.6-10.4 4-14.4 5.6-18.4 0 0-.8 0-3.2 0-7.2 0-12.8 9.6-12.8 9.6l1.6-8.8h-15.2l-10.4 62.4h17.6z" />
							<path d="m376.8 227.2c-11.2 0-19.2 3.2-19.2 3.2l-2.4 13.6s7.2-3.2 17.6-3.2c5.6 0 10.4.8 10.4 5.6 0 3.2-.8 4-.8 4s-4.8 0-7.2 0c-13.6 0-28.8 5.6-28.8 24 0 14.4 9.6 17.6 15.2 17.6 11.2 0 16-7.2 16.8-7.2l-.8 6.4h14.4l6.4-44c.8-19.2-16-20-21.6-20zm4 36c0 2.4-1.6 15.2-11.2 15.2-4.8 0-6.4-4-6.4-6.4 0-4 2.4-9.6 14.4-9.6 2.4.8 2.4.8 3.2.8z" />
							<path d="m412 291.2c4.8-26.4 5.6-48 16.8-44 1.6-10.4 4-14.4 5.6-18.4 0 0-.8 0-3.2 0-7.2 0-12.8 9.6-12.8 9.6l1.6-8.8h-15.2l-10.4 62.4h17.6z" />
						</g>
						<path d="m180 279.2c0 9.6 5.6 13.6 12.8 13.6 5.6 0 10.4-1.6 12-2.4l2.4-13.6c-.8 0-2.4.8-4 .8-5.6 0-6.4-3.2-5.6-4.8l4.8-28h8.8l2.4-15.2h-8l1.6-9.6" fill="#dce5e5" />
						<path d="m218.4 264.8c0 22.4 7.2 28 20 28 12 0 16.8-2.4 16.8-2.4l3.2-15.2s-8.8 4-16.8 4c-17.6 0-14.4-12.8-14.4-12.8h32.8s2.4-10.4 2.4-14.4c0-10.4-5.6-23.2-23.2-23.2-16.8-1.6-20.8 16-20.8 36zm20-23.2c8.8 0 10.4 10.4 10.4 11.2h-20.8c0-.8 1.6-11.2 10.4-11.2z" fill="#dce5e5" />
						<path d="m340 290.4 3.2-17.6s-8 4-13.6 4c-11.2 0-16-8.8-16-18.4 0-19.2 9.6-29.6 20.8-29.6 8 0 14.4 4.8 14.4 4.8l2.4-16.8s-9.6-4-18.4-4c-18.4 0-28.8 16-28.8 46.4 0 20 1.6 33.6 20.8 33.6 6.4 0 15.2-2.4 15.2-2.4z" fill="#dce5e5" />
						<path d="m95.2 244.8s7.2-3.2 17.6-3.2c5.6 0 10.4.8 10.4 5.6 0 3.2-.8 4-.8 4s-4.8 0-7.2 0c-13.6 0-28.8 5.6-28.8 24 0 14.4 9.6 17.6 15.2 17.6 11.2 0 16-7.2 16.8-7.2l-.8 6.4h14.4l6.4-44c0-18.4-16-19.2-22.4-19.2m12 34.4c0 2.4-9.6 15.2-19.2 15.2-4.8 0-6.4-4-6.4-6.4 0-4 2.4-9.6 14.4-9.6 2.4.8 11.2.8 11.2.8z" fill="#dce5e5" />
						<path d="m136 290.4s4.8 1.6 18.4 1.6c4 0 24 .8 24-20.8 0-20-19.2-16-19.2-24 0-4 3.2-5.6 8.8-5.6 2.4 0 11.2.8 11.2.8l2.4-14.4s-5.6-1.6-15.2-1.6c-12 0-16 4.8-16 20.8 0 18.4 12 16.8 12 24 0 4.8-5.6 5.6-9.6 5.6" fill="#dce5e5" />
						<path d="m469.6 236s-6.4-8-15.2-8c-14.4 0-19.2 17.6-19.2 38.4 0 12.8-1.6 26.4 12 26.4 9.6 0 15.2-6.4 15.2-6.4l-.8 5.6h16l12-76.8m-20.8 41.6c0 8.8-7.2 20-16 20-5.6 0-8.8-4.8-8.8-12.8 0-12.8 5.6-20.8 12.8-20.8 5.6 0 12 4 12 13.6z" fill="#dce5e5" />
						<path d="m29.6 291.2 9.6-57.6 1.6 57.6h11.2l20.8-57.6-8.8 57.6h16.8l12.8-76.8h-20l-22.4 47.2-.8-47.2h-8.8l-27.2 76.8z" fill="#dce5e5" />
						<path d="m260.8 291.2h16.8c4.8-26.4 5.6-48 16.8-44 1.6-10.4 4-14.4 5.6-18.4 0 0-.8 0-3.2 0-7.2 0-12.8 9.6-12.8 9.6l1.6-8.8" fill="#dce5e5" />
						<path d="m355.2 244.8s7.2-3.2 17.6-3.2c5.6 0 10.4.8 10.4 5.6 0 3.2-.8 4-.8 4s-4.8 0-7.2 0c-13.6 0-28.8 5.6-28.8 24 0 14.4 9.6 17.6 15.2 17.6 11.2 0 16-7.2 16.8-7.2l-.8 6.4h14.4l6.4-44c0-18.4-16-19.2-22.4-19.2m12 34.4c0 2.4-9.6 15.2-19.2 15.2-4.8 0-6.4-4-6.4-6.4 0-4 2.4-9.6 14.4-9.6 3.2.8 11.2.8 11.2.8z" fill="#dce5e5" />
						<path d="m395.2 291.2h16.8c4.8-26.4 5.6-48 16.8-44 1.6-10.4 4-14.4 5.6-18.4 0 0-.8 0-3.2 0-7.2 0-12.8 9.6-12.8 9.6l1.6-8.8" fill="#dce5e5" />
					</symbol>

					<symbol id="icon-paypal" viewBox="0 0 491.2 491.2">
						<path d="m392.049 36.8c-22.4-25.6-64-36.8-116-36.8h-152.8c-10.4 0-20 8-21.6 18.4l-64 403.2c-1.6 8 4.8 15.2 12.8 15.2h94.4l24-150.4-.8 4.8c1.6-10.4 10.4-18.4 21.6-18.4h44.8c88 0 156.8-36 176.8-139.2.8-3.2.8-6.4 1.6-8.8-2.4-1.6-2.4-1.6 0 0 5.6-38.4 0-64-20.8-88" fill="#263b80" />
						<path d="m412.849 124.8c-.8 3.2-.8 5.6-1.6 8.8-20 103.2-88.8 139.2-176.8 139.2h-44.8c-10.4 0-20 8-21.6 18.4l-29.6 186.4c-.8 7.2 4 13.6 11.2 13.6h79.2c9.6 0 17.6-7.2 19.2-16l.8-4 15.2-94.4.8-5.6c1.6-9.6 9.6-16 19.2-16h12c76.8 0 136.8-31.2 154.4-121.6 7.2-37.6 3.2-69.6-16-91.2-6.4-7.2-13.6-12.8-21.6-17.6" fill="#139ad6" />
						<path d="m391.249 116.8c-3.2-.8-6.4-1.6-9.6-2.4s-6.4-1.6-10.4-1.6c-12-2.4-25.6-3.2-39.2-3.2h-119.2c-3.2 0-5.6.8-8 1.6-5.6 2.4-9.6 8-10.4 14.4l-25.6 160.8-.8 4.8c1.6-10.4 10.4-18.4 21.6-18.4h44.8c88 0 156.8-36 176.8-139.2.8-3.2.8-6.4 1.6-8.8-4.8-2.4-10.4-4.8-16.8-7.2-1.6 0-3.2-.8-4.8-.8" fill="#232c65" />
						<path d="m275.249 0h-152c-10.4 0-20 8-21.6 18.4l-36.8 230.4 246.4-246.4c-11.2-1.6-23.2-2.4-36-2.4z" fill="#2a4dad" />
						<path d="m441.649 153.6c-2.4-4-4-8-7.2-12-5.6-6.4-13.6-12-21.6-16.8-.8 3.2-.8 5.6-1.6 8.8-20 103.2-88.8 139.2-176.8 139.2h-44.8c-10.4 0-20 8-21.6 18.4l-25.6 161.6z" fill="#0d7dbc" />
						<path d="m50.449 436.8h94.4l23.2-145.6c0-2.4.8-4 1.6-5.6l-131.2 131.2-.8 4.8c-.8 8 4.8 15.2 12.8 15.2z" fill="#232c65" />
						<path d="m246.449 0h-123.2c-3.2 0-5.6.8-8 1.6l-12 12c-.8 1.6-1.6 3.2-1.6 4.8l-24 150.4z" fill="#436bc4" />
						<path d="m450.449 232.8c2.4-12 3.2-23.2 3.2-34.4l-156 156c76-.8 135.2-32 152.8-121.6z" fill="#0cb2ed" />
						<path d="m248.849 471.2 12.8-80-100 100h68c9.6 0 17.6-7.2 19.2-16z" fill="#0cb2ed" />
						<g fill="#33e2ff" opacity=".6">
							<path d="m408.049 146.4 45.6 45.6c0-5.6-1.6-11.2-2.4-16.8l-40-40c-1.6 4-2.4 7.2-3.2 11.2z" />
							<path d="m396.849 180c-1.6 3.2-3.2 6.4-4.8 9.6l55.2 55.2c.8-4 1.6-8 2.4-12z" />
							<path d="m431.249 287.2c1.6-3.2 3.2-6.4 4.8-9.6l-60.8-60.8c-2.4 2.4-4 5.6-6.4 8z" />
							<path d="m335.249 250.4 69.6 69.6 7.2-7.2-68-68c-3.2 1.6-5.6 3.2-8.8 5.6z" />
							<path d="m292.849 266.4 76 76c3.2-1.6 6.4-3.2 9.6-4.8l-74.4-74.4c-4 .8-7.2 2.4-11.2 3.2z" />
							<path d="m320.849 353.6c4-.8 8.8-.8 12.8-1.6l-80-80c-4.8 0-8.8.8-13.6.8z" />
							<path d="m196.049 272.8h-6.4c-2.4 0-4.8.8-6.4.8l86.4 87.2c2.4-2.4 5.6-4.8 8.8-5.6z" />
							<path d="m164.049 314.4 94.4 94.4 2.4-12.8-94.4-94.4z" />
							<path d="m156.049 364.8 94.4 94.4 2.4-12-94.4-94.4z" />
							<path d="m150.449 403.2-1.6 12.8 75.2 75.2h5.6c2.4 0 4.8-.8 7.2-1.6z" />
							<path d="m140.049 466.4 24.8 24.8h14.4l-36.8-36.8z" />
						</g>
					</symbol>
				</svg>

				<div class="col-lg-6">
					<div class="order-details-wrap">
						<h5>Order Details</h5>
						<table class="order-details">
							<thead>
								<tr>
									<th>Product</th>
									<th>Quantity</th>
									<th>Price</th>
								</tr>
							</thead>
							<tbody class="order-details-body">
								<?php
									$subtotal = 0;
									foreach($cartitems as $cartitem){
										$pname = $cartitem['P_Name'];
										$pprice = $cartitem['P_Price'];
										echo '<tr>';
										echo '<td class="prodetail">';
										echo '<span>' . $pname . '</span>';
										$sql6 = "SELECT * FROM Details WHERE c_item_id = ? ORDER BY customize_id ASC";
										$stmt6 = $con->prepare($sql6);
										$stmt6->bind_param("i", $cartitem['CI_ID']);
										$stmt6->execute();
										$result6 = $stmt6->get_result();
										$customitems = [];
										if($result6->num_rows > 0){
											while($row6 = $result6->fetch_assoc()){
												$customitems[] = $row6;
											}
										}
										$addprice = 0;
										$priceperitem = 0;
										foreach($customitems as $customitem){
											$sql7 = "SELECT custom.*, cc.CC_Group, cc.compulsory_status FROM customization AS custom 
													INNER JOIN customize_category AS cc 
													ON custom.CC_ID = cc.CC_ID 
													WHERE custom.Custom_ID = " . $customitem['customize_id'];
											$result7 = mysqli_query($con, $sql7);
											if($result5){
												while($row3 = mysqli_fetch_array($result7)){
													$customname = $row3['Custom_Name'];
													$customprice = $row3['Custom_Price'];
													$typecc = "";
													if($row3['compulsory_status'] == "yes"){
														$typecc = "ccrequire" . $row3['CC_ID'];
													}
													echo '<span class="customitem '.$typecc.'"> - ' . $customname . '</span>';
													// echo '<br/>';
													$addprice = $addprice + $customprice;
												}
											}
										}
										echo '</div>';
										echo '</td>';
										$sql3 = "SELECT * FROM Details WHERE c_item_id = ?";
										$stmt3 = $con->prepare($sql3);
										$stmt3->bind_param("i", $cartitem['CI_ID']);
										$stmt3->execute();
										$result3 = $stmt3->get_result();
										$customitems = [];
										if($result3->num_rows > 0){
											while($row3 = $result3->fetch_assoc()){
												$customitems[] = $row3;
											}
										}
										$addprice = 0;
										$priceperitem = 0;
										foreach($customitems as $customitem){
											$sql4 = "SELECT * FROM customization WHERE Custom_ID = " . $customitem['customize_id'];
											$result4 = mysqli_query($con, $sql4);
											if($result4){
												while($row2 = mysqli_fetch_array($result4)){
													$customprice = $row2['Custom_Price'];
													$addprice = $addprice + $customprice;
												}
											}
										}
										// $priceperitem = $cartitem['sub_price'] + ($addprice * $cartitem['Qty']);
										echo '<td class="proqty">'.$cartitem['Qty'].'</td>';
										echo '<td><span>RM ' . number_format($cartitem['sub_price'], 2) . '</span></td>';
										echo '</tr>';
										$subtotal += $cartitem['sub_price'];
									}
										
									
									$delivery_fee = 5;
									// $sst = 0.06;
									$discount = $subtotal * ( $discountamount / 100);
									$total = $subtotal + $delivery_fee - $discount;
									echo '<input type="hidden" class="cart-id" value="' . $CT_ID . '">';
									echo '<input type="hidden" class="totalprice" value="' . $total . '">';
								?>
							</tbody>
							<tbody class="checkout-details">
								<tr>
									<th>Subtotal</th>
									<td colspan="2"><span>RM <?php echo number_format($subtotal, 2); ?></span></td>
								</tr>
								<tr>
									<th>Discount (<span><?php echo $discountamount; ?></span>%)
										<div class="showpromoword" style="font-size: 12px; font-weight: normal;"> - Promo Code: <?php echo $codename; ?></div>
									</th>
									<td colspan="2"><span>RM <?php echo number_format($discount, 2) ?></span></td>
								</tr>
								<tr>
									<th>Delivery Fee</th>
									<td colspan="2"><span>RM <?php echo number_format($delivery_fee, 2) ?></span></td>
								</tr>
								<tr>
									<th>Total</th>
									<td colspan="2"><span>RM <?php echo number_format($total, 2) ?></span></td>
								</tr>
							</tbody>
						</table>
						<!-- <div class="btn"><a class="boxed-btn odr-btn">Place Order</a></div> -->
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end check out section -->
	<div id="overlay" class="overlay"></div>
	<section id="addaddress" >
        <h4>Save new address
            <span class="modal-close" onclick="closePopup()">&times;</span>

        </h4>
        <form id="addressform" name="addForm" method="post"  onsubmit="return validateForm(event)">
			<div>
				<label for="editAdd1">Address 1:</label>
				<input type="text" id="editAdd1" name="editAdd1" placeholder="Please Enter Address 1" >
				<span id="address1-error" class="error-message"></span>
			</div>
			<div>
				<label for="editAdd2">Address 2:</label>
				<input type="text" id="editAdd2" name="editAdd2" placeholder="Please Enter Address2" >
				<span id="address2-error" class="error-message"></span>
			</div>
			<div>
				<label for="editPost">Postcode(No need fill up):</label>
				<input type="text" id="editPost" name="editPost" placeholder="75450 (Default)" readonly>
			</div>
			<div>
				<label for="editCity">City:</label>
				<select id="editCity" name="editCity" >
					<option value="" disabled selected>Select city</option>
					<option value="Ayer Keroh">Ayer Keroh</option>
					<option value="Bukit Beruang">Bukit Beruang</option>
					<option value="Bukit Katil">Bukti Katil</option>
				</select>
			</div>
			<br>
			<div>
				<label for="State">State,Country (No need fill up):</label>
				<input type="text" id="editCity-state" name="editCity-state" placeholder="Melaka, Malaysia (Default)" readonly>
			</div>
			<span class="address-text">*Cuppa Joy only provide delivery service for 75450 areas. Thank you for your understand.*</span>
			<input type="submit" class="boxed-btn" id="submitbtn" value="Save"></input>
		</form>

    </section>
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
	<!-- Stripe JavaScript library -->
	<!-- <script type="text/javascript" src="https://js.stripe.com/v2/"></script> -->

	<script>
		function closePopup(){
			document.getElementById('addaddress').style.display = 'none';
			document.getElementById('overlay').style.display = 'none';
		}
		function openAddPopup() {
			// Open the "select address" form
			document.getElementById('addaddress').style.display = 'block';
			document.getElementById('overlay').style.display = 'block';
		}
		$(document).ready(function(){
			$('.inputGroup input[type="radio"]').change(function(){
				var $input = $(this);
				
				$input.closest('.inputGroup').siblings('.inputGroup').removeClass('active');
				
				if ($input.is(':checked')) {
					$input.closest('.inputGroup').addClass('active');
				} else {
					$input.closest('.inputGroup').removeClass('active');
				}
			});
		});
		
		$(document).ready(function(){
			$('.dccard-btn').on('click', function(){
				var addressfrm = $('.billing-address-form');
				var rcname = addressfrm.find('#name').val();
				// var rcemail = addressfrm.find('#email').val();
				var rcphone = addressfrm.find('#phone').val();
				var ctid = parseInt($('.cart-id').val());
				ttl = parseFloat($('.totalprice').val());
				var addressId;
				$('input[name="address"]').each(function(){
					if ($(this).is(':checked')){
						addressId = parseInt($(this).val());
					}
				});
				console.log(addressId);
				var remark = addressfrm.find('#bill').val();
				console.log(remark);
				console.log(rcname);
				// console.log(rcemail);
				console.log(rcphone);
				console.log(ctid);
				console.log(ttl);
				var regex = /^[a-zA-Z\s]+$/;
				var nameValue = $('#name').val().trim();
				if (nameValue === '') {
					swal("Error", "Please enter a receiver name.", "error");
					return;
				}
				if (!nameValue.match(regex)) {
					swal("Error", "Name can only contain alphabets and spaces.", "error");
					return;
				}

				var phoneValue = $('#phone').val().trim();
				if (phoneValue === '') {
					swal("Error", "Please enter a receiver phone.", "error");
					return;
				}
				if (!phoneValue.match(/^\d{9,11}$/)) {
					swal("Error", "Phone number must be 9 to 11 digits.", "error");
					return;
				}

				var addresschecked = false;
				$('.inputGroup input[type="radio"]').each(function() {
					if ($(this).is(':checked')) {
						addresschecked = true;
						return false;
					}
				});
				if (!addresschecked) {
					swal("Error", "Please select an address.", "error");
					return;
				}
				
				$.ajax({
					url: 'checkout.php',
					type: 'POST',
					data: {
						rcname: rcname,
						// rcemail: rcemail,
						rcphone: rcphone,
						addrId: addressId,
						remark: remark,
						ctid: ctid,
						total: ttl,
					},
					success: function(response){
						// $id = response;
						// console.log($id);
						console.log('Database updated successfully.');
						window.location.href = 'stripepay.php';
					}
				});
			});
		});


		$(document).ready(function() {
			$('input, textarea').change(function() {
				// Retrieve updated values
				var rcname2 = $('#name').val();
				var rcphone2 = $('#phone').val();
				var remark2 = $('#bill').val();
				var ctid2 = parseInt($('.cart-id').val());
				ttl2 = parseFloat($('.totalprice').val());
				var addressId2;
				$('input[name="address"]').each(function(){
					if ($(this).is(':checked')){
						addressId2 = parseInt($(this).val());
					}
				});
				console.log("comment: " + remark2);
				$.ajax({
					url: 'checkout.php',
					method: 'POST',
					data: {
						rcname2: rcname2,
						rcphone2: rcphone2,
						remark2: remark2,
						ctid2: ctid2,
						ttl2: ttl2,
						addressId2: addressId2
					},
					success: function(response) {
						console.log(response);
					},
					error: function(xhr, status, error) {
						console.error('Error occurred while updating session:', error);
					}
				});
			});
		});
		$(document).ready(function(){
			$('#bill').keyup(function(){
				var remark2 = $('#bill').val();
				console.log("comment: " + remark2);
				$.ajax({
					url: 'checkout.php',
					type: 'POST',
					data: {
						remarkkkkk: remark2,
					},
					success: function(response) {
						console.log(response);
					},
					error: function(xhr, status, error) {
						console.error('Error occurred while updating session:', error);
					}
				})
			});
		});
		function validateFormFields() {
			var nameValue = $('#name').val().trim();
			var regex = /^[a-zA-Z\s]+$/;
			if (nameValue === '') {
				// alert("Please enter a receiver name.");
				// swal("Error", "Please enter a receiver name.", "error");
				return false; // Prevent further execution of the code
			}
			if (!nameValue.match(regex)) {
				// swal("Error", "Name can only contain alphabets and spaces.", "error");
				return false;
			}
			
			var phoneValue = $('#phone').val().trim();
			if (phoneValue === '') {
				// alert("Please enter a receiver phone.");
				// swal("Error", "Please enter a receiver phone.", "error");
				return false; // Prevent further execution of the code
			}
			if (!phoneValue.match(/^\d{9,11}$/)) {
				swal("Error", "Phone number must be 9 to 11 digits.", "error");
				return false;
			}
			var addresschecked = false;
			$('.inputGroup input[type="radio"]').each(function () {
				if ($(this).is(':checked')) {
					addresschecked = true;
					return false;
				}
			});
			if (!addresschecked) {
				// alert("Please select an address.");
				// swal("Error", "Please select an address.", "error");
				return false; // Prevent further execution of the code
			}

			return true; // All required fields are filled, proceed with PayPal checkout
		}

	
		paypal.Buttons({
			style: {
			},
			onInit(data, actions) {
				// Disable the button initially
				// actions.disable();
			},
			onClick(data, actions) {
				// Show a validation error if the checkbox isn't checked
				var status2 = validateFormFields();
				var regex = /^[a-zA-Z\s]+$/;
				if(status2)
					actions.resolve();
				// Check fields initially
				if(!status2){
					var msg = "";
					var nameValue = $('#name').val().trim();
					if (nameValue === '') {
						msg = "name";
					}
					if (!nameValue.match(regex)) {
						msg = "name";
					}
					var phoneValue = $('#phone').val().trim();
					if (phoneValue === '') {
						msg = "phone number";
					}
					if (!phoneValue.match(/^\d{9,11}$/)) {
						msg = "phone number";
					}
					var addresschecked = false;
					$('.inputGroup input[type="radio"]').each(function () {
						if ($(this).is(':checked')) {
							addresschecked = true;
						}
					});
					if (!addresschecked) {
						msg = "address";
					}
					swal({
						title: 'Data incorrect',
						text: 'Please provide us your '+msg+'.',
						icon: 'warning',
						button: 'OK'
					}).then((result) => {
						if(result){
							window.location.href = window.location.href;
						}
					});
					return actions.reject();
				}
			},
			createOrder: (data, actions) => {
				return actions.order.create({
					"intent": "CAPTURE",
					"purchase_units": [{
						"description": "Your order description",
						"amount": {
							"currency_code": "<?php echo $currency; ?>",
							"value": "<?php echo $total; ?>",
						}
					}]
				});
			},
			onApprove: (data, actions) => {
				return actions.order.capture().then(function(orderData) {
					setProcessing(true);

					var postData = {paypal_order_check: 1, order_id: orderData.id};
					fetch('paypal_checkout_validate.php', {
						method: 'POST',
						headers: {'Accept': 'application/json'},
						body: encodeFormData(postData)
					})
					.then((response) => response.json())
					.then((result) => {
						if(result.status == 1){
							window.location.href = "submit.php?checkout_ref_id="+result.ref_id;
						}else{
							const messageContainer = document.querySelector("#paymentResponse");
							messageContainer.classList.remove("hidden");
							messageContainer.textContent = result.msg;
							
							setTimeout(function () {
								
								messageContainer.classList.add("hidden");
								messageContainer.textContent = "";
							}, 5000);
						}
						setProcessing(false);
					})
					console.error("failed to load the PayPal JS SDK script", error);
				});
			}
		}).render('#paypal-button-container');
	
		const encodeFormData = (data) => {
			var form_data = new FormData();

			for ( var key in data ) {
				form_data.append(key, data[key]);
			}
			return form_data;
			
		}

		// Show a loader on payment form processing
		const setProcessing = (isProcessing) => {
			if (isProcessing) {
				document.querySelector(".overlay").classList.remove("hidden");
			} else {
				document.querySelector(".overlay").classList.add("hidden");
			}
		}
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
		$(document).ready(function(){
			var applypromo = parseFloat(<?php echo $discountamount; ?>);
			if(applypromo == 0){
				$('.showpromoword').hide();
			}
		});
		$(document).ready(function() {
			function validateAddress1() {
				const address1 = $('#editAdd1').val().trim();
				const address1Error = $('#address1-error');

				if (address1 === "") {
					address1Error.text("Address 1 cannot be empty.").css("color", "red");
					return false;
				} else {
					address1Error.text("");
					return true;
				}
			}

			function validateAddress2() {
				const address2 = $('#editAdd2').val().trim();
				const address2Error = $('#address2-error');

				if (address2 === "") {
					address2Error.text("Address 2 cannot be empty.").css("color", "red");
					return false;
				} else {
					address2Error.text("");
					return true;
				}
			}

			function validateCity() {
				const city = $('#editCity').val();
				const cityError = $('#city-error');

				if (city === null) {
					cityError.text("Please select a city.").css("color", "red");
					return false;
				} else {
					cityError.text("");
					return true;
				}
			}

			function validateForm(event) {
				const isAddress1Valid = validateAddress1();
				const isAddress2Valid = validateAddress2();
				const isCityValid = validateCity();

				if (!isAddress1Valid) {
					event.preventDefault();
					swal({
						icon: 'error',
						title: 'Invalid Address 1',
						text: 'Please ensure that address 1 is valid.'
					});
					return false;
				}
				if (!isAddress2Valid) {
					event.preventDefault();
					swal({
						icon: 'error',
						title: 'Invalid Address 2',
						text: 'Please ensure that address 2 is valid.'
					});
					return false;
				}
				if (!isCityValid) {
					event.preventDefault();
					swal({
						icon: 'error',
						title: 'No city selected',
						text: 'Please ensure that a city is selected.'
					});
					return false;
				}
				return true;
			}

			$('#addressform').on('submit', validateForm);
		});
	</script>

</body>
</html>
