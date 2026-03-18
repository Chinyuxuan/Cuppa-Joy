<?php
	include("db_connection.php");
	session_start();

	date_default_timezone_set('UTC');

	$custid = $_SESSION['customer_id'];
	unset($_SESSION['O_ID']);

	if (!isset($custid)){
		header("location:sign-in.php");
		exit;
	}

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

	$recordsPerPage = 10;
	if (isset($_GET['page']) && is_numeric($_GET['page'])) {
		$currentPage = $_GET['page'];
	} else {
		$currentPage = 1;
	}
	$offset = ($currentPage - 1) * $recordsPerPage;
	$stmt14 = $con->prepare("SELECT COUNT(*) AS total FROM cart WHERE C_ID = ? AND C_Status = 'Paid'");
	$stmt14->bind_param("i", $custid);
	$stmt14->execute();
	$result14 = $stmt14->get_result();
	$row14 = $result14->fetch_assoc();
	$totalRecords = $row14['total'];
	$totalPages = ceil($totalRecords / $recordsPerPage);
	function isActive($page)
	{
		$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
		return ($currentPage == $page) ? 'class="active"' : '';
	}

	function getPreviousPage($currentPage)
	{
		return ($currentPage > 1) ? $currentPage - 1 : 1;
	}

	function getNextPage($currentPage, $totalPages)
	{
		return ($currentPage < $totalPages) ? $currentPage + 1 : $totalPages;
	}

	$sql2 = "SELECT * FROM cart WHERE C_ID = ? AND C_Status = 'Paid'  ORDER BY CT_ID DESC LIMIT ? OFFSET ?";
	$stmt2 = $con->prepare($sql2);
	$stmt2 -> bind_param("iii", $custid, $recordsPerPage, $offset);
	$stmt2 -> execute();
	$result2 = $stmt2 -> get_result();
	$carts = [];
	if($result2->num_rows > 0){
		while($row2 = $result2->fetch_assoc()){
			$carts[] = $row2;
		}
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Responsive Bootstrap4 Shop Template, Created by Imran Hossain from https://imransdesign.com/">

    <title>History - Cuppa Joy</title>
    
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
    <!-- icon library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- data-bs-dismiss bootstrap library --><!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3F+faIlPW5pLOi2AA8zFZtQi8M0Iby0k18hFXxKGfr6KO29q/Zy5K+pw8bI4kFq" crossorigin="anonymous">

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
						<h1>Order History</h1>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end breadcrumb section -->
	
    <!-- Container - History -->
    <div class="mt-80 mb-150">
        <div class="history container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ordertable">
                        <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search something..">

                        <table id="myTable">
                            <thead class="total-table-head">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Order Date</th>
                                    <th>Order Time</th>
									<th>Delivery Status</th>
                                    <th>Total Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="myTablebody">
								<?php
									foreach($carts as $cart){
										$sql3 = "SELECT * FROM reservation WHERE CT_ID = ?";
										$stmt3 = $con->prepare($sql3);
										$stmt3 -> bind_param("i", $cart['CT_ID']);
										$stmt3->execute();
										$result3 = $stmt3->get_result();
										$reservation = $result3->fetch_assoc();
										if ($reservation) {
											// Convert date and time to UTC
											$dateTimeUTC = new DateTime($reservation['Date'] . ' ' . $reservation['Time']);
											// Adjust the time zone to UTC+8
											$dateTimeUTC->setTimezone(new DateTimeZone('Asia/Singapore'));
											$dateLocal = $dateTimeUTC->format('Y-m-d');
											$timeLocal = $dateTimeUTC->format('H:i:s');
											echo '<tr>';
											echo '<td>' .$reservation['O_ID']. '</td>';
											echo '<td>' .date('d-m-Y', strtotime($dateLocal)). '</td>';
											echo '<td>' .$timeLocal. '</td>';
											$btnstatus="";
											if($reservation['Delivery_Status'] == "pending"){
												$statusword = "pending";
												$btnstatus = "disabled";
											}else if ($reservation['Delivery_Status'] == "completed"){
												$statusword = "completed";
											}
											$btnword = "Rate";
											$sql12 = "SELECT * FROM rating WHERE O_ID = ?";
											$stmt12 = $con->prepare($sql12);
											$stmt12 -> bind_param("i", $reservation['O_ID']);
											$stmt12 ->execute();
											$result12 = $stmt12->get_result();
											if($result12->num_rows == 1){
												$btnword = "View Rating";
											}
											echo '<td><div class="statustag '.$statusword.'">'.$statusword.'</div></td>';
											echo '<td>RM '.number_format($reservation['Total'], 2).'</td>';
											echo '<td class="btncol"><button class=" boxed-btn viewdetail-btn" type="button" onclick="showPopup('.$reservation['O_ID'].')">View All Details</button>';
											echo '<button onclick="gotorating('.$reservation['O_ID'].')" class="boxed-btn rate-btn '.$btnstatus.'" '.$btnstatus.'>'.$btnword.'</button></td>';
											echo '</tr>';
										}
									}
								?>
							</tbody>
                        </table>
                    </div>
					<?php
						foreach($carts as $cart2){
							$sql11 = "SELECT * FROM reservation WHERE CT_ID = ?";
							$stmt11 = $con->prepare($sql11);
							$stmt11 -> bind_param("i", $cart2['CT_ID']);
							$stmt11->execute();
							$result11 = $stmt11->get_result();
							$reservation = $result11->fetch_assoc();
							if ($reservation){
								//detailbox
								echo '<div class="overlay detailbox_'.$reservation['O_ID'].'" id="popupContainer">';
								echo '<div class="detail-dialog modal-dialog-centered" >';
								echo '<div class="modal-content">';
								echo '<div class="modal-header">';
								echo '<h4 class="modal-title">Order ID: ' . $reservation['O_ID'] . '</h4>';
								echo '<button type="button" class="close-modal" onclick="hidePopup('.$reservation['O_ID'].')" aria-label="Close">&times;</button>';
								echo '</div>';
								echo '<div class="modal-body">';
								echo '<div class="left">';
								echo '<div class="leftup">';
								echo '<h5>Rider Details</h5>';
								echo '<div class="rdetail">';
								$sql4 = "SELECT * FROM rider WHERE R_ID = ?";
								$stmt4 = $con->prepare($sql4);
								$stmt4->bind_param("s", $reservation['R_ID']);
								$stmt4->execute();
								$result4 = $stmt4->get_result();
								if($row4 = $result4->fetch_assoc()){
									echo '<img id="photo-rider" src="../image/rider/'.$row4['R_Photo'].'">';
									echo '<span>'.$row4['R_Name'].'</span>';
								}
								echo '</div>';
								echo '</div>';
								echo '<div class="leftmedium">';
								echo '<h5>Payment Details</h5>';
								$sql5 = "SELECT * FROM payment WHERE O_ID = ?";
								$stmt5 = $con->prepare($sql5);
								$stmt5->bind_param("i", $reservation['O_ID']);
								$stmt5->execute();
								$result5 = $stmt5->get_result();
								if($row5 = $result5->fetch_assoc()){
									$paymethod = $row5['PM_Method'];
								}
								echo '<p>Payment Method: '.$paymethod.'</p>';
								echo '</div>';
								echo '<div class="leftbottom">';
								echo '<h5>Delivery Details</h5>';
								$sql6 = "SELECT * FROM address WHERE A_ID = ?";
								$stmt6 = $con->prepare($sql6);
								$stmt6->bind_param("i", $reservation['A_ID']);
								$stmt6->execute();
								$result6 = $stmt6->get_result();
								if($row6 = $result6->fetch_assoc()){
									$address = $row6['Address_1']. ', ' . $row6['Address_2'] . ', ' . $row6['Postcode'] . ' ' . $row6['City'] . ', ' . $row6['state_country'] ;
								}
								echo '<p>'.$address.'</p>';
								echo '<p><b>Receiver Name: </b><span>'.$reservation['ReceiverName'].'</span></p>';
								echo '</div>';
								echo '</div>';
								echo '<div class="right">';
								echo '<div class="rightbottom">';
								echo '<h5>Order Details</h5>';
								echo '<table class="odrdetail">';
								echo '<tbody>';
								$sql7 = "SELECT * FROM cart_item WHERE CT_ID = ?";
								$stmt7 = $con->prepare($sql7);
								$stmt7->bind_param("i", $cart2['CT_ID']);
								$stmt7->execute();
								$result7 = $stmt7->get_result();
								$cartitems = [];
								if($result7->num_rows > 0){
									while($row7 = $result7->fetch_assoc()){
										$cartitems[] = $row7;
									}
								}
								$btnstatus = "";
								foreach($cartitems as $cartitem) {
									$sql8 = "SELECT * FROM product WHERE P_ID = ?";
									$stmt8 = $con->prepare($sql8);
									$stmt8->bind_param("i", $cartitem['P_ID']);
									$stmt8->execute();
									$result8 = $stmt8->get_result();
								
									if($row8 = $result8->fetch_assoc()) {
										$pname = $row8['P_Name'];
										$pimage = $row8['P_Photo'];
										$pprice = $row8['P_Price'];
									}
								
									echo '<tr>';
									echo '<td>&times;<span class="pqty">'.$cartitem['Qty'].'</span></td>';
									echo '<td><img class="pphoto" src="../image/product/'.$pimage.'"></td>';
									echo '<td class="pname">';
									echo '<div><h6>'.$pname.'</h6></div>';
									
									$sql9 = "SELECT * FROM Details WHERE c_item_id = ? ORDER BY customize_id ASC";
									$stmt9 = $con->prepare($sql9);
									$stmt9->bind_param("i", $cartitem['CI_ID']);
									$stmt9->execute();
									$result9 = $stmt9->get_result();
									$customitems = [];
								
									if($result9->num_rows > 0) {
										while($row9 = $result9->fetch_assoc()) {
											$customitems[] = $row9;
										}
									}
									
									$addprice = 0;
									$priceperitem = 0;
									
									foreach($customitems as $customitem) {
										$sql10 = "SELECT custom.*, cc.CC_Group, cc.compulsory_status FROM customization AS custom 
												  INNER JOIN customize_category AS cc 
												  ON custom.CC_ID = cc.CC_ID 
												  WHERE custom.Custom_ID = ?";
										$stmt10 = $con->prepare($sql10);
										$stmt10->bind_param("i", $customitem['customize_id']);
										$stmt10->execute();
										$result10 = $stmt10->get_result();
								
										if($row10 = $result10->fetch_assoc()) {
											$customname = $row10['Custom_Name'];
											$customprice = $row10['Custom_Price'];
										}
										$addprice += $customprice;
										echo '<span> - '.$customname.'</span>';
									}
									// $priceperitem = $cartitem['sub_price'] + ($addprice * $cartitem['Qty']);
									echo '</div>';
									echo '</td>';
									echo '<td> RM '.number_format($cartitem['sub_price'], 2).'</td>';
									echo '</tr>';
									
									
								}
								if($reservation['Delivery_Status'] == "pending")
									$btnstatus = "disabled";
								$btnword = "Rate";
								$sql13 = "SELECT * FROM rating WHERE O_ID = ?";
								$stmt13 = $con->prepare($sql13);
								$stmt13 -> bind_param("i", $reservation['O_ID']);
								$stmt13 ->execute();
								$result13 = $stmt13->get_result();
								if($result13->num_rows == 1){
									// $btnstatus = "disabled";
									$btnword = "View Rating";
								}
								echo '</tbody>';
								echo '</table>';
								echo '<div class="showtotalprice">';
								echo '<span><b>Total (including delivery fee and discount): </b></span>';
								echo '<span><b>RM ' . number_format($reservation['Total'], 2) . '</b></span>';
								echo '</div>';
								echo '</div>';
								echo '</div>';
								echo '</div>';
								echo '<div class="modal-footer">';
								echo '<button onclick="gotorating('.$reservation['O_ID'].')" class="boxed-btn rate-btn '.$btnstatus.'" '.$btnstatus.'>'.$btnword.'</button>';
								echo '</div>';
								echo '</div>';
								echo '</div>';
								echo '</div>';
							}
						}
					?>
                    <!-- <div class="overlay" id="popupContainer">
						<div class="detail-dialog modal-dialog-centered" >
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title">Order ID: </h5>
									<button type="button" class="close-modal" onclick="hidePopup()" aria-label="Close">&times;</button>
								</div>
								<div class="modal-body">
									<div class="left">
										<div class="leftup">
											<h5>Rider Details</h5>
											<div class="rdetail">
												<img id="photo-rider" src="../Rider/assets/image/profile-img.jpg">
												<span>K. Albert</span>
											</div>
											
										</div>
										<div class="leftbottom">
											<h5>Payment Details</h5>
											<p>Payment Method: Ewallet</p>
										</div>
									</div>
									<div class="right">
										<div class="rightup">
											<h5>Delivery Address</h5>
											<p>55, Jalan Hijauan 4/3, Horizon Hill</p>
										</div>
										<div class="rightbottom">
											<h5>Order Details</h5>
											<table class="odrdetail">
												<tbody>
													<tr>
														<td>&times;<span class="pqty">1</span></td>
														<td><img class="pphoto" src="assets/img/curry-puff.png"></td>
														<td class="pname">
															<div>Burger</div>
															<div></div>
														</td>
														
													</tr>
												</tbody>
											</table>
										</div>
										
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="boxed-btn rate-btn">Rate</button>
								</div>
							</div>
						</div>
					</div> -->
                </div>
            </div>
			<div class="row mb-80">
				<div class="col-lg-12 text-center">
				
				<?php
					if ($totalPages > 1) {
						echo '<div class="pagination-wrap">';
						echo '<ul>';
						
						$orangeColorClass = $totalPages >= 1 ? 'orange' : '';
						
						if($currentPage>1){
							$prevPage = getPreviousPage($currentPage);
							echo "<li><a href='?page=$prevPage' class='".($currentPage == 1 ? $orangeColorClass : '')."'>Prev</a></li>";
						}
						for ($i = 1; $i <= $totalPages; $i++) {
							echo "<li " . isActive($i) . "><a href='?page=$i' ".isActive($i).">$i</a></li>";
						}
				
						
						if($currentPage<$totalPages){
							$nextPage = getNextPage($currentPage, $totalPages);
							echo "<li><a href='?page=$nextPage'>Next</a></li>";
						}
				
						echo '</ul>';
						echo '</div>';
					}
				?>

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

    <!-- search table -->
    <script>
        function myFunction() {
          // Declare variables
          var input, filter, table, tr, td, i, txtValue;
          input = document.getElementById("myInput");
          filter = input.value.toUpperCase();
          table = document.getElementById("myTable");
          tr = table.getElementsByTagName("tr");
        
          for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            if (td) {
              txtValue = td.textContent || td.innerText;
              if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
              } else {
                tr[i].style.display = "none";
              }
            }
          }
        }
    
		function showPopup(orderid) {
			orderid = parseInt(orderid);
			var $box = $('.detailbox_' + orderid);
			$box.css('display', 'block');
		}

		function hidePopup(orderid) {
			orderid = parseInt(orderid);
			var $box = $('.detailbox_' + orderid);
			$box.css('display', 'none');
		}
		function gotorating(orderid){
			var $button = $(this);
			if($button.hasClass('disabled')){
				// alert("The rating function is not available now.");
				swal({
					title: 'Rate function not available.',
					text: 'The rating function is not available now!',
					icon: 'warning',
					button: 'OK'
				});
			}
			else {
				window.location.href="rating.php?orderid="+orderid;
			}
		}
		function confirmLogout() {
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
				if (result) {
					window.location.href = 'logout.php';
				}
			});

			return false;
		}
	</script>

</body>
</html>