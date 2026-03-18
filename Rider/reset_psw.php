<?php 
session_start();

if (isset($_POST["password"])) {
    include('../user/db_connection.php');
    $psw = $_POST["password"];
    
    if (isset($_GET['token']) && isset($_GET['email'])) {
        $token = $_GET['token'];
        $email = $_GET['email'];
        
        $hash = password_hash($psw, PASSWORD_DEFAULT);
        
        $sql = mysqli_query($con, "SELECT * FROM `rider` WHERE R_Email='$email'");
        $query = mysqli_num_rows($sql);
        $fetch = mysqli_fetch_assoc($sql);
        
        if ($query > 0) {
            $password = $fetch['R_PW'];
            $new_pass = $hash;
            if (!password_verify($psw, $password)) {
                mysqli_query($con, "UPDATE `rider` SET R_PW='$new_pass' WHERE R_Email='$email'");
                echo "<script>alert('Your password has been successfully reset.'); window.location.replace('pages-login.php');</script>";
            }else{
                echo "<script>alert('Your new password cannot same as the old password.'); window.location.replace('pages-login.php');</script>";
            }
        } else {
            echo "<script>alert('Email not found in database');</script>";
        }
    } else {
        echo "<script>alert('Token is not set!');</script>";
        exit;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Favicons -->
    <link href="assets/image/smile-black.png" rel="icon">
    <link href="assets/image/smile-black.png" rel="apple-touch-icon">

    <!-- Required meta tags -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Responsive Bootstrap4 Shop Template, Created by Imran Hossain from https://imransdesign.com/">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans|Nunito|Poppins" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet">
    <title>Reset password - Cuppa Joy</title>
    <style>
        #message { display:none; color: #000; position: relative; margin-left: 10px; font-family: "Poppins", sans-serif; font-size: 14px; }
        #message p { font-size: 18px; margin-top: -10px; }
        .valid { color: green; }
        .invalid { color: red; }
    </style>
</head>
<body>
    <main>
        <div class="container">
            <section class="section min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container resetpw">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
                            <div class="d-flex justify-content-center py-4">
                                <a href="index.php" class="logo d-flex align-items-center w-auto">
                                <span class="d-none d-lg-block">Cuppa&nbsp;</span>
                                <img src="assets/image/smile-black.png" alt="">
                                <span class="d-none d-lg-block">Joy</span>
                                </a>
                            </div>    
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="pt-4 pb-2">
                                        <h5 class="card-title text-center pb-0 fs-4">Reset Password</h5>
                                    </div>
                                    <form class="row g-3 needs-validation" novalidate method="post" onsubmit="return validateForm()">
                                        <div class="col-12">
                                            <label for="newpass" class="form-label">New Password</label>
                                            <div class="input-group has-validation">
                                                <input type="password" name="password" class="form-control" id="newpass" required>
                                                <div class="invalid-feedback">Please enter your new password.</div>
                                            </div>
                                        </div>
                                        <div id="message">
                                            <p id="letter" class="invalid"><i class="bi bi-info-circle me-1"></i>A <b>lowercase</b> letter</p>
                                            <p id="capital" class="invalid"><i class="bi bi-info-circle me-1"></i>A <b>capital (uppercase)</b> letter</p>
                                            <p id="number" class="invalid"><i class="bi bi-info-circle me-1"></i>A <b>number</b></p>
                                            <p id="length" class="invalid"><i class="bi bi-info-circle me-1"></i>Min <b>8 characters</b></p>
                                        </div>
                                        <div class="col-12">
                                            <label for="newcfpass" class="form-label">Confirm new password</label>
                                            <input type="password" name="newcfpass" class="form-control" id="newcfpass" required>
                                            <div class="invalid-feedback">Please enter your password again.</div>
                                        </div>
                                        <div>
                                            <span id="passwordMatchStatus" class="status-message"></span>
                                        </div>
                                        <div class="col-12">
                                            <input class="btn btn-success w-100" type="submit" value="Submit" onclick="submitForm(event)">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script>
        const pwField = document.getElementById("newpass");
        const pwField2 = document.getElementById("newcfpass");
        const message = document.getElementById("message");
        const letter = document.getElementById("letter");
        const capital = document.getElementById("capital");
        const number = document.getElementById("number");
        const length = document.getElementById("length");

        pwField.onfocus = function() {
            message.style.display = "block";
        };

        pwField.onblur = function() {
            message.style.display = "none";
        };

        pwField.onkeyup = function() {
            const lowerCaseLetters = /[a-z]/g;
            if (pwField.value.match(lowerCaseLetters)) {
                letter.classList.remove("invalid");
                letter.classList.add("valid");
            } else {
                letter.classList.remove("valid");
                letter.classList.add("invalid");
            }

            const upperCaseLetters = /[A-Z]/g;
            if (pwField.value.match(upperCaseLetters)) {
                capital.classList.remove("invalid");
                capital.classList.add("valid");
            } else {
                capital.classList.remove("valid");
                capital.classList.add("invalid");
            }

            const numbers = /[0-9]/g;
            if (pwField.value.match(numbers)) {
                number.classList.remove("invalid");
                number.classList.add("valid");
            } else {
                number.classList.remove("valid");
                number.classList.add("invalid");
            }

            if (pwField.value.length >= 8) {
                length.classList.remove("invalid");
                length.classList.add("valid");
            } else {
                length.classList.remove("valid");
                length.classList.add("invalid");
            }
        };

        $(document).ready(function() {
            function submitForm(event) {
                // Check if any of the validation indicators contain the 'invalid' class
                if ($('#letter').hasClass('invalid') || 
                    $('#capital').hasClass('invalid') || 
                    $('#number').hasClass('invalid') || 
                    $('#length').hasClass('invalid')) {
                    alert('Please enter password correctly.');
                    event.preventDefault(); // Prevent form submission
                    return false;
                }
                const pwValue = pwField.value;
                const pw2Value = pwField2.value;

                // Check if passwords match
                if (pwValue !== pw2Value) {
                    event.preventDefault();
                    console.log("Passwords do not match");
                    alert("Passwords do not match. Please make sure your passwords match.");
                    return false; // Exit the function without submitting the form
                }
            }
            // Attach the submitForm function to the form submit event
            $('form').on('submit', submitForm);
        });
    </script>
</body>
</html>
