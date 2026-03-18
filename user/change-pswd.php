<?php
session_start();
include("db_connection.php");

if (!isset($_SESSION["customer_id"])) {
    header("location:sign-in.php");
    exit;
}

$currectuser = $_SESSION["customer_id"];
$sql = "SELECT * FROM `customer` WHERE C_ID = '$currectuser'";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['oldpass'])) {
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
				echo "
				<style>
					.swal-button {
						background-color: #F28123 !important; /* Orange color */
						color: #fff !important; /* White text color */
						border: none !important; /* No border */
						text-align: center !important; /* Center text alignment */
						margin: auto !important; /* Center horizontally */
						display: block !important; /* Ensure it's displayed as a block element */
						border-radius: 10px !important; /* Add some border radius */
						padding: 10px 20px !important; /* Add padding */
						cursor: pointer !important; /* Add cursor pointer */
						font-family: 'Open Sans', sans-serif !important;
					}

					  .swal-title, .swal-text {
                            font-family: 'Open Sans', sans-serif !important; /* Use Open Sans font */
                        }
				</style>
				<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
				<script type='text/javascript'>
				document.addEventListener('DOMContentLoaded', function() {
					swal({
						icon: 'success',
						title: 'Success',
						text: 'Password updated successfully',
						buttons: {
							confirm: {
								className: 'swal-button'
							}
						}
					}).then(function() {
						window.location.href = 'profile.php';
					});
				});
				</script>";
				exit;
            } else {
                echo "Error updating password: " . mysqli_error($con);
            }
        } else {
			echo "<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
			<script type='text/javascript'>
			document.addEventListener('DOMContentLoaded', function() {
			swal({
				icon: 'error',
				title: 'Incorrect Password',
				text: 'Old password is incorrect, please enter again.',
				});
			});
		</script>";
        }
    } else {
        echo "Incomplete form data received.";
    }
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
	<title>Change Password - Cuppa Joy</title>

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
	<link rel="stylesheet" href="change-pw.css">
	<link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
	<style>
        /* The message box is shown when the user clicks on the password field */
        #message {
        display:none;
        color: #000;
        position: relative;
        padding: 0px;
        margin-top: 0px;
        }

        #message p {
        font-size: 18px;
		margin-top:-10px
        }

        /* Add a green text color and a checkmark when the requirements are right */
        .valid {
        color: green;
        }

        .valid:before {
        position: relative;
        left: -10px;
        content: "✔";
        }

        /* Add a red text color and an "x" when the requirements are wrong */
        .invalid {
        color: red;
        }

        .invalid:before {
        position: relative;
        left: -10px;
        content: "✖";
        }
</style>

</head>
<body>
	
	<!--PreLoader-->
    <div class="loader">
        <div class="loader-inner">
            <div class="circle"></div>
        </div>
    </div>
    <!--PreLoader Ends-->
	

	<a href="profile.php" class="back"> <i class="fas fa-arrow-left"></i> Back to Profile</a>

	
	
		<section id="editpassword">
        <h2>Change Password
            <span class="modal-close" onclick="closePopup()"></span>
        </h2>
        <form action="" id="passwordfrm" name="passwordfrm" method="POST">
            <label for="oldpass">Enter current password</label>
            <input type="password" name="oldpass" id="oldpass" required>
			<i class='bx bx-hide eye-icon0'></i>
            
            <label for="newpass">Enter new password</label>
            <input type="password" name="newpass" id="newpass" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                    title="Must contain at least one number and one uppercase and lowercase letter, 
                    and at least 8 characters" required><i class='bx bx-hide eye-icon1'></i>
			<div id="message">
                    
                    <p id="letter" class="invalid">A <b>lowercase</b> letter</p>
                    <p id="capital" class="invalid">A <b>capital (uppercase)</b> letter</p>
                    <p id="number" class="invalid">A <b>number</b></p>
                    <p id="length" class="invalid">Min <b>8 characters</b></p>
            </div>
            <label for="newcfpass">Confirm new password</label>
            <input type="password" name="newcfpass" id="newcfpass" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                    title="Must contain at least one number and one uppercase and lowercase letter, 
                    and at least 8 characters" required><i class='bx bx-hide eye-icon2'></i>
			<hr/>
            <span id="passwordMatchStatus" class="status-message"></span>
			<input type="submit" id="passbtn" name="passbtn" value="Update" onclick="submitForm(event)">

        </form>
    </section>
	
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
	<script src="change-pswd.js"></script>
	<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
	</body>
</html>