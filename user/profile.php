<?php
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

	//$formatted_bod = date("d-m-Y", strtotime($bod));

	// Output the formatted birthday
	//echo $formatted_bod; // Output: 09/03/2024

// Retrieve user's address
$customerId = $_SESSION["customer_id"] ?? null;
if ($customerId) {
    $query2 = "SELECT * FROM `address` WHERE C_ID = ? AND Address_status = 1";
    $stmt2 = mysqli_prepare($con, $query2);
    mysqli_stmt_bind_param($stmt2, "i", $customerId);
    mysqli_stmt_execute($stmt2);
    $result2 = mysqli_stmt_get_result($stmt2);
    if (!$stmt2) {
        // Handle the error
        die("Error in retrieving address: " . mysqli_error($con));
    }
}

    if ($_SERVER["REQUEST_METHOD"] == "POST") 
	{
        if (isset($_POST['editFName'], $_POST['editLName'], $_POST['editContact'])) {
            $editFirstName = $_POST['editFName'];
            $editLastName = $_POST['editLName'];
            $editContact = "60".$_POST['editContact'];
    
            $currectuser = $_SESSION["customer_id"];
            $sql = "UPDATE `customer` SET C_Firstname = ?, C_Lastname = ?, C_ContactNumber = ? WHERE C_ID = ?";
    
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "sssi", $editFirstName, $editLastName, $editContact, $currectuser);
    
            $result = mysqli_stmt_execute($stmt);
    
            if ($result) {
				echo "<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
				<script type='text/javascript'>
				document.addEventListener('DOMContentLoaded', function() {
                      swal({
                          icon: 'success',
                          title: 'Success',
                          text: 'Update Successfully'
                          }).then(function() {
                              window.location.href = 'profile.php';
                          });
						});
                  </script>";
            } else {
                echo "Error updating profile: " . mysqli_error($con);
            }
    
            mysqli_stmt_close($stmt);
        } elseif (isset($_POST['oldpass'])) {
            $pass1 = $_POST['oldpass'];
            $newpass = $_POST['newpass'];
            $newpass = password_hash($newpass, PASSWORD_DEFAULT);
            $newcfpass = $_POST['newcfpass'];
    
            $currectuser = $_SESSION["customer_id"];
            $sql = "SELECT C_PW FROM `customer` WHERE C_ID = ?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "i", $currectuser);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $stored_password);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
    
            if (password_verify($pass1, $stored_password)) {
                $sql1 = "UPDATE `customer` SET C_PW = ? WHERE C_ID = ?";
                $stmt1 = mysqli_prepare($con, $sql1);
                mysqli_stmt_bind_param($stmt1, "si", $newpass, $currectuser);
                $result1 = mysqli_stmt_execute($stmt1);
                mysqli_stmt_close($stmt1);
    
                if ($result1) {
                    echo "<script type='text/javascript'>
                              alert('Password updated successfully!');
                              window.location.href = 'profile.php';
                            </script>";
                    exit;
                } else {
                    echo "Error updating password: " . mysqli_error($con);
                }
            } else {
                echo "<script type='text/javascript'>alert('Current password is incorrect, please enter again.');</script>";
            }
        } else {
           // echo "Incomplete form data received.";
        }
    }

	//address form
	// Handle address form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['editAdd1'], $_POST['editAdd2'], $_POST['editCity'])) {
        // Validate and sanitize input data
        $add1 = $_POST['editAdd1'];
        $add2 = $_POST['editAdd2'];
        $city = $_POST['editCity'];
        // Additional fields (e.g., city, state, country) could be validated and sanitized here

        $StateCountry = "Melaka, Malaysia"; // Default value
		$postcode="75450";
		$addressStatus = 1; // Default status value

        $customerId = $_SESSION["customer_id"];
		$sql = "INSERT INTO `address` (Address_1, Address_2, Postcode, City, state_country, C_ID, Address_status) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssi", $add1, $add2, $postcode,$city, $StateCountry, $customerId,$addressStatus);
        $result = mysqli_stmt_execute($stmt);

        if (!$result) {
            // Handle the error
            die("Error saving address: " . mysqli_error($con));
        } else {
			echo "<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
			<script type='text/javascript'>
			document.addEventListener('DOMContentLoaded', function() {
			swal({
			  icon: 'success',
			  title: 'Success',
			  text: 'Address saved successfully',
			}).then(() => {
			  window.location.href = 'profile.php';
			});
		});
		</script>";
  
  
        }
    }
	
}


	if ($stmt2 !== null) {
		mysqli_stmt_close($stmt2);
	}
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
	<title>Profile - Cuppa Joy</title>

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
    <!-- profile -->
	<link rel="stylesheet" href="profile.css">


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
											echo '<li id="historyGo"><a href="history.php">History</a></li>';
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
												echo '<a class="shopping-cart profile-icon" href="profile.php">
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
							<h1>User Profile</h1>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- end breadcrumb section -->
	
		<div class="container">
    <div class="main-body">
        <div class="row gutters-sm">
            <!-- Left Column -->
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-column align-items-center text-center">
                            <img src="assets\img\user.png" alt="Admin" class="rounded-circle" width="160">
                            <div class="mt-3">
                                <h4><?php echo htmlspecialchars($firstname); ?> <?php echo htmlspecialchars($lastname); ?></h4>
                                <hr>
                                <button class="editbtn" id="editbtn" onclick="editform()">Edit Profile</button>
                                <p><a href="change-pswd.php">Click here to change password</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Right Column -->
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Full Name</h6>
                            </div>
                            <div class="col-sm-9 text-secondary">
                                <span id="firstname"><?php echo htmlspecialchars($firstname); ?></span> 
                                <span id="lastname"><?php echo htmlspecialchars($lastname); ?></span>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Email</h6>
                            </div>
                            <div class="col-sm-9 text-secondary">
                                <span id=""><?php echo htmlspecialchars($Email); ?></span>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Phone</h6>
                            </div>
                            <div class="col-sm-9 text-secondary">
                                <span id="contno"><?php echo htmlspecialchars($phno); ?></span>
                            </div>
                        </div>
                        <hr>
                        <h6 class="mb-0">Address</h6>
                        <?php
                        if(mysqli_num_rows($result2) > 0) {
                            while ($row = mysqli_fetch_assoc($result2)) {
                        ?>
                        <div class="row">
                            <div class="col-sm-9 text-secondary">
                                <span><?php echo htmlspecialchars($row['Address_1']); ?>, <?php echo htmlspecialchars($row['Address_2']); ?>,
                                <?php echo htmlspecialchars($row['Postcode']); ?>, <?php echo htmlspecialchars($row['City']); ?>, <?php echo htmlspecialchars($row['state_country']); ?></span>
                                <div class="del" onclick="deleteAddress(<?php echo $row['A_ID']; ?>)">Delete</div>
                                <hr>
                            </div>
                        </div>
                        <?php
                            }
                        } else {
                            echo "<p>No address found. Please enter your address</p>";
                        }
                        ?>
                        <input type="button" id="saveMorebtn" value="Add address" onclick="openAddPopup()">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="overlay"></div>
