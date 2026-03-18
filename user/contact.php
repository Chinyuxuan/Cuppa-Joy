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
$currentuser = $isSignedIn ? $_SESSION["customer_id"] : $defaultCustomerId;

// Retrieve user's information if signed in
if ($isSignedIn) {
    $sql = "SELECT * FROM `customer` WHERE C_ID = ?";
    $stmtUser = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmtUser, "s", $currentuser);
    mysqli_stmt_execute($stmtUser);
    $gotResult = mysqli_stmt_get_result($stmtUser);

    if ($gotResult && mysqli_num_rows($gotResult) > 0) {
        $row = mysqli_fetch_array($gotResult);
        $firstname = $row['C_Firstname'];
        $lastname = $row['C_Lastname'];
        $phno = $row['C_ContactNumber'];
        $CEmail = $row['C_Email'];
        $password = $row['C_PW'];
    }
}

//----------------------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == "POST") 
{
    // Retrieve subject and message from POST data
    $Subject = $_POST['subject'];
    $Message = $_POST['message'];

    // For signed-in users, use the session data
    if ($isSignedIn) {
        $Firstname = $firstname;
        $Lastname = $lastname;
        $Phonenumber = $phno;
        $Email = $CEmail; // Note: consider renaming one of these for clarity
        $status = "Non-Replied";
    } else {
        // For guests, retrieve from the form
        $Firstname = $_POST['firstname'];
        $Lastname = $_POST['lastname'];
        $Phonenumber = "60" . $_POST['phone'];
        $Email = $_POST['email'];
        $status = "Non-Replied";
    }


    // Check if all fields are not empty and phone number is valid
    if (!empty($Firstname) && !empty($Lastname) && !empty($Phonenumber) && !empty($Email) && !empty($Subject) && !empty($Message) && preg_match('/^\d{10,15}$/', $Phonenumber)) {
        // Prepare the SQL query excluding C_ID and C_Photo
        $query = "INSERT INTO `contact_us` (Firstname, Lastname, Phone, Email, Subject, Message, Contact_Status) VALUES (?,?,?,?,?,?,?)";
        $stmt = mysqli_prepare($con, $query);

        // Check for errors in preparing the statement
        if ($stmt === false) {
            die('Error preparing statement: ' . mysqli_error($con));
        }

        // Bind parameters to the prepared statement
        mysqli_stmt_bind_param($stmt, 'sssssss', $Firstname, $Lastname, $Phonenumber, $Email, $Subject, $Message, $status);

        // Execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Success message
           // echo "<script>alert('Form submitted successfully.');</script>";
        } else {
            die('Error executing statement: ' . mysqli_stmt_error($stmt));
        }
    } else {
        $missingFields = [];
        if (empty($Firstname)) $missingFields[] = "Firstname";
        if (empty($Lastname)) $missingFields[] = "Lastname";
        if (empty($Phonenumber) || !preg_match('/^\d{11,13}$/', $Phonenumber)) $missingFields[] = "Phone number";
        if (empty($Email)) $missingFields[] = "Email";
        if (empty($Subject)) $missingFields[] = "Subject";
        if (empty($Message)) $missingFields[] = "Message";
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                swal({
                    icon: 'error',
                    title: 'Missing Information',
                    text: 'Please enter valid information for the following field(s): " . implode(", ", $missingFields) . "',
                    button: 'OK'
                });
            });
        </script>";
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
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Responsive Bootstrap4 Shop Template, Created by Imran Hossain from https://imransdesign.com/">

	<!-- title -->
	<title>Contact Us - Cuppa Joy</title>

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

	<link rel="stylesheet" href="contact.css">


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
							<li><a href="index.php">Home</a></li>
								<li><a href="shop.php">Menu</a></li>
								<li><a href="promo.php"> Show Promo</a></li>
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
						<h1>Contact us</h1>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end breadcrumb section -->

	<!-- contact form -->
	<div class="contact-from-section mt-150 mb-150">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 mb-5 mb-lg-0">
					<div class="form-title">
						<h2>Have you any question?</h2>
						<p>Are you ready to take Cuppa Joy to the next level? Tell us your ideas! 
							We need creative and innovative individuals like you to tell us whatʼs up because we are all ears. 
							Dream it and we might just make it happen.</p>
					</div>
				 	<div id="form_status"></div>
					<div class="contact-form">
					<form type="POST" method="post" id="fruitkha-contact" onsubmit="return validateForm();">
    <p>
        <input type="text" placeholder="Firstname" name="firstname" id="firstname" value="<?php echo htmlspecialchars($isSignedIn ? $firstname : ''); ?>" oninput="validateFirstName()" <?php echo $isSignedIn ? 'readonly' : ''; ?>>
        <input type="text" placeholder="Lastname" name="lastname" id="lastname" value="<?php echo htmlspecialchars($isSignedIn ? $lastname : ''); ?>" oninput="validateLastName()" <?php echo $isSignedIn ? 'readonly' : ''; ?>>
		
		<span id="name-error-first" class="error-message"></span>
		<span id="name-error-last" class="error-message"></span>
		<br>
        <input type="email" placeholder="Email" name="email" id="email" value="<?php echo htmlspecialchars($isSignedIn ? $CEmail : ''); ?>" onkeyup="validateEmail()" <?php echo $isSignedIn ? 'readonly' : ''; ?>>
        <div class="phoneOnly">
            <span>+60:</span>
            <input type="text" placeholder="Enter 9-11 digits" name="phone" id="phone" value="<?php echo htmlspecialchars($isSignedIn ? substr($phno, 2) : ''); ?>" onkeyup="validatePhone()" <?php echo $isSignedIn ? 'readonly' : ''; ?>>
        </div>
        <span id="email-error"></span>
        <span id="phone-error"></span>
    </p>
    <p>
        <input type="text" placeholder="Subject" name="subject" id="subject">
    </p>
    <p>
        <textarea name="message" id="message" cols="30" rows="10" placeholder="Message"></textarea>
    </p>
    <input type="hidden" name="token" value="FsWga4&@f6aw" />
    <p><input type="submit" value="Submit"></p>
