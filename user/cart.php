<?php
	include("db_connection.php");
	session_start();

	$custid = $_SESSION['customer_id'];

	if (!isset($custid)){
		header("location:sign-in.php");
		exit;
	}
	$todaydate = date('m-d');
	$currentDate = date("Y-m-d");
	$currentTime = date("H:i:s");
	date_default_timezone_set('UTC');
	$dateTimeUTC = new DateTime($currentDate . ' ' . $currentTime);
	$dateTimeUTC->setTimezone(new DateTimeZone('Asia/Singapore'));
	$timeLocal = $dateTimeUTC->format('H:i:s');

	$isAfterTenPM = (float)$dateTimeUTC->format('G') + ((float)$dateTimeUTC->format('i') / 60);

	$sql = "SELECT * FROM `customer` WHERE C_ID = '$custid'";
	$gotResult2 = mysqli_query($con, $sql);

	if($gotResult2){
		if(mysqli_num_rows($gotResult2)>0){
			while($row = mysqli_fetch_array($gotResult2)){

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

	if (isset($_POST['cartitemid4'])) {
		// Sanitize the input (assuming integers for CI_ID and P_ID)
		$ciid4 = intval($_POST['cartitemid4']);
	
		$sql21 = "SELECT * FROM details WHERE c_item_id = ?";
		$stmt21 = $con->prepare($sql21); // Pass the SQL query string as an argument to prepare()
		$stmt21->bind_param("i", $ciid4);
		$stmt21->execute();
		if ($stmt21->error) {
			error_log($stmt21->error); // Log the error message if there is one
		}
		$result21 = $stmt21->get_result();
		if ($result21->num_rows > 0) {
			while ($row21 = $result21->fetch_assoc()) {
				$sql22 = "DELETE FROM details WHERE D_ID = ?";
				$stmt22 = $con->prepare($sql22); // Pass the SQL query string as an argument to prepare()
				$stmt22->bind_param("i", $row21['D_ID']);
				$stmt22->execute();
				if ($stmt22->error) {
					error_log($stmt22->error); // Log the error message if there is one
				}
			}
		}
		
		// Perform the removal action (e.g., delete the product from the database)
		// Your removal logic here
		$stmt10 = $con->prepare("DELETE FROM cart_item WHERE CI_ID = ?");
		$stmt10->bind_param("i", $ciid4);
		$result10 = $stmt10->execute();
	
		if ($result10) {
			echo 'Product removed successfully';
		}
	}
	
    // Assuming the product ID is stored in a column named 'P_ID'
    $CartNo = "SELECT * FROM cart WHERE C_ID = ? AND C_Status = 'No-Paid'";
    $stmt = $con->prepare($CartNo);
    $stmt->bind_param("i", $custid);
    $stmt->execute();
    $result = $stmt->get_result();
	// Fetch the CT_ID from the result set
    $row = $result->fetch_assoc();
    $CT_ID = $row['CT_ID'];
	$promo_id = $row['Promo_ID'];
	$discountamount = 0;
	$codename = null;
	if(!is_null($promo_id)){
		$sql23 = "SELECT * FROM promo WHERE Promo_ID = ? AND Start_From <= ? AND End_By >= ?";
		$stmt23 = $con->prepare($sql23);
		$stmt23 -> bind_param("iss", $promo_id, $currentDate, $currentDate);
		$stmt23 -> execute();
		$result23 = $stmt23->get_result();
		if($row23 = $result23->fetch_assoc()){
			$codename = $row23['Promo_Name'];
			$discountamount = $row23['Discount'];
		}else{
			$sql24 = "UPDATE cart SET Promo_ID = NULL WHERE CT_ID = ? ";
			$stmt24 = $con->prepare($sql24);
			$stmt24 -> bind_param("i", $CT_ID);
			$result24 = $stmt24->execute();
		}
	}

	$checkstatus = "SELECT * FROM Details d INNER JOIN Cart_Item ci ON d.c_item_id = ci.CI_ID INNER JOIN customization custom ON custom.Custom_ID = d.customize_id WHERE ci.CT_ID = ?";
	$stmtstatus = $con->prepare($checkstatus);
	$stmtstatus->bind_param("i", $CT_ID);
	$stmtstatus->execute();
	$resultstatus = $stmtstatus->get_result();
	if($resultstatus->num_rows > 0){
		while($rowstatus = $resultstatus->fetch_assoc()){
			if($rowstatus['available_status'] == "Unavailable"){
				$deletedetail = "DELETE FROM Details WHERE D_ID = ?";
				$stmtdelete = $con->prepare($deletedetail);
				$stmtdelete -> bind_param("i", $rowstatus['D_ID']);
				$stmtdelete->execute();
			}
		}
	}

	$check1 = "SELECT * FROM Details d INNER JOIN Cart_Item ci ON d.c_item_id = ci.CI_ID INNER JOIN customization custom ON custom.Custom_ID = d.customize_id WHERE ci.CT_ID = ?";
	$stmtc1 = $con->prepare($check1);
	$stmtc1 ->bind_param("i", $CT_ID);
	$stmtc1->execute();
	$resultc1 = $stmtc1->get_result();
	if($resultc1->num_rows > 0){
		while($rowc1 = $resultc1->fetch_assoc()){
			$checkc2 = "SELECT * FROM opt WHERE P_ID = ? AND CC_ID = ?";
			$stmtc2 = $con->prepare($checkc2);
			$stmtc2 ->bind_param("ii", $rowc1['P_ID'], $rowc1['CC_ID']);
			$stmtc2->execute();
			$resultc2 = $stmtc2->get_result();
			$countc2 = $resultc2->num_rows;
			if($countc2 == 0){
				$deletedetail = "DELETE FROM Details WHERE D_ID = ?";
				$stmtdelete = $con->prepare($deletedetail);
				$stmtdelete -> bind_param("i", $rowc1['D_ID']);
				$stmtdelete->execute();
			}
		}
	}

	$notavailablewor = "";
	
    $sql1 = "SELECT * FROM Cart_Item WHERE CT_ID = ?";
    $stmt1 = $con->prepare($sql1);
    $stmt1->bind_param("i", $CT_ID);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $cartitems = [];
    if($result1->num_rows > 0){
        while($row1 = $result1->fetch_assoc()){
            $cartitems[] = $row1;
        }
    }

	if(isset($_POST['newQuantity'])){
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$CI_ID = $_POST['CI_ID'];
			$newQuantity = $_POST['newQuantity'];
			$price = $_POST['price'];
			$subprice = $price * $newQuantity;
		
			// Update quantity in the database
			$stmt5 = $con->prepare("UPDATE cart_item SET Qty = ?, sub_price = ? WHERE CI_ID = ?");
			$stmt5->bind_param("idi", $newQuantity, $subprice, $CI_ID);
			$stmt5->execute();
			error_log($stmt5);
		}
	}

	if(isset($_POST['inputValue'], $_POST['pid2'], $_POST['isChecked'], $_POST['cartitemid2'])) {
		if (!isset($_POST['pid2'])) {
			header('Content-Type: application/json');
			echo json_encode(array('error' => 'Product ID not provided'));
			exit; // Stop execution after sending JSON response
		}
		$customID = $_POST['inputValue'];
		$isChecked = $_POST['isChecked'];
		$ciid2 = $_POST['cartitemid2'];
		$pid2 = $_POST['pid2'];

		if ($isChecked) {
			// Insert new detail record
			$query18 = "INSERT INTO details (customize_id, c_item_id) VALUES (?,?)";
			$stmt18 = $con->prepare($query18);
			$stmt18->bind_param("ii", $customID, $ciid2);
			$result18 = $stmt18->execute();
			error_log($stmt18);
			if (!$result18) {
				echo 'Error inserting new detail record: ' . $stmt18->error;
			}
		} else {
			// Delete existing detail record
			$stmt17 = $con->prepare("DELETE FROM details WHERE customize_id = ? AND c_item_id = ?");
			$stmt17->bind_param("ii", $customID, $ciid2);
			$result17 = $stmt17->execute();
			error_log($stmt17);
			if (!$result17) {
				echo 'Error deleting detail record: ' . $stmt17->error;
			}
		}

		$error18 = $stmt18->error;
		$error17 = $stmt17->error;
		error_log($error18);
		error_log($error17);
		// Respond to the AJAX request with success message or appropriate response
		echo 'Database updated successfully.';
	}

	if(isset($_POST['inputValue3'], $_POST['isChecked3'], $_POST['cartitemid3'], $_POST['ccid3'])){
		$customID = $_POST['inputValue3'];
		$isChecked = $_POST['isChecked3'];
		$ciid2 = $_POST['cartitemid3'];
		$ccid = $_POST['ccid3'];

		// First, delete the old existing record
		$sql19 = "SELECT DISTINCT d.*, cc.CC_ID, cc.CC_Group, cc.compulsory_status, custom.Custom_Name 
				FROM details AS d 
				INNER JOIN customization AS custom ON d.customize_id = custom.Custom_ID 
				INNER JOIN customize_category as cc ON custom.CC_ID = cc.CC_ID 
				WHERE cc.compulsory_status = 'yes' AND d.c_item_id = ? AND cc.CC_ID = ?
				GROUP BY d.D_ID";
		$stmt19 = $con->prepare($sql19);
		$stmt19->bind_param("ii", $ciid2, $ccid);
		$stmt19->execute();
		$result19 = $stmt19->get_result();

		if($result19->num_rows == 1) {
			$row19 = $result19->fetch_assoc();
			$d_id = $row19['D_ID'];

			$sql20 = "DELETE FROM details WHERE D_ID = ? AND c_item_id = ?";
			$stmt20 = $con->prepare($sql20);
			$stmt20->bind_param("ii", $d_id, $ciid2);
			$result20 = $stmt20->execute();
			
			if (!$result20) {
				echo 'Error deleting detail record: ' . $stmt20->error;
			}
		}

		// Now insert the new record
		if($isChecked) {
			$query21 = "INSERT INTO details (customize_id, c_item_id) VALUES (?,?)";
			$stmt21 = $con->prepare($query21);
			$stmt21->bind_param("ii", $customID, $ciid2);
			$result21 = $stmt21->execute();

			if (!$result21) {
				echo 'Error inserting new detail record: ' . $stmt21->error;
			}
		}
	}

	if(isset($_POST['promocode'])){
		$pcode = $_POST['promocode'];
		$sql25 = "SELECT * FROM promo WHERE Promo_Name = ? AND Start_From <= ? AND End_By >= ?";
		$stmt25 = $con->prepare($sql25);
		$stmt25 -> bind_param("sss", $pcode, $currentDate, $currentDate);
		$stmt25->execute();
    	$result25 = $stmt25->get_result();
		if($row25 = $result25->fetch_assoc()){
			$sql28 = "SELECT * FROM promo_history WHERE Promo_ID =? AND Cus_ID = ?";
			$stmt28 = $con->prepare($sql28);
			$stmt28 -> bind_param("ii", $row25['Promo_ID'], $custid);
			$stmt28-> execute();
			$result28 = $stmt28->get_result();
			if($result28->num_rows == 0){
				$sql26 = "UPDATE cart SET Promo_ID = ? WHERE CT_ID = ?";
				$stmt26 = $con->prepare($sql26);
				$stmt26->bind_param("ii", $row25['Promo_ID'], $CT_ID);
				$result26 = $stmt26->execute();
				if($result26){
					echo "
						<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
						<script>
							document.addEventListener('DOMContentLoaded', function() {
								swal({
									title: 'Apply Success',
									text: 'Promo code applied successfully.',
									icon: 'success',
									button: 'OK'
								}).then((result) => {
									if(result)
										window.location.href = window.location.href;
								});
								
							});
						</script>
					";
				} else {
					echo "
						<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
						<script>
							document.addEventListener('DOMContentLoaded', function() {
								swal({
									title: 'Apply Failed',
									text: 'Failed to apply promo code.',
									icon: 'warning',
									button: 'OK'
								}).then((result) => {
									if(result)
										window.location.href = window.location.href;
								});
							});
						</script>
					";
				}
			}else{
				echo "
					<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
					<script>
						document.addEventListener('DOMContentLoaded', function() {
							swal({
								title: 'Apply Failed',
								text: 'The promo code already been used.',
								icon: 'warning',
								button: 'OK'
							}).then((result) => {
								if(result)
									window.location.href = window.location.href;
							});
						});
					</script>
				";
			}
		} else {
			echo "
				<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
				<script>
					document.addEventListener('DOMContentLoaded', function() {
						swal({
							title: 'Apply Failed',
							text: 'Invalid or expired promo code.',
							icon: 'warning',
							button: 'OK'
						}).then((result) => {
							if(result)
								window.location.href = window.location.href;
						});
					});
				</script>
			";
		}
	}
	if(isset($_POST['disapply'])){
		if(!is_null($promo_id)){
			// $codeqty = $codeqty + 1;
			$sql28 = "UPDATE cart SET Promo_ID = NULL WHERE CT_ID = ? ";
			$stmt28 = $con->prepare($sql28);
			$stmt28 -> bind_param("i", $CT_ID);
			$result28 = $stmt28->execute();

			if($result28){
				echo "Update promo code successfully";
			}
		} else {
			echo "Update failed";
		}
	}
	function hasAvailableCustomization($option, $con) {
		$query27 = "SELECT DISTINCT * FROM customization WHERE CC_ID = ? AND available_status = 'available' ORDER BY Custom_ID ASC";
		$stmt27 = $con->prepare($query27);
		$stmt27->bind_param("i", $option['CC_ID']);
		$stmt27->execute();
		$result27 = $stmt27->get_result();
		$available = $result27->num_rows > 0;
		$stmt27->close();
		return $available;
	}

	if(isset($_POST['sub__price'])){
		$updateprice = "UPDATE cart_item SET sub_price = ? WHERE CI_ID = ?";
		$updatestmt = $con->prepare($updateprice);
		$updatestmt -> bind_param("di", $_POST['sub__price'], $_POST['cart_item_id']);
		$updatestmt->execute();
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
	<title>Cart</title>

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
	<!-- <link rel="stylesheet" href="assets\css\single-prod.css"> -->

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
										<a class="shopping-cart current-list-item" href="cart.php"><i class="fas fa-shopping-cart"></i></a>
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
						<h1>Cart</h1>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end breadcrumb section -->

	<!-- cart -->
	<div class="cart-section mt-150 mb-150">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 col-md-12">
					<div class="cart-table-wrap">
						<ul class="cart-table">
							<span class="cartlimitation">Your cart can only have total 12 items.</span>
							<hr />
							<?php
								$subtotal = 0;
								foreach($cartitems as $cartitem){
									$sql2 = "SELECT * FROM product WHERE P_ID =" . $cartitem['P_ID'];
									$result2 = mysqli_query($con, $sql2);
									if($result2){
										while($row = mysqli_fetch_array($result2)){
											$pstatus1 = $row['P_Status'];
											$pname = $row['P_Name'];
											$pimage = $row['P_Photo'];
											$pprice = $row['P_Price'];
											$showstatus = "";
											$background = "";
											if($pstatus1 =="no"){
												$showstatus = "unavailable";
												$background = '<div class="not-available-overlay"></div>';
											}
											echo '<li class="carttable-row">';
											echo '<div class="cartitem-row '.$showstatus.'">';
											echo $background;
											if($pstatus1 =="no") {
												echo '<div class="not-available-text">Not Available</div>'; // "Not Available" text
											}
											echo '<input type="hidden" class="cartitem-id" value="' . $cartitem['CI_ID'] . '">';
											echo '<input type="hidden" class="product-id" value="' . $cartitem['P_ID'] . '">';
											echo '<input type="hidden" class="product-price" value="' . $row['P_Price'] . '">';
											echo '<div class="adddelete">';
											echo '<div class="block">';
											echo '<div class="action">';
											echo '<div><span class="minus bg-dark" id="minus">-</span></div>';
											echo '<div><span class="plus bg-dark" id="plus">+</span></div>';
											echo '</div>';
											echo '<div id="number">x' . $cartitem['Qty'] . '</div>';
											
											echo '</div>';
											echo '</div>';
											echo '<div class="pphoto"><img src="../image/product/' . $pimage .'"></div>';
											echo '<div class="pdetail">';
											echo '<h5>' . $pname . '</h5>';
											echo '<div class="customgroup">';

											$sql3 = "SELECT * FROM Details WHERE c_item_id = ? ORDER BY customize_id ASC";
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
												$sql4 = "SELECT custom.*, cc.CC_Group, cc.compulsory_status FROM customization AS custom 
														INNER JOIN customize_category AS cc 
														ON custom.CC_ID = cc.CC_ID 
														WHERE custom.Custom_ID = " . $customitem['customize_id'];
												$result4 = mysqli_query($con, $sql4);
												if($result4){
													while($row2 = mysqli_fetch_array($result4)){
														$customname = $row2['Custom_Name'];
														$customprice = $row2['Custom_Price'];
														$typecc = "";
														if($row2['compulsory_status'] == "yes"){
															$typecc = "ccrequire" . $row2['CC_ID'];
														}
														echo '<span class="customitem '.$typecc.'"> - ' . $customname . '</span>';
														// echo '<br/>';
														$addprice = $addprice + $customprice;
													}
												}
											}
											echo '</div>';
											$priceperitem = ($pprice * $cartitem['Qty']) + ($addprice * $cartitem['Qty']);//
											// echo '<span>add egg</span>';
											echo '</div>';
											echo '<input type="hidden" class="custom-price" value="' . $addprice . '">';
											if($pstatus1 == "no") {
												$priceperitem = 0.0;
											}
											echo '<div class="priceperitem">RM ' . number_format($priceperitem, 2) . '</div>';
											echo '</div>';
											echo '<hr />';
											echo '</li>';
											
											$subtotal += $priceperitem;
										}
									}
								}
								
								$delivery_fee = 5;
								// $sst = 0.06;
								$discount = $subtotal * ($discountamount / 100);
								$total = $subtotal + $delivery_fee - $discount;
							?>
						</ul>
						<div class="shownonproduct">
							You have no any product in the cart. You may go to add some products first.
						</div>
					</div>
				</div>

				<div class="col-lg-4">
					<div class="coupon-section">
						<h3>Apply Promo Code</h3>
						<div class="coupon-form-wrap">
							<form action="" method="post" onsubmit="return checkpromo()">
								<p><input type="text" name="promocode" id="promocode" placeholder="Promo Code"></p>
								<p><button type="submit" name="submitbtn" class="showbtn">Apply</button></p>
							</form>
						</div>
					</div>
					<div class="total-section">
						<table class="total-table">
							<thead class="total-table-head">
								<tr class="table-total-row">
									<th>Total</th>
									<th>Price</th>
								</tr>
							</thead>
							<tbody>
								<tr class="total-data">
									<td><strong>Subtotal: </strong></td>
									<td><span id="subtotal">RM <?php echo number_format($subtotal, 2) ?></span></td>
								</tr>
								<tr class="total-data">
									<td>
										<strong>Discount (<span class="discountamount"><?php echo $discountamount; ?></span>%): </strong>
										<div class="showpromoword" style="font-size: 12px;"> - Promo Code: <?php echo $codename; ?></div>
									</td>
									<td><span id="discount">RM <?php echo number_format($discount, 2) ?></span></td>
								</tr>
								<tr class="total-data">
									<td><strong>Delivery Fee: </strong></td>
									<td><span id="deliveryFee">RM <?php echo number_format($delivery_fee, 2) ?></span></td>
								</tr>
								<tr class="total-data">
									<td><strong>Total: </strong></td>
									<td><span id="total">RM <?php echo number_format($total, 2) ?></span></td>
								</tr>
							</tbody>
						</table>
						<div class="cart-buttons">
							<button class="showbtn" onclick="disapplycoupon()">Remove Promo Code</button>
							<button class="showbtn" onclick="showPopup()">Edit Cart</button>
							<button class="showbtn" onclick="gotonext()">Check Out</button>
						</div>
						<div style="width: 100%; text-align: center;"><span class="lastcall" style="color: red;">Last order at 9.30pm.</span></div>
					</div>

					
				</div>
			</div>
		</div>
	</div>
	<!-- end cart -->

	<section class="editcart-section">
		<div class="overlay" id="editcart-section">
			<div class="detail-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h4>Edit Cart</h4>
						<?php
						foreach($cartitems as $cartitem){
							$sql5 = "SELECT * FROM product WHERE P_ID = " . $cartitem['P_ID'];
								$result5 = mysqli_query($con, $sql5);
								if($result5){
									while($row = mysqli_fetch_array($result5)){
										$pstatus2 = $row['P_Status'];
										if($pstatus2 == "no") {
											$showstatus = "unavailable";
											$btnstatus = "disabled";
											$notavailablewor = "*Please remove the product which is not available.";
										}
									}
								}
						}
						?>
						<span id="notAvailableSpan" ><?php echo $notavailablewor; ?></span>

						<button type="button" class="close-modal" onclick="hidePopup()" aria-label="Close">&times;</button>
					</div>
					<div class="modal-body">
						<?php
							$num = 1;
							$radio = 1;
							foreach($cartitems as $cartitem){
								$sql5 = "SELECT * FROM product WHERE P_ID = " . $cartitem['P_ID'];
								$result5 = mysqli_query($con, $sql5);
								if($result5){
									while($row = mysqli_fetch_array($result5)){
										$pname1 = $row['P_Name'];
										$pimage1 = $row['P_Photo'];
										$pstatus = $row['P_Status'];
										$showstatus = "";
										$btnstatus = "";
										if($pstatus == "no") {
											$showstatus = "unavailable";
											$btnstatus = "disabled";
											$notavailablewor = "*Please remove the product which is not available.";
										}
										$customizestatus = "";
										if($row['Customize_Status'] == "no"){
											$customizestatus = "dontshow";
										}
										echo '<div class="oneproduct '.$showstatus.'">';
										if($pstatus1 =="no") {
											echo '<div class="not-available-text">Not Available</div>'; // "Not Available" text
										}
										echo '<div class="up">';
										echo '<div class="numbering">'.$num.'</div>';
										echo '<div class="prophoto"><img src="../image/product/'.$pimage1.'" alt="'.$pimage1.'"></div>';
										echo '<div class="prodetail">';
										echo '<input type="hidden" class="cartitem-id" value="' . $cartitem['CI_ID'] . '">';
										echo '<input type="hidden" class="product-id" value="' . $cartitem['P_ID'] . '">';
										echo '<input type="hidden" class="product-price" value="' . $row['P_Price'] . '">';
										echo '<div><h6>' . $pname1 . '</h6></div>';
										echo '<div class="cgroup">';

										$sql8 = "SELECT * FROM Details WHERE c_item_id = ? ORDER BY customize_id ASC";
										$stmt8 = $con->prepare($sql8);
										$stmt8->bind_param("i", $cartitem['CI_ID']);
										$stmt8->execute();
										$result8 = $stmt8->get_result();
										$customitems = [];
										if($result8->num_rows > 0){
											while($row8 = $result8->fetch_assoc()){
												$customitems[] = $row8;
											}
										}

										foreach($customitems as $customitem){
											$sql9 = "SELECT custom.*, cc.CC_Group, cc.compulsory_status FROM customization AS custom 
													INNER JOIN customize_category AS cc 
													ON custom.CC_ID = cc.CC_ID 
													WHERE custom.Custom_ID = " . $customitem['customize_id'];
											$result9 = mysqli_query($con, $sql9);
											if($result9){
												while($row9 = mysqli_fetch_array($result9)){
													$customname = $row9['Custom_Name'];
													$customprice = $row9['Custom_Price'];
													$typecc = "";
													if($row9['compulsory_status'] === "yes"){
														$typecc = "ccrequire" . $row9['CC_ID'];
													}
													echo '<span class="cdetail '.$typecc.'"> - ' . $customname . '</span>';
												}
											}
										}
										echo '</div>';//end of customization
										echo '</div>';//end of prodetail
										echo '<div class="btngroup">';
										echo '<button onclick="showDivision('.$num.')" class="btn boxed-btn '.$customizestatus.'" '.$btnstatus.'>Edit</button>';
										echo '<button class="btn remove">Remove</button>';
										echo '</div>';
										echo '</div>';//end of .up
										echo '<div class="bottom" id="bottom'.$num.'">';
										echo '<ul class="options-list">';
										echo '<div class="row">';

										$sql13 = "SELECT Customize_Status FROM product WHERE P_ID = ?";
										$stmt13 = $con->prepare($sql13);
										$stmt13->bind_param("i", $cartitem['P_ID']);
										$stmt13->execute();
										$result13 = $stmt13->get_result();
										$row13 = $result13->fetch_assoc();
										$customstatus = $row13['Customize_Status'];

										if($customstatus == 'yes'){
											$query14 = "SELECT opt.*, cc.CC_Group, cc.compulsory_status FROM opt AS opt 
														INNER JOIN customize_category AS cc 
														ON opt.CC_ID = cc.CC_ID 
														WHERE opt.P_ID = ? 
														ORDER BY `cc`.`compulsory_status` DESC";
											$stmt14 = $con->prepare($query14);
											$stmt14->bind_param("i", $cartitem["P_ID"]);
											$stmt14->execute();
											$options = [];
											$result14 = $stmt14->get_result();
											if($result14->num_rows > 0){
												while ($row14 = $result14 -> fetch_assoc()){
													$options[] = $row14;
												}
											}
											
											$options = array_filter($options, function($option) use ($con) {
												return hasAvailableCustomization($option, $con);
											});
											foreach($options as $opt){
												$typeinput = "";
												$typename = "";
												$requirement = "";
												$inputgp = "";
												$custommessage = "";
												if($opt['compulsory_status'] == "no"){
													$typeinput = "checkbox";
													$requirement = " ";
													$inputgp = "inputcb";
													$custommessage = "<div style='color: red; font-style: Italic;'>*Optional</div>";
												}
												else if($opt['compulsory_status'] == "yes"){
													$typeinput = "radio";
													$requirement = "required";
													$inputgp = "inputrd";
													$custommessage = "<div style='color: red; font-style: Italic;'>*Pick one option.</div>";
												}
												$typename = $typeinput.$radio;

												echo '<div class="col-md-4">';
												echo '<form class="formcustom">';
												echo '<input type="hidden" class="ccgroup" value="' . $opt['CC_ID'] . '">';
												echo '<h5>' . $opt['CC_Group'] . '</h5>';
												echo $custommessage;
												
												$query15 = "SELECT DISTINCT * FROM customization WHERE CC_ID = ? AND available_status = 'available' ORDER BY Custom_ID ASC";
												$stmt15 = $con->prepare($query15);
												$stmt15->bind_param("i", $opt['CC_ID']);
												$stmt15->execute();
												$result15 = $stmt15->get_result();
												$customs = [];
												if($result15->num_rows > 0){
													while ($row15 = $result15 -> fetch_assoc()){
														$customs[] = $row15;
													}
												}

												foreach($customs as $custom){
													$customdetail = ""; // Initialize customdetail
													foreach($customitems as $customitem){
														if($custom['Custom_ID'] == $customitem['customize_id']) {
															$customdetail = " active"; // Update customdetail if the input is checked
															break; // Exit the loop once a match is found
														}
													}
													echo '<div class="inputGroup'.$customdetail.' '.$inputgp.'">';
													echo '<input type="'.$typeinput.'" id="input'.$radio.'" name="'.$typename.'" value="'.$custom['Custom_ID'].'" '.$requirement;
													if ($customdetail == " active") {
														echo ' checked';
													}
													echo '>';
													echo '<label for="input'.$radio.'" id="label"">'.$custom['Custom_Name'].'</label><span class="price"> + RM '.number_format($custom['Custom_Price'], 2).'</span>';
													echo '</div>';

													$radio++;
												}
												echo '</form>';
												echo '</div>';//end of col-md-4
											}
										} else {
											echo '<div><p>This product has <b>no custom</b> options.</p></div>';
										}
										echo '</div>'; //end of row
										echo '</ul>'; //end of ul
										echo '</div>';//end of bottom
										echo '</div>';//end of .oneproduct
										$num++;
									}
								}
							}
						?>
					</div>
				</div>
			</div>
		</div>

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
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> -->
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
		$(document).ready(function() {
			$('.action').on('click', '.plus, .minus', function() {
				var $button = $(this);
				var $cartitemid = $button.closest('.cartitem-row').find('.cartitem-id').val();
				var $pp = parseFloat($button.closest('.cartitem-row').find('.product-price').val());
				var $addp = parseFloat($button.closest('.cartitem-row').find('.custom-price').val());
				var $quantityElement = $button.closest('.action').siblings('#number');
				var $priceElement = $button.closest('.cartitem-row').find('.priceperitem');
				var $priceproduct = parseFloat($priceElement.text().replace('RM ', ''));
				var $quantityPerItem = parseInt($quantityElement.text().replace('x', ''));
				var currentQuantity = $quantityPerItem;
				var newQuantity = currentQuantity + ($button.hasClass('plus') ? 1 : -1);
				console.log("quantity: " + newQuantity);
				// Ensure new quantity is at least 1
				if (newQuantity < 1 || newQuantity > 12) {
					// Alert message
					if (newQuantity < 1)
						// alert("The quantity of product should not be less than 1. If you want to delete the product, you may click the EDIT CART button to remove the product from your cart.");
						swal({
							title: 'Minimal Quantity',
							text: 'The quantity of product should not be less than 1. If you want to delete the product, you may click the EDIT CART button to remove the product from your cart.',
							icon: 'warning',
							button: 'OK'
						});
					if (newQuantity > 12)
						// alert("Your product quantity should not be more than 12. You may try to place another order to buy more. Thank you");
						swal({
							title: 'Limit Exceeded',
							text: 'Your product quantity should not be more than 12. You may try to place another order to buy more. Thank you',
							icon: 'warning',
							button: 'OK'
						});
					return; // Do nothing if quantity is already 1 and minus button is clicked
				}

				// Check if total quantity exceeds 12
				var totalQuantity = calculateTotalQuantity();
				if ($button.hasClass('plus') && totalQuantity >= 12) {
					// Alert the user
					// alert("Your total product quantity should not exceed 12. Please adjust the quantities accordingly.");
					swal({
							title: 'Limit Exceeded',
							text: 'Your total product quantity should not exceed 12. Please adjust the quantities accordingly.',
							icon: 'warning',
							button: 'OK'
						});
					return;
				}

				// Make AJAX request to update quantity
				$.ajax({
					url: 'cart.php', // PHP script to handle the update
					type: 'POST',
					data: {
						CI_ID: $cartitemid, // Pass the cart item ID
						price: $priceproduct,
						newQuantity: newQuantity
					},
					success: function(response) {
						// Update quantity and subtotal on the page based on server's response
						$quantityElement.text('x' + newQuantity); // Update text content of the element

						// Calculate and update new subtotal
						// var newSubtotal = ($pp + $addp) * newQuantity; // Calculate new subtotal
						var newSubtotal = ($priceproduct / currentQuantity) * newQuantity;
						$priceElement.text('RM ' + newSubtotal.toFixed(2)); // Update subtotal on the page

						// Calculate and update total
						updateTotal();
						$.ajax({
							url: 'cart.php',
							type: 'POST',
							data: {
								sub__price: newSubtotal,
								cart_item_id: $cartitemid
							}
						});
					}
				});
			});

			// Call updateTotal initially to set the initial values
			updateTotal();
		});

		function calculateTotalQuantity() {
			var totalQuantity = 0;
			$('.cartitem-row').each(function() {
				var quantity = parseInt($(this).find('#number').text().replace('x', ''));
				totalQuantity += quantity;
			});
			return totalQuantity;
		}
		
		function showPopup() {
			var carthasProduct = false;
			$('.carttable-row').each(function() {
				carthasProduct = true;
			});
			if(!carthasProduct){
				// alert("Please add some products first.");
				swal({
					title: 'Empty Cart',
					text: 'Please add some products first.',
					icon: 'error',
					button: 'OK'
				});
			}else{
				document.getElementById("editcart-section").classList.add("show");
			}
		}

		function hidePopup() {
			document.getElementById("editcart-section").classList.remove("show");
		}

		$(document).ready(function(){
			var productnumber = false;
			$('.carttable-row').each(function() {
				productnumber = true;
			});
			if(!productnumber){
				$('.shownonproduct').css('display', 'block');
				$('.cartlimitation').hide();
			}
		});

		$(document).ready(function(){
			var applypromo = parseFloat(<?php echo $discountamount; ?>);
			if(applypromo == 0){
				$('.showpromoword').hide();
			}
		});
		
		function checkproduct(){
			var productnumber = false;
			$('.carttable-row').each(function() {
				productnumber = true;
			});
			if(!productnumber){
				$('.shownonproduct').css('display', 'block');
				$('.cartlimitation').hide();
				hidePopup();
			}
		}

		console.log('Subtotal:', <?php echo $subtotal; ?>);
		console.log('Delivery Fee:', deliveryFee);
		// console.log('Tax charge:', sst);
		console.log('Total:', total);

		//edit button
		function showDivision(number){
			var bottom = document.getElementById('bottom'+number);
			if (bottom) { // Check if bottom is not null
				if (bottom.style.display === "none" || bottom.style.display === "") {
					bottom.style.display = "block"; // Show the bottom content
				} else {
					bottom.style.display = "none"; // Hide the bottom content
				}
			} else {
				console.error("Element with ID " + number + " not found.");
			}
		}

		//edit cart customization class active
		//customdetail inputs
		$(document).ready(function(){
			$('.inputGroup input[type="checkbox"]').change(function(){
				if ($(this).is(':checked')) {
					$(this).closest('.inputGroup').addClass('active');
				} else {
					$(this).closest('.inputGroup').removeClass('active');
				}
				var inputValue = parseInt($(this).val()); // Get the value of the input
				var isChecked = parseInt($(this).is(':checked') ? 1 : 0); // Check if the input is checked
				var cartitemid2 = parseInt($(this).closest('.oneproduct').find('.cartitem-id').val());
				var pid2 = parseInt($(this).closest('.oneproduct').find('.product-id').val());
				var customname = $(this).closest('.inputGroup').find('label').text(); // Get the custom name
				var customprice = parseFloat($(this).closest('.inputGroup').find('.price').html().replace(' + RM ', '')); // Get the custom price
				var productrow = $(this).closest('.oneproduct');
				var customDetailSection = productrow.find('.cgroup');

				$.ajax({
					url: 'cart.php',
					type: 'POST',
					data: { 
						inputValue: inputValue, 
						isChecked: isChecked, 
						cartitemid2: cartitemid2,
						pid2: pid2,
						
					},
					success: function(response) {
						// Handle success response
						console.log('Database updated successfully.');

						if (isChecked) {
							// Append the new custom detail
							customDetailSection.append('<span class="cdetail"> - ' + customname + '</span>');
						} else {
							// Remove the existing custom detail with the same name
							customDetailSection.find('.cdetail').filter(function() {
								return $(this).text().includes(customname); // Check if the custom detail contains the custom name
							}).remove();
						}


						$('.cartitem-row').each(function(){
							var CIID = parseInt($(this).find('.cartitem-id').val());
							if(CIID == cartitemid2){
								var productRow2 = $(this).closest('.cartitem-row');
								var customDetailSection2 = productRow2.find('.customgroup');
								var price = productRow2.find('.priceperitem');
								var priceempty = parseFloat(price.html().replace('RM ', ''));
								var qtyElement = productRow2.find('#number'); // Fetch the element
								var qty = parseInt(qtyElement.text().replace('x', '')); // Extract the quantity value
								var $quantityPerItem2 = parseInt(qty); // Convert to integer directly
								var currentQuantity2 = $quantityPerItem2; // Use the stored value directly
								var $addition = parseFloat(productRow2.find('.custom-price').val());
								var writeaddition = productRow2.find('.custom-price');
								var priceitem = parseFloat(priceempty / currentQuantity2);
								console.log('priceempty:', priceempty); // Add this line for debugging
								console.log('qty:', currentQuantity2); // Add this line for debugging
								console.log('priceitem:', priceitem);
								console.log('customprice:', customprice);
								if (isChecked) {
									// Append the new custom detail
									customDetailSection2.append('<span class="customitem"> - ' + customname + '</span>');
									priceitem = parseFloat(priceitem + customprice);
									$addition = $addition + customprice;
								} else {
									// Remove the existing custom detail with the same name
									customDetailSection2.find('.customitem').filter(function() {
										return $(this).text().includes(customname); // Check if the custom detail contains the custom name
									}).remove();
									priceitem = parseFloat(priceitem - customprice);
									$addition = $addition + customprice;
								}
								var subprice = parseFloat(priceitem * currentQuantity2);
								console.log('priceitem:', subprice);
								price.html('RM ' + subprice.toFixed(2));
								writeaddition.val($addition.toFixed(2));
								updateTotal();
								$.ajax({
									url: 'cart.php',
									type: 'POST',
									data: {
										sub__price: subprice,
										cart_item_id: cartitemid2
									}
								});
							}
						});
						console.log({ 
							inputValue: inputValue, 
							isChecked: isChecked, 
							cartitemid2: cartitemid2,
							pid2: pid2
						});
						
					},
					error: function(xhr, status, error) {
						// Handle error response
						console.error('Error updating database:', error);
					}
				});
			});
		});
		
		$(document).ready(function(){
			$('.inputGroup input[type="radio"]').change(function(){
				var $input = $(this);
				
				// Remove active class from all inputs in the same group
				$input.closest('.inputGroup').siblings('.inputGroup').removeClass('active');
				$input.closest('.inputGroup').siblings('input[type="radio"]').not(this).prop('checked', false);
				// Add active class to the selected input
				if ($input.is(':checked')) {
					$input.closest('.inputGroup').addClass('active');
				} else {
					$input.closest('.inputGroup').removeClass('active');
				}
				var inputValue = parseInt($(this).val());
				var isChecked = parseInt($(this).is(':checked') ? 1 : 0);
				var cartitemid2 = parseInt($(this).closest('.oneproduct').find('.cartitem-id').val());
				var pid2 = parseInt($(this).closest('.oneproduct').find('.product-id').val());
				var pprice2 = parseFloat($(this).closest('.oneproduct').find('.product-price').val());
				var ccid = parseInt($(this).closest('.col-md-4').find('.ccgroup').val());
				var customname = $(this).closest('.inputGroup').find('label').text(); 
				var customprice = parseFloat($(this).closest('.inputGroup').find('.price').text().split('RM ')[1]);
				var productrow = $(this).closest('.oneproduct');
				var customDetailSection = productrow.find('.cgroup');

				$.ajax({
					url: 'cart.php',
					type: 'POST',
					data: { 
						inputValue3: inputValue, 
						isChecked3: isChecked, 
						cartitemid3: cartitemid2,
						ccid3: ccid,
						pid3: pid2
					},
					success: function(response) {
						// Handle success response
						console.log('Database updated successfully.');

						customDetailSection.find('.ccrequire'+ccid).remove();
						if (isChecked) {
							// Append the new custom detail
							customDetailSection.append('<span class="cdetail ccrequire' + ccid + '"> - ' + customname + '</span>');
						}

						$('.cartitem-row').each(function(){
							var CIID = parseInt($(this).find('.cartitem-id').val());
							if(CIID == cartitemid2){
								productRow2 = $(this).closest('.cartitem-row');
								var customDetailSection2 = productRow2.find('.customgroup');
								var price = productRow2.find('.priceperitem');
								var priceempty = parseFloat(price.text().replace('RM ', ''));
								var qtyElement = productRow2.find('#number'); 
								var qty = parseInt(qtyElement.text().replace('x', '')); 
								var $quantityPerItem2 = parseInt(qty);
								var currentQuantity2 = $quantityPerItem2;
								var $addition = parseFloat(productRow2.find('.custom-price').val());
								var writeaddition = productRow2.find('.custom-price');
								var priceitem = parseFloat(priceempty / currentQuantity2);
								console.log('priceempty:', priceempty);
								console.log('qty:', currentQuantity2); 
								console.log('priceitem:', priceitem);
								console.log('customprice:', customprice);

								customDetailSection2.find('.ccrequire'+ccid).remove();
								if (isChecked) {
									customDetailSection2.append('<span class="customitem ccrequire' + ccid + '"> - ' + customname + '</span>');
									priceitem = parseFloat(priceitem + customprice);
									$addition = $addition + customprice;
								}
								var totalcustomradioprice = 0;
								productrow.find('.inputGroup.active').each(function(){
									var $input = $(this);
									var customprice2 = parseFloat($input.closest('.inputGroup').find('.price').text().split('RM ')[1]); // Get the custom price
									totalcustomradioprice += customprice2;
								});
								priceitem = parseFloat(pprice2 + totalcustomradioprice);
								console.log('totalcustomradioprice: ' + totalcustomradioprice);
								console.log('priceitem:', priceitem);
								var subprice = parseFloat(priceitem * currentQuantity2);
								console.log('priceitem:', subprice);
								price.html('RM ' + subprice.toFixed(2));
								writeaddition.val($addition.toFixed(2));
								updateTotal();
								$.ajax({
									url: 'cart.php',
									type: 'POST',
									data: {
										sub__price: subprice,
										cart_item_id: cartitemid2
									}
								});
							}
						});
						console.log({ 
							inputValue: inputValue, 
							isChecked: isChecked, 
							cartitemid2: cartitemid2,
							pid2: pid2
						});
						
					},
					error: function(xhr, status, error) {
						// Handle error response
						console.error('Error updating database:', error);
					}
				});
			});
		});

		// Function to update total
		function updateTotal() {
			var subtotal = 0;
			$('.priceperitem').each(function() {
				subtotal += parseFloat($(this).text().replace('RM ', ''));
			});
			var discountpercentage = parseFloat($('.discountamount').text());
			var discount = subtotal * (discountpercentage/100);
			var deliveryFee = parseFloat(<?php echo $delivery_fee; ?>);
			//var sst = parseFloat(subtotal * <?php //echo $sst ?>);
			var total = parseFloat(subtotal + deliveryFee - discount);

			// Update total elements on the page
			$('#subtotal').text('RM ' + subtotal.toFixed(2));
			$('#deliveryFee').text('RM ' + deliveryFee.toFixed(2));
			// $('#sst').text('RM ' + sst.toFixed(2));
			$('#total').text('RM ' + total.toFixed(2));
			$('#discount').text('RM ' + discount.toFixed(2));
			console.log("time" + <?php echo $isAfterTenPM; ?>);
		}
		//remove button
		$(document).ready(function(){
			$('.remove').on('click', function(){
				var $button = $(this);
				var $product = $button.closest('.oneproduct');
				var $cartitemid4 = parseInt($product.find('.cartitem-id').val());
				var $pid4 = parseInt($product.find('.product-id').val());

				// Show confirmation dialog
				// if(confirm('Are you sure you want to remove this product?')) {
				swal({
					title: 'Remove Product',
					text: 'Are you sure you want to remove this product?',
					icon: 'warning',
					buttons: {
						cancel: {
							text: 'Cancel',
							value: null,
							visible: true,
							className: 'swal-button swal-button--cancel', // Custom class for the Cancel button
							closeModal: true,
						},
						confirm: {
							text: 'Confirm',
							value: true,
							visible: true,
							className: 'swal-button swal-button--confirm', // Custom class for the Confirm button
							closeModal: true,
						},
					},
				}).then((confirmation) => {
					if (confirmation) {
						$.ajax({
							url: 'cart.php',
							type: 'POST',
							data: {
								cartitemid4: $cartitemid4,
								pid4: $pid4,
							},
							success: function(response) {
								if (response.includes('Product removed successfully')) {
									$product.remove();
									var unavailableProducts = 0;
									$('.oneproduct').each(function(index){
										if(!$(this).hasClass('unavailable')) {
											// Update product index
											$(this).find('.numbering').text(index + 1);
										} else {
											unavailableProducts++;
										}
									});
									if (unavailableProducts == 0) {
										$('#notAvailableSpan').hide();
									} else {
										$('#notAvailableSpan').show();
									}
									$('.carttable-row').each(function() {
										var CIID2 = parseInt($(this).find('.cartitem-id').val());
										if (CIID2 === $cartitemid4) {
											productRow4 = $(this).closest('.carttable-row');
											productRow4.remove();
											updateTotal();
										}
									});
									checkproduct();
								} else {
									console.error('Error removing product:', response);
								}
							},
							error: function(xhr, status, error) {
								console.error('AJAX Error:', error);
							}
						});
					}
				});
			});
		});
		
		function gotonext(){
			var hasUnavailableProduct = false;
			var hasProduct = false;
			$('.oneproduct').each(function() {
				hasProduct = true;
				if ($(this).hasClass('unavailable')) {
					hasUnavailableProduct = true;
					return false;
				}
			});
			var nowtime = parseFloat(<?php echo $isAfterTenPM; ?>);
			if ( nowtime >= 23.5 || nowtime < 2) {
				swal({
					title: 'Store Closed',
					text: 'The store is closed. Please come back tomorrow.',
					icon: 'error',
					button: 'OK'
				});
				return; 
			}
			if ( nowtime <= 12 && nowtime > 2) {
				swal({
					title: 'Store Closed',
					text: 'The store is not open yet. Please checkout later.',
					icon: 'error',
					button: 'OK'
				});
				return; 
			}
			var requirevalid = true;

			// Check each formcustom for radio button validation
			$('.formcustom').each(function() {
				var formIsValid = false;
				var foundcount = 0;
				// Check radio buttons (inputrd) in current form
				$(this).find('.inputrd').each(function() {
					if ($(this).find('input[type="radio"]').is(':checked')) {
						formIsValid = true;
						return false; // Exit the loop if a radio button is checked
					}
					foundcount++;
				});
				if(foundcount == 0){
					formIsValid = true;
				}

				// Display a warning if required radio button is not selected
				if (!formIsValid) {
					requirevalid = false;
					return false; // Exit each loop if any form is invalid
				}
			});

			if (!hasProduct) {
				// alert('Your cart is empty. Please add products to proceed to checkout.');
				swal({
					title: 'Empty Cart',
					text: 'Your cart is empty. Please add products to proceed to checkout.',
					icon: 'error',
					button: 'OK'
				});
			} else if (hasUnavailableProduct) {
				swal({
					title: 'Unavailable Product',
					text: 'Please remove all unavailable products from your cart before proceeding to checkout.',
					icon: 'error',
					button: 'OK'
				});
			}  else if (!requirevalid) {
				swal({
					title: 'Product Customization',
					text: 'Please ensure the customization of products you have selected.',
					icon: 'warning',
					button: 'OK'
				});
			} else {
				window.location.href = "checkout.php";
			}
		}

		function disapplycoupon(){
			var promoid = <?php echo is_null($promo_id) ? 0 : $promo_id; ?>;
			if(promoid == 0){
				swal({
					title: 'Remove Failed',
					text: 'Invalid or expired promo code. Please apply a promo code first.',
					icon: 'warning',
					button: 'OK'
				});
				return;
			}
			$.ajax({
				url: 'cart.php',
				method: 'POST',
				data:{
					disapply: "disapply"
				},
				success: function(response){
					if(response.includes("successfully")){
						swal({
							title: 'Remove Success',
							text: 'Promo code be removed successfully.',
							icon: 'success',
							button: 'OK'
						}).then((result) => {
							if(result)
								window.location.href = window.location.href;
						});
					} 
				}
			});
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
			var nowtime = parseFloat(<?php echo $isAfterTenPM; ?>);
			if(nowtime >= 23 && nowtime < 23.5){
				$('.lastcall').show();
			}else{
				$('.lastcall').hide();
			}
		});

		function checkpromo() {
			var promoInput = document.getElementById('promocode').value.trim();
			
			if (promoInput === '') {
				// alert('Please enter a promo code.');
				swal({
					title: 'Applied Failed',
					text: 'Please enter the promo cofde first.',
					icon: 'warning',
					button: 'OK'
				});
				return false;
			}
			
			return true;
		}
	</script>
</body>
</html>