<section id="editprofile">
        <h2>Edit Profile
            <span class="modal-close" onclick="closePopup()">&times;</span>
        </h2>
        <form id="editForm" name="editForm" method="post" action="profile.php" onsubmit="return submitForm(event)">
            <div>
                <label for="editFName">First Name:</label>
                <input type="text" id="editFName" name="editFName" placeholder="Alphabet allowed only" oninput="validateFirstName()" required>
            </div>
            <span id="name-error-first" class="error-message"></span>

            <div>
                <label for="editLName">Last Name:</label>
                <input type="text" id="editLName" name="editLName" placeholder="Alphabet allowed only" oninput="validateLastName()" required>
            </div>
            <span id="name-error-last" class="error-message"></span>

            <div>
                <label class="labelNum" for="editContact">Contact Number:</label>
                <div class="phoneOnly">
                    <span>+60</span>
                    <input type="text" id="editContact" name="editContact" placeholder="Enter 9-11 digits" onkeyup="validatePhone()" required>
                </div>
                <span id="phone-error" class="error-message"></span>
            </div>

            <input type="submit" id="submitbtn" value="Update">
        </form>
    </section>

	<div id="overlay"></div>
	<!--select address-->
	<section id="select-address" >
        <h2>Save new address
            <span class="modal-close" onclick="closePopup()">&times;</span>

        </h2>
        <form id="addressform" name="addForm" method="post" action="profile.php" onsubmit="return validateForm(event)">
		<div>
			<label for="editAdd1">Address 1:</label>
			<input type="text" id="editAdd1" name="editAdd1" placeholder="Please Enter Address 1" onblur="validateAddress1()" required>
			<span id="address1-error" class="error-message"></span>
		</div>
		<div>
			<label for="editAdd2">Address 2:</label>
			<input type="text" id="editAdd2" name="editAdd2" placeholder="Please Enter Address2" onblur="validateAddress2()" required>
			<span id="address2-error" class="error-message"></span>
		</div>
		<div>
			<label for="editPost">Postcode(No need fill up):</label>
			<input type="text" id="editPost" name="editPost" placeholder="75450 (Default)" readonly>
		</div>
		<div>
			<label for="editCity">City:</label>
			<select id="editCity" name="editCity" required>
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
		<input type="submit" id="submitbtn" value="Save" onclick="saveadd()">
		</form>

    </section>
	<!--end select add-->
    
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
	<!-- profile js -->
	<script src="profile.js"></script>
	
	<script>
    function deleteAddress(addressId) {
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this address",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                // Send AJAX request to delete the address
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            // Reload the page or update the address list as needed
                            location.reload(); // Example: Reload the page after deletion
                        } else {
                            console.error('Error deleting address.');
                        }
                    }
                };
                xhr.open("POST", "delete_address.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.send("A_ID=" + addressId);
            } 
        });
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