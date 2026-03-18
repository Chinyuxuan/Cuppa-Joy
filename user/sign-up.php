<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include("db_connection.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $Firstname = $_POST['C_Firstname'];
    $Lastname = $_POST['C_Lastname'];
    $Phonenumber = "60". $_POST['C_ContactNumber'];
    $Email = $_POST['C_Email'];
    $password = $_POST['C_PW']; // This is the plaintext password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the plaintext password

    // Check if all fields are not empty
    if (!empty($Firstname) && !empty($Lastname) && !empty($Phonenumber) && !empty($Email) && !empty($password)) 
    {
        // Check if email already exists
        $check_query = mysqli_query($con, "SELECT * FROM customer where C_Email ='$Email'");
        $rowCount = mysqli_num_rows($check_query);

        if ($rowCount > 0) {
            echo "
            <script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                swal({
                    title: 'Warning',
                    text: 'User with this email already exists',
                    icon: 'warning',
                    button: 'OK'
                    
                });
            });
            </script>";
            
        } else {
            // Prepare the SQL query excluding C_ID and C_Photo
            $query = "INSERT INTO `customer` (C_Firstname, C_Lastname, C_ContactNumber, C_Email,  C_PW) VALUES (?,  ?, ?, ?, ?)";
            $stmt = mysqli_prepare($con, $query);

            // Check for errors in preparing the statement
            if ($stmt === false) 
            {
                die('Error preparing statement: ' . mysqli_error($con));
            }

            // Bind parameters to the prepared statement
            mysqli_stmt_bind_param($stmt, 'sssss', $Firstname, $Lastname, $Phonenumber, $Email, $hashedPassword); // Use $hashedPassword here

            // Execute the prepared statement
            if (mysqli_stmt_execute($stmt)) 
            {
                // Send email verification
                $otp = rand(100000, 999999);
                $_SESSION['otp'] = $otp;
                $_SESSION['mail'] = $Email;
                require "Mail/phpmailer/PHPMailerAutoload.php";
                $mail = new PHPMailer;

                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->Port = 587;
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = 'tls';

                $mail->Username = 'cuppajoy88@gmail.com'; // Change this to your email
                $mail->Password = 'imjo jrya kyki hgjt'; // Change this to your email password

                $mail->setFrom('cuppajoy88@gmail.com', 'CUPPA JOY - OTP Verification');
                $mail->addAddress($Email);

                $mail->isHTML(true);
                $mail->Subject = "Your verify code";
                $mail->Body = "
                <p>Dear user,</p>
                <p>Please use this OTP code to verify your account to Sign in into Cuppa Joy.</p>
                
                <h3>Your verify OTP code is <span class='otp'>$otp</span></h3>
                <br><br>
                
                <p>Thank you</p>";
                    
                   
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );
                if (!$mail->send()) 
                {
                    //if mail is not sent
                echo "
                <script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    swal({
                        title: 'Register Failed, Invalid Email',
                        icon: 'error',
                        button: 'OK'
                    });
                });
                </script>";

                } 
                else 
                {
                    //if mail is sent
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
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        swal({
                            title: 'Register Successfully',
                            text: 'OTP sent to youe email, you can check in your mail box',
                            icon: 'success',
                            button: 'OK'
                        }).then(function() {
                            window.location.href = 'verification.php';
                        });
                    });
                    </script>";
                    
                }
                exit; // Exit after successful registration
            } 
            else 
            {
                die('Error executing statement: ' . mysqli_stmt_error($stmt));
            }
        }//end register a customer account
    } else {
        $missingFields = [];
        if (empty($Firstname)) {
            $missingFields[] = "Firstname";
        }
        if (empty($Lastname)) {
            $missingFields[] = "Lastname";
        }
        if (empty($Phonenumber)) {
            $missingFields[] = "Phone number";
        }
        if (empty($Email)) {
            $missingFields[] = "Email";
        }
        if (empty($password)) {
            $missingFields[] = "Password";
        }
       
        $missingFieldsString = implode(", ", $missingFields);
        echo '<script>console.log("Debug: Checking missing fields");</script>';

        echo "
        <script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            swal({
                title: 'Validation Error',
                text: 'Please enter valid information for the following field(s): " . $missingFieldsString . "',
                icon: 'error',
                button: 'OK'
            });
        });
        </script>";
        
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Cuppa Joy</title>
    <link rel="stylesheet" type="text/css" href="sign-up2.css">
    <link rel="icon" href="assets/img/smile-black.png" type="image/icon">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
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

