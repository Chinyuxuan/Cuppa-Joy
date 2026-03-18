<?php
session_start();
include("db_connection.php");

if(isset($_GET['logout'])) {
    //echo "Successfully logged out. Your session has been destroyed.";
}

// Check if the form is submitted and if the required POST values exist
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['C_Email']) && isset($_POST['C_PW'])) {
    $Email = $_POST['C_Email'];
    $password = $_POST['C_PW'];

    if (!empty($Email) && !empty($password)) {
        $query = "SELECT * FROM `customer` WHERE C_Email = ? LIMIT 1";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 's', $Email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            $hashedPassword = $user_data['C_PW'];
            $status = $user_data['C_Status'];

            // Compare plaintext password with hashed password
            if (password_verify($password, $hashedPassword)) {
                if ($status == 1) {
                    //is customer is verified
                    $_SESSION['customer_id'] = $user_data['C_ID'];
                    echo "<script type='text/javascript'>window.location.href = 'index.php';</script>";
                    exit();
                } else {
                   // Unverified email
                   echo "<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
                   <script>
                       document.addEventListener('DOMContentLoaded', function() {
                           swal({
                               title: 'Warning',
                               text: 'Please verify your account before logging in.Wait for sending OTP code to your email. Thank you patient',
                               icon: 'warning',
                               button: 'OK'
                           }).then(function() {
                            // Trigger AJAX request to send verification email
                            $.ajax({
                                type: 'POST',
                                url: 'not_login_verify.php',
                                data: { email: '$Email' },
                                success: function(response) {
                                    swal({
                                        title: 'Email Sent',
                                        text: 'OTP sent to youe email, you can check in your mail box.',
                                        icon: 'success',
                                        button: 'OK'
                                    }).then(function() {
                                        // Redirect to the verification page
                                        window.location.href = 'verification.php';
                                    });
                                },
                                error: function() {
                                    swal({
                                        title: 'Error',
                                        text: 'Failed to send verification email. Please try again later.',
                                        icon: 'error',
                                        button: 'OK'
                                    });
                                }
                            });
                            
                           });
                       });
                   </script>";
               }
            } else {
                //if password is wrong
                echo "
                <script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        swal({
                            title: 'Password incorrect.',
                            text:'Please enter your passowrd again.',
                            icon: 'error',
                            button: 'OK'
                        });
                    });
                </script>";
                
            }
        } else {
            echo "
            <script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    swal({
                        title: 'Email incorrect.',
                        text: 'Please enter your email again. Please Sign Up if you don\'t have an account.',
                        icon: 'error',
                        button: 'OK'
                    });
                });
            </script>";

        }
    } else {
        echo "
        <script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    swal({
                        title: 'Email and password are required.',
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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="google-signin-client_id" content="837431288932-r0ks0osa3dfqqjqjau24gfjjo3uvceoc.apps.googleusercontent.com">
    <title>Sign In - Cuppa Joy</title>
    <link rel="stylesheet" type="text/css" href="sign-in.css">
    <link rel="icon" href="assets/img/smile-black.png" type="image/icon">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
    <script src="https://apis.google.com/js/platform.js" async defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            <p>If you don't have an account, please sign up to create an account.</p>
            <a href="sign-up.php"><button id="signinbtn">Sign Up</button></a>
            <br>
            <img src="assets/img/sign-in.png" alt="" class="image">
        </div>
        <div class="right">
            <h1>Sign In</h1><br />
            <form  id="myForm"  method="post">
           
                <div class="input-field">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="useremail" name="C_Email" placeholder="Email" required>
                </div>
               
                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input type="password" class="input-pw" id="pw" name="C_PW" placeholder="Password" required>
                    <i class='bx bx-hide eye-icon1'></i>
                </div>

                
                <input type="submit" id="submitbtn" value="Sign In">
                <p><a href="recover_psw.php">Click here if you forgot password</a></p>
            </form>
            <!-- <h2>OR</h2>
            <div class="g-signin2" data-onsuccess="onSignIn" data-prompt="select_account"></div> -->
        </div>
    </div>
    <script src="sign-in.js"></script>
   <!-- Load the Google API platform script -->
<script src="https://apis.google.com/js/platform.js" async defer></script>

    <script>

    </script>
</body>

</html>