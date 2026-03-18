
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="assets/css/all.min.css">
    <link rel="stylesheet" href="forgot.css">

    <link rel="icon" href="assets/img/smile-black.png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">

    <title>Verification - Cuppa Joy</title>
</head>
<body>
<a href="sign-in.php" class="back"> <i class="fas fa-arrow-left"></i> Back to Sign In</a>

<div class="form-group">
           
                <form  action="#" method="POST" name="recover_psw">
                <h2>Verification password
            <span class="modal-close" ></span>
            </h2>
                    <div class="row">
                        <label for="email_address" class="label">Enter OTP Code</label>
                        <br>
                        <div class="col-md-6">
                            <input type="text" id="otp" class="form-control" name="otp_code" required autofocus>
                        </div>
                        <input type="submit" value="Verify" name="verify">
                    </div>
                       
                </form>
            </div>
        </div>
  
</body>
</html>
<?php 
session_start();
include('db_connection.php');

if(isset($_POST["verify"])){
    $otp = $_SESSION['otp'];
    $email = $_SESSION['mail']; // Fetch the email from session
    $otp_code = $_POST['otp_code'];

    if($otp != $otp_code){
        echo "<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                swal({
                    icon: 'error',
                    title:'Wrong OTP',
                    text: 'Invalid OTP code. Please enter again',
                });
            });
        </script>";
    } else {
        $updateQuery = "UPDATE customer SET C_Status = 1 WHERE C_Email = '$email'";
        if(mysqli_query($con, $updateQuery)) {
            // Update successful
            echo "<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    swal({
                        icon: 'success',
                        title:'Success',
                        text: 'Verify account done, you may sign in now',
                    }).then(() => {
                        window.location.replace('sign-in.php');
                    });
                });
            </script>";
        } else {
            // Update failed, display error message
            echo "Error updating record: " . mysqli_error($con);
        }
    }
}
?>