<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
</head>
<body>
    <header>
        <div id="top header">
            <div id="logo" class="tooltip">
                <a href="index.php">
                <img src="assets/img/full-white.png" alt="Cuppa Joy logo">
                </a>
                <!--<span class="tooltiptext">Go back to main page</span>-->
            </div>
        </div>
    </header>
    
    <div class="container">
        <div class="left">
            <p>Welcome to Cuppa Joy</p>
            <p>If you already have an account, please sign in.</p>
            <a href="sign-in.php"><button id="signinbtn">Sign In</button></a>
            <br>
            <img src="assets/img/sign-up.png" alt="" class="image">
        </div>

        <div class="right">
            <h1>Sign Up</h1><br />
            <form  id="myForm" method="post">
                <div class="input-field">
                    <i class="fas fa-user"></i>
                    <input type="text" id="firstname" name="C_Firstname" placeholder="First name" oninput="validateFirstName()" required>
                </div>
                <span id="name-error-first" class="error-message"></span>
                
                <div class="input-field">
                    <i class="fas fa-"></i>
                    <input type="text" id="lastname" name="C_Lastname" placeholder="Last name" oninput="validateLastName()" required>
                </div>
                <span id="name-error-last" class="error-message"></span>

                <div class="input-field">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="useremail" name="C_Email" placeholder="Email" spellcheck="false" onkeyup=" validateEmail()" required>
                </div>
                <span id="email-error"></span>

                <div class="input-field">
                    <i class="fas fa-phone"></i>
                        <span>+60</span>
                    <input type="text" id="phno" name="C_ContactNumber" value="" placeholder=" xxxxxxxxx (9-11 digit)" onkeyup=" validatePhone()" required>
                </div>
                <span id="phone-error"></span>

                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input type="password" class="input-pw" id="pw" name="C_PW" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                    title="Must contain at least one number and one uppercase and lowercase letter, 
                    and at least 8 characters" placeholder="Password" required>
                    <i class='bx bx-hide eye-icon1'></i>
                </div>

                <div id="message">
                    <h3>Password must contain the following:</h3>
                    <p id="letter" class="invalid">A <b>lowercase</b> letter</p>
                    <p id="capital" class="invalid">A <b>capital (uppercase)</b> letter</p>
                    <p id="number" class="invalid">A <b>number</b></p>
                    <p id="length" class="invalid">Min <b>8 characters</b></p>
                </div>
                <!--msg to confirm pw is correct with validation -->

                <div class="input-field">
                    <i class="fas fa-"></i>
                    <input type="password" class="input-pw" id="confirmpw" name="C_ConfirmPW" placeholder="Confirm Password" required>
                    <i class='bx bx-hide eye-icon2'></i>
                </div>

                <span id="passwordMatchStatus"></span>
                <br>
      
                <input type="submit" id="submitbtn" value="Sign Up"  onclick="submitForm(event)">
            </form>
        </div>
    </div>

    <script src="sing-u.js"></script>

<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
<script>
    //-------------------------------------------------------------------------------
    //triger the eye icon
    const eyeIcon = document.querySelector(".eye-icon1");
    const pwField = document.getElementById("pw");
    const eyeIcon2 = document.querySelector(".eye-icon2");
    const pwField2 = document.getElementById("confirmpw");
    const passwordMatchStatus = document.getElementById("passwordMatchStatus");

    eyeIcon.addEventListener("click", () => {
        togglePasswordVisibility(pwField, eyeIcon);
    });

    eyeIcon2.addEventListener("click", () => {
        togglePasswordVisibility(pwField2, eyeIcon2);
    });

    // Function to toggle password visibility
    function togglePasswordVisibility(passwordField, eyeIcon) {
        if (passwordField.type === "password") {
            passwordField.type = "text";
            eyeIcon.classList.replace("bx-hide", "bx-show");
        } else {
            passwordField.type = "password";
            eyeIcon.classList.replace("bx-show", "bx-hide");
        }
    }
//-----------------------------------------------------------------------------------
    // Check password match or not
    pwField.addEventListener("input", checkPasswordsMatch);
    pwField2.addEventListener("input", checkPasswordsMatch);

    function checkPasswordsMatch() {
        const pwValue = pwField.value;
        const pw2Value = pwField2.value;

        if (pwValue === pw2Value) {
            passwordMatchStatus.textContent = "Passwords match";
            passwordMatchStatus.style.color = "green";
        } else {
            passwordMatchStatus.textContent = "Passwords do not match";
            passwordMatchStatus.style.color = "red";
        }
    }

    document.getElementById("myForm").addEventListener("submit", function(event) {
        // Call verifyPassword function to validate password
        if (!verifyPassword() || !checkPasswordsMatch()) {
            event.preventDefault(); // Prevent form submission if password validation fails
        }
    });