</form>

					</div>
				</div>
				<div class="col-lg-4">
					<div class="contact-form-wrap">
						<!--<div class="contact-form-box">
							<h4><i class="fas fa-map"></i> Shop Address</h4>
							<p><//?php echo $Address; ?></p>
						</div>-->
						<div class="contact-form-box">
							<h4><i class="far fa-clock"></i> Shop Hours</h4>
							<p>MONDAY - SUNDAY: 10 AM to 10 PM <br> </p>
						</div>
						<div class="contact-form-box">
							<h4><i class="fas fa-address-book"></i> Contact</h4>
							<p>Phone: 012-3568004<br> Email: cuppajoy88@gmail.com</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end contact form -->


	<!-- find our location -->
	<div class="find-location blue-bg">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 text-center">
					<p> <i class="fas fa-map-marker-alt"></i> Find Our Location</p>
					<span class="location">123, Jalan Ayer Keroh Lama,Kampung Baru Ayer Keroh, 75450 Ayer Keroh, Melaka
,Malaysia</span>
				</div>
			</div>
		</div>
	</div>
	<!-- end find our location -->

	<!-- google map section -->
		<div class="embed-responsive embed-responsive-21by9">
			<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3986.7006897279257!2d102.28080277496798!3d2.2651745977149176!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31d1e56ba327d929%3A0x856306957b336bde!2s123%2C%20Jln%20Ayer%20Keroh%20Lama%2C%20Kampung%20Baru%20Ayer%20Keroh%2C%2075450%20Ayer%20Keroh%2C%20Melaka!5e0!3m2!1sen!2smy!4v1710767139305!5m2!1sen!2smy width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
		</div>
		<!-- end google map section -->


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
	<!-- form validation js -->
	<script src="assets/js/form-validate.js"></script>
	<!-- main js -->
	<script src="assets/js/main.js"></script>

	<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
	<script>
