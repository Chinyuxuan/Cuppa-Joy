<?php
	include("db_connection.php");
	session_start();
	if (!isset($_SESSION['O_ID'])){
		header("location:checkout.php");
		echo "
			<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
			<script>
				document.addEventListener('DOMContentLoaded', function() {
					swal({
						title: 'Error',
						text: 'Place order failed.',
						icon: 'error',
						button: 'OK'
					});
				});
             </script>";
		exit;
	}
	date_default_timezone_set('UTC');

	$custid = $_SESSION['customer_id'];
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
	date_default_timezone_set('UTC');
	$oid = $_SESSION['O_ID'];
	$todaydate = date('m-d');
	$currentDate = date("Y-m-d");
	// $customerBirthdate = date('m-d', strtotime($bod));
	// $discountamount = 0;
	// if($todaydate == $customerBirthdate){
	// 	$discountamount = 10;
	// }


	// Assuming the product ID is stored in a column named 'P_ID'
    $CartNo = "SELECT * FROM cart WHERE C_ID = ? AND C_Status = 'No-Paid'";
    $stmt = $con->prepare($CartNo);
    $stmt->bind_param("i", $custid);
    $stmt->execute();
    $result = $stmt->get_result();
	// Fetch the CT_ID from the result set
    if($row = $result->fetch_assoc()){
		$CT_ID = $row['CT_ID'];
		$promo_id = $row['Promo_ID'];
	}
    

	$pcodeid = null;
	$discountamount = 0;
	if(!is_null($promo_id)){
		$sql10 = "SELECT * FROM promo WHERE Promo_ID = ? AND Start_From <= ? AND End_By >= ?";
		$stmt10 = $con->prepare($sql10);
		$stmt10 -> bind_param("iss", $promo_id, $currentDate, $currentDate);
		$stmt10 -> execute();
		$result10 = $stmt10->get_result();
		if($row10 = $result10->fetch_assoc()){
			// Check if the promo end date is greater than or equal to the current date
			if($row10['End_By'] >= $currentDate) {
				$pcodeid = $row10['Promo_ID'];
				$discountamount = $row10['Discount'];
			} else {
				// If the promo has ended, remove it from the cart
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
								title: 'Unavailable Promo Code',
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

	// Prepare and execute the query using the fetched CT_ID
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

	$ttl = 0;
	$sql5 = "SELECT * FROM reservation WHERE O_ID = ?";
	$stmt5 = $con->prepare($sql5);
	$stmt5->bind_param("i", $oid);
	$stmt5->execute();
	$result5 = $stmt5->get_result();
	if($result5->num_rows == 0){
		// echo "<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>";
		// echo "<script>
		// 		swal({
		// 			title: 'Transaction failed',
		// 			text: 'Transaction process will be terminated. Please try again to make payment.',
		// 			icon: 'warning',
		// 			button: 'OK'
		// 		}).then((value) => {
		// 			window.history.back();
		// 		});
		// 	  </script>";
	}else{
		$row5 = $result5->fetch_assoc();
		$ttl = $row5['Total'];
	}

	if(isset($_POST['isdelete'])){
		$sql7 = "DELETE FROM reservation WHERE O_ID = ?";
		$stmt7 = $con->prepare($sql7);
		$stmt7 -> bind_param("i", $oid);
		$result7 = $stmt7->execute();
	}
	
    //show success or error message
    // echo $statusMsg;
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Responsive Bootstrap4 Shop Template, Created by Imran Hossain from https://imransdesign.com/">

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
	<script src="https://kit.fontawesome.com/4dd5e87a71.js" crossorigin="anonymous"></script>

    <title>Pay With Stripe - Cuppa Joy</title>
</head>
<body>
    <div class="pcontainer">
        <div class="col-lg-8">
            <div class="payment-card">
                <div class="totalpay">
					<ul class="cart-table">
						<hr />
						<?php
							$subtotal = 0;
							foreach($cartitems as $cartitem){
								$sql2 = "SELECT * FROM product WHERE P_ID =" . $cartitem['P_ID'];
								$result2 = mysqli_query($con, $sql2);
								if($result2){
									while($row = mysqli_fetch_array($result2)){
										$pname = $row['P_Name'];
										$pimage = $row['P_Photo'];
										$pprice = $row['P_Price'];
										echo '<li class="carttable-row">';
										echo '<div class="cartitem-row">';
										echo '<input type="hidden" class="cartitem-id" value="' . $cartitem['CI_ID'] . '">';
										echo '<input type="hidden" class="product-id" value="' . $cartitem['P_ID'] . '">';
										echo '<input type="hidden" class="product-price" value="' . $row['P_Price'] . '">';
										echo '<div class="">';
										echo '<div class="block">';
										echo '<div id="number">x' . $cartitem['Qty'] . '</div>';
										echo '</div>';
										echo '</div>';
										echo '<div class="pphoto"><img src="../image/product/' . $pimage .'"></div>';
										echo '<div class="pdetail">';
										echo '<h5>' . $pname . '</h5>';
										echo '<div class="customgroup">';

										$sql3 = "SELECT * FROM Details WHERE c_item_id  = ? ORDER BY customize_id ASC";
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
										// $priceperitem = $cartitem['sub_price'] + ($addprice * $cartitem['Qty']);
										// echo '<span>add egg</span>';
										echo '</div>';
										echo '<input type="hidden" class="custom-price" value="' . $addprice . '">';
										echo '<div class="priceperitem">RM ' . number_format($cartitem['sub_price'], 2) . '</div>';
										echo '</div>';
										echo '<hr />';
										echo '</li>';
										
										$subtotal += $cartitem['sub_price'];
									}
								}
							}
							
							$delivery_fee = 5;
							// $sst = 0.06;
							$discount = $subtotal * ( $discountamount / 100);
							$total = $subtotal + $delivery_fee - $discount;
						?>
						</ul>
					<div>You have to pay <b>RM <?php echo number_format($ttl, 2); ?></b> (including delivery fee and discount).</div>
				</div>
                <div class="carddetail">
					<form action="submit.php" method="POST" id="paymentFrm">
						<div class="row">
						<label for="name">Card Holder Name</label>
							<input type="text" name="name" id="name" size="50" value="" placeholder="Card Holder Name" required/>
						</div>
						<div class="row">
							<label for="cnumber">CARD NUMBER</label>
							<div class="c-number" id="c-number">
								<input id="cnumber" class="cc-number" maxlength="19" name="card_num" placeholder="Card number" required>
								<i class="fa-solid fa-credit-card" style="margin: 0;"></i>
							</div>
						</div>
						<div class="row c-details">
							<div class="right">
								<label for="cc-exp">CARD EXPIRY</label>
								<div class="ccexp-box">
									<input name="exp_month" size="2" maxlength="2" class="card-expiry-month" id="card-expiry-month" min="1" max="12" placeholder="MM"/>
									<span> / </span>
									<input name="exp_year" size="2" maxlength="2" class="card-expiry-year" id="card-expiry-year" min="24" max="30" placeholder="YY"/>
								</div>
							</div>
							<div class="left">
								<label for="cvv">CARD CVV</label>
								<div class="cvv-box" id="cvv-box">
                                	<input id="cvv" size="3" class="cc-cvv" name="cvc" placeholder="CVV" required maxlength="3">
									<i class="fa-solid fa-circle-question" title="3 digits on the back of the card" style="cursor: pointer;"></i>
								</div>
							</div>
						</div>
						<div class="row">
							<button type="submit" id="payBtn" class="btn">Submit Payment</button>
						</div>
					</form>
				</div>
            </div>
        </div>
    </div>
    

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
	<!-- Stripe JavaScript library -->
	<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
	<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>

	
    <script>
		//set your publishable key
		Stripe.setPublishableKey('pk_test_51P417pCwq26I0BhNGEdkBzymyafNdECdelrpVbIQgXZJnws9Nc4cES3EnJ1PYUWbi5I64o4TiOoxLJPFQNs4o9PK00qjVWpTIb');
		let submitButtonClicked = false;
        //callback to handle the response from stripe
        function stripeResponseHandler(status, response) {
            if (response.error) {
                //enable the submit button
                $('#payBtn').removeAttr("disabled");
                //display the errors on the form
                $(".payment-errors").html(response.error.message);
            } else {
                var form$ = $("#paymentFrm");
                //get token id
                var token = response['id'];
                //insert the token into the form
                form$.append("<input type='hidden' name='stripeToken' value='" + token + "' />");
                //submit form to the server
                form$.get(0).submit();
            }
        }
        $(document).ready(function() {
            //on form submit
            $("#paymentFrm").submit(function(event) {
                //disable the submit button to prevent repeated clicks
                $('#payBtn').attr("disabled", "disabled");
                submitButtonClicked = true;
                //create single-use token to charge the user
                Stripe.createToken({
                    number: $('.cc-number').val(),
                    cvc: $('.cc-cvv').val(),
                    exp_month: $('.card-expiry-month').val(),
                    exp_year: $('.card-expiry-year').val()
                }, stripeResponseHandler);
                
                //submit from callback
                return false;
            });
        });

		var total = <?php echo $ttl; ?>;
		$(document).ready(function(){
			if(total == 0){
				swal({
					title: 'Transaction failed',
					text: 'Transaction process will be terminated. Please try again to make payment.',
					icon: 'warning',
					button: 'OK'
				}).then((value) => {
					window.location.href = 'checkout.php';
				});
			}
		});

		let cNumber = document.getElementById('cnumber');
		cNumber.addEventListener('keyup', function(e){
			let num = cNumber.value;
			let newValue='';
			num = num.replace(/\s/g, '');
			for(var i=0; i<num.length; i++){
				if(i%4 == 0 && i > 0) newValue = newValue.concat(' ');
				newValue = newValue.concat(num[i]);
				cNumber.value = newValue;
			}
			let ccNum = document.getElementById('c-number');
			if(num.length<16){
				ccNum.style.border = "2px solid red";
			}else{
				ccNum.style.border="2px solid green"
			}
		});
		let word = "";
		let eDate = document.getElementById('card-expiry-month');
		eDate.addEventListener('keyup', function(e){
			let newInput = parseInt(eDate.value);
			if(isNaN(newInput) || newInput > 12 || newInput < 1){
				eDate.style.border="2px solid red";
			} else {
				eDate.style.border="2px solid green";
			}
		});

		let expYearInput = document.getElementById('card-expiry-year');
		expYearInput.addEventListener('keyup', function(e){
			let currentYear = new Date().getFullYear() % 100; // Extract last two digits of current year
			let enteredYear = parseInt(expYearInput.value);
			if(isNaN(enteredYear) || enteredYear < currentYear || (enteredYear > currentYear + 5)){
				expYearInput.style.border="2px solid red";
			} else {
				expYearInput.style.border="2px solid green";
			}
		});
		
		let cvv = document.getElementById('cvv');
		cvv.addEventListener('keyup', function(e){
			let elen = cvv.value;
			let cvvbox = document.getElementById('cvv-box');
			let regex = /^[0-9]+$/;
			if(elen.length<3 || !regex.test(elen)){
				cvvbox.style.border="2px solid red";
			}else{
				cvvbox.style.border="2px solid green";
			}
		});

		$(document).ready(function(){

			function invalidinput(word){
				// alert("Please enter a valid " + word + " input");
				swal("Error", "Please enter a valid " + word + " input", "error");
			}
			function checkvalid(event){
				event.preventDefault();

				let newInput = parseInt($('#card-expiry-month').val());
				let enteredYear = parseInt($('#card-expiry-year').val());
				let currentYear = new Date().getFullYear() % 100;
				let currentMonth = new Date().getMonth() + 1;
				let num2 = $('#cnumber').val().replace(/\s/g, '');
				let regex = /^[0-9]+$/;
				let regex3 = /^[a-zA-Z\s]+$/;
				let cvvvalue = $('#cvv').val();
				let nameentered = $('#name').val().trim();

				if (!nameentered) {
					swal("Error", "Please enter card holder name", "error");
					return false;
				}
				if (!regex3.test(nameentered)) {
					swal("Error", "Name can only contain alphabetic characters and spaces.", "error");
					return false;
				}
				if (!regex.test(num2)) {
					swal("Error", "Invalid card number entered.", "error");
					return false;
				}
				if (!Stripe.card.validateCardNumber(num2)) {
					swal("Error", "Invalid card number.", "error");
					return false;
				}
				if(num2.length < 16 || isNaN(num2)){
					invalidinput("card number");
					return false;
				}
				if (isNaN(newInput) || newInput > 12 || newInput < 1) {
					invalidinput("month");
					return false;
				}
				if (isNaN(enteredYear) || enteredYear < currentYear || (enteredYear > currentYear + 5)) {
					invalidinput("year");
					return false;
				}
				
				if (cvvvalue.length < 3 || !regex.test(cvvvalue)) {
					swal("Error", "Invalid cvv number.", "error");
					return false;
				}
				console.log("month:" + newInput);
				console.log("year:" + enteredYear);
				if ((enteredYear === currentYear && newInput < currentMonth) ||
					(enteredYear === currentYear + 5 && newInput > currentMonth)) {
						swal("Error", "Please enter a valid month and year input", "error");
					return false;
				}
				return true;
			}

			$('#paymentFrm').on('submit', function(event) {
				if (!checkvalid(event)) {
					event.preventDefault();
				}
			});
		});

		

		// Function to cancel payment and redirect back to the previous page
		function cancelPayment() {
			// Perform cancellation logic here, such as displaying a message to the user
			// alert("Payment process has been cancelled due to inactivity.");
			swal({
				title: 'Overtime',
				text: 'Payment process has been cancelled due to inactivity.',
				icon: 'warning',
				button: 'OK'
			});
			var isdelete = 1;
			$.ajax({
				url: 'stripepay.php',
				type: 'POST',
				data: {
					isdelete: isdelete,
				},
				success: function(response){
					// Redirect back to the previous page
					window.history.back();
				}
			});	
			
		}

		// Start the timer when the page loads
		var timer = setTimeout(cancelPayment, 5 * 60 * 1000); // 5 minutes in milliseconds

		// Reset the timer when the submit button is clicked
		document.getElementById("paymentFrm").addEventListener("submit", function() {
			clearTimeout(timer); // Reset the timer
		});

		// Reset the timer when any input field is changed
		var inputFields = document.querySelectorAll("input, select, textarea");
		inputFields.forEach(function(input) {
			input.addEventListener("change", function() {
				clearTimeout(timer); // Reset the timer
				timer = setTimeout(cancelPayment, 5 * 60 * 1000); // Restart the timer
			});
		});
		$(window).on('beforeunload', function() {
			// Make an AJAX request to delete the row
			if(!submitButtonClicked){
				// var reservationid = parseInt();
				$.ajax({
					url: 'checkout.php',
					type: 'POST',
					data: {
						reservationid: <?php echo $oid; ?>, // You need to pass the identifier of the row to delete
					},
					success: function(response){
						console.log('Row deleted successfully');
					},
					error: function(xhr, status, error) {
						console.error('Error deleting row:', error);
					}
				});
				// cancelPayment();
			}
		});
    </script>
</body>
</html>