//------------------------------------------------------------------------------------------------------
    //password validation

    var myInput = document.getElementById("pw");
    var letter = document.getElementById("letter");
    var capital = document.getElementById("capital");
    var number = document.getElementById("number");
    var length = document.getElementById("length");

    // When the user clicks on the password field, show the message box
    myInput.onfocus = function() {
        document.getElementById("message").style.display = "block";
    }

    // When the user clicks outside of the password field, hide the message box
    myInput.onblur = function() {
        document.getElementById("message").style.display = "none";
    }

    // When the user starts to type something inside the password field
    myInput.onkeyup = function() {
        // Validate lowercase letters
        var lowerCaseLetters = /[a-z]/g;
        if (myInput.value.match(lowerCaseLetters)) {
            letter.classList.remove("invalid");
            letter.classList.add("valid");
        } else {
            letter.classList.remove("valid");
            letter.classList.add("invalid");
        }

        // Validate capital letters
        var upperCaseLetters = /[A-Z]/g;
        if (myInput.value.match(upperCaseLetters)) {
            capital.classList.remove("invalid");
            capital.classList.add("valid");
        } else {
            capital.classList.remove("valid");
            capital.classList.add("invalid");
        }

        // Validate numbers
        var numbers = /[0-9]/g;
        if (myInput.value.match(numbers)) {
            number.classList.remove("invalid");
            number.classList.add("valid");
        } else {
            number.classList.remove("valid");
            number.classList.add("invalid");
        }

        // Validate length
        if (myInput.value.length >= 8) { // Adjusted condition for length
            length.classList.remove("invalid");
            length.classList.add("valid");
            length.style.color = ""; // Reset color to default
        } else {
            length.classList.remove("valid");
            length.classList.add("invalid");
            length.style.color = "red"; // Set color to red when length exceeds 15 characters
        }
    }
//----------------------------------------------------------------------------------------------
//Check all validation before submit the form
function submitForm(event) {
        // Rest of your function remains unchanged
        const pwValue = pwField.value;
        const pw2Value = pwField2.value;

      // Validate both names using the specific functions
    var validFirstName = validateFirstName();
    var validLastName = validateLastName();

    // If either name validation fails, show an error message and prevent form submission
    if (!validFirstName ) {
		event.preventDefault();
        swal({
            icon: 'error',
            title: 'Invalid Name',
            text: 'Please ensure that firstname are valid.'
        });
        return false; // Return false to indicate form should not submit
    }

	    // If either name validation fails, show an error message and prevent form submission
    if (!validLastName) {
		event.preventDefault();
        swal({
            icon: 'error',
            title: 'Invalid Name',
            text: 'Please ensure that lastname are valid.'
        });
        return false; // Return false to indicate form should not submit
    }

             // Check if email is valid
        if (!validateEmail()) {
            // If email is not valid, prevent form submission and display an error message
            event.preventDefault();
            swal({
                icon: 'error',
                title: 'Invalid Email',
                text: 'Please make sure your email is valid.'
            });
            return; // Exit the function without submitting the form
        }

        // Check if phone is valid
        if (!validatePhone()) {
            // If email is not valid, prevent form submission and display an error message
            event.preventDefault();
            swal({
                icon: 'error',
                title: 'Invalid Phone',
                text: 'Please make sure your phone number is valid.'
            });
            return; // Exit the function without submitting the form
        }

        // Check if passwords match
        if (pwValue !== pw2Value) {
            // If passwords don't match, prevent form submission and display an error message
            event.preventDefault();
            swal({
                icon: 'error',
                title: 'Password Not Match',
                text: 'Passwords do not match. Please make sure your passwords match.'
            });
            return; // Exit the function without submitting the form
        }

    
    }
//----------------------------------------------------------------------------------------
    //email validation
    var email = document.getElementById("useremail");
    var email_error = document.getElementById("email-error")

    function validateEmail() {

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

        email_error.innerHTML = "";
        return true;
    }
//---------------------------------------------------------------------------------------
    // Validate phone number
    var phone = document.getElementById("phno");
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
//---------------------------------------------------------------------------------
//validate firstname
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
        return false; // Indicates validation failure
    } else {
        errorSpan.textContent = ""; // Clears any previous error message
        return true; // Indicates validation success
    }
}
//---------------------------------------------------------------------------------------------
//validate lastname
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
        return false;
    } else {
        errorSpan.textContent = "";
        return true;
    }
}


</script>

</body>
</html>