//validate the form when submut to check all field
function validateForm() 
{
	// Validate both names using the specific functions
    var validFirstName = validateFirstName();
    var validLastName = validateLastName();

    // If either name validation fails, show an error message and prevent form submission
    if (!validFirstName ) {
		event.preventDefault();
        swal({
            icon: 'error',
            title: 'Invalid First Name',
            text: 'Please ensure that firstname are valid.'
        });
        return false; // Return false to indicate form should not submit
    }

	    // If either name validation fails, show an error message and prevent form submission
    if (!validLastName) {
		event.preventDefault();
        swal({
            icon: 'error',
            title: 'Invalid Last Name',
            text: 'Please ensure that lastname are valid.'
        });
        return false; // Return false to indicate form should not submit
    }

        // Validate email
        var email = document.getElementById("email").value;
        var phone = document.getElementById("phone").value;

        if (!validateEmail(email)) {
			event.preventDefault();
          

			swal({
            icon: 'error',
            title: 'Invalid Email',
            text: 'Please enter a valid email address.'
        });
            return false;
        }

        if (!validatePhone(phone)) {
			event.preventDefault();
			
			swal({
            icon: 'error',
            title: 'Invalid Phone Number',
            text: 'Please enter a valid phone number.'
        });
            return false;
        }

        return true;
}
//--------------------------------------------------------------
//email validation
var email = document.getElementById("email");
var email_error = document.getElementById("email-error")

function validateEmail()
{
	// Check if the phone number field is empty
    if (email.value.trim() === "") {
        email_error.innerHTML = ""; // Clear the error message
        return true; // Return true as there is no error
    }
  if(!email.value.match(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/))
  {
    email_error.innerHTML="Please make sure your email is valid"
    email_error.style.color="red";
    return false;
  }

  email_error.innerHTML="";
  return true;
}
//---------------------------------------------------------------------------
// Validate phone number
var phone = document.getElementById("phone");
var phone_error = document.getElementById("phone-error");

function validatePhone() {
    // Check if the phone number field is empty
    if (phone.value.trim() === "") {
        phone_error.innerHTML = ""; // Clear the error message
        return true; // Return true as there is no error
    }

    // Check if the phone number matches the expected format
    if (!phone.value.match(/^\d{9,11}$/)) {
      phone_error.innerHTML = "Mobile Number should be 9 to 11 digits";
      phone_error.style.color = "red";
      return false;
  }

    // If the phone number format is correct, clear the error message
    phone_error.innerHTML = "";
    return true;
}

//-------------------------------------------------------------------------------------
//validate the first name 
function validateFirstName() {
    var input = document.getElementById("firstname");
    var errorSpan = document.getElementById("name-error-first");
    var regex = /^[a-zA-Z\s]+$/;  // Allows alphabetic characters and spaces

    if (input.value === "") {
        errorSpan.textContent = ""; // Clears any previous error message
        return true; // Empty input is considered valid with no error message
    }

    if (input.value.trim() === "") {
        errorSpan.textContent = "Only alphabetic characters and space are allowed.";
        errorSpan.style.color = "red";
        return false; // Indicates validation failure for spaces only
    }

    if (!input.value.match(regex)) {
        errorSpan.textContent = "Only alphabetic characters and space are allowed.";
        errorSpan.style.color = "red";
        return false; // Indicates validation failure for invalid characters
    } else {
        errorSpan.textContent = ""; // Clears any previous error message
        return true; // Indicates validation success
    }
}
//-------------------------------------------------------------------------------------
//validate the last name 
function validateLastName() {
    var input = document.getElementById("lastname");
    var errorSpan = document.getElementById("name-error-last");
    var regex = /^[a-zA-Z\s]+$/;  // Allows alphabetic characters and spaces

    if (input.value === "") {
        errorSpan.textContent = ""; // Clears any previous error message
        return true; // Empty input is considered valid with no error message
    }

    if (input.value.trim() === "") {
        errorSpan.textContent = "Only alphabetic characters and space are allowed.";
        errorSpan.style.color = "red";
        return false; // Indicates validation failure for spaces only
    }

    if (!input.value.match(regex)) {
        errorSpan.textContent = "Only alphabetic characters and space are allowed.";
        errorSpan.style.color = "red";
        return false; // Indicates validation failure for invalid characters
    } else {
        errorSpan.textContent = ""; // Clears any previous error message
        return true; // Indicates validation success
    }
}

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