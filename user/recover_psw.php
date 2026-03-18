
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

    <link rel="icon" href="assets/img/smile-black.png" type="image/icon">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
    <title>Recover Password - Cuppa Joy</title>
</head>
<body>
<a href="sign-in.php" class="back"> <i class="fas fa-arrow-left"></i> Back to Sign In</a>
<!--<nav class="navbar navbar-expand-lg navbar-light navbar-laravel">
    <div class="container">
        <a class="navbar-brand" href="#">User Password Recover</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

    </div>
</nav>-->

         <div class="form-group">
           
         <form action="#" method="POST" name="recover_psw" onsubmit="return submitForm(event)">


                <h2>Recover Password
            <span class="modal-close" ></span>
            </h2>
                    <div class="row">
                        <label for="email_address" class="label">Enter E-Mail Address</label>
                        <br>
                        <div class="col-md-6">
                            <input type="text" id="email_address" class="form-control" name="email" required autofocus onkeyup=" validateEmail()">
                            
                        </div>
                        <span id="email-error"></span>
                        <input type="submit" value="Recover" name="recover">    
                    </div>
                       
                </form>
            </div>
        </div>
  
<!--email validation-->
<script>
function validateEmail() {
  var email = document.getElementById("email_address");
  var email_error = document.getElementById("email-error");

  if (!email.value.match(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/)) {
    email_error.innerHTML = "Please make sure your email is valid";
    email_error.style.color = "red";
    return false;
  }

  email_error.innerHTML = "";
  return true;
}

function submitForm(event) {
  if (!validateEmail()) {
    // If email is not valid, prevent form submission and display an error message
    event.preventDefault();
    console.log("Invalid email"); // Debugging statement
    
    swal({
        icon: 'error',
        title: 'Invalid Email',
        text: 'Please make sure your email is valid.'
    });


    return false; // Exit the function without submitting the form
  }
}
</script>

</body>
</html>


<?php 
session_start();
include("db_connection.php");

if(isset($_POST["recover"])){
    $email = $_POST["email"];

    // Set session variables to empty strings by default
    $_SESSION['token'] = '';
    $_SESSION['email'] = '';

    $sql = mysqli_query($con, "SELECT * FROM customer WHERE C_Email='$email'");
    $query = mysqli_num_rows($sql);
    $fetch = mysqli_fetch_assoc($sql);

    if(mysqli_num_rows($sql) <= 0){
        ?>
 <script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            swal({
                icon: 'error',
                title: 'No User Found',
                text: 'Sorry, no emails exist.'
            });
        });
    </script>
        <?php
    } else {
        // Generate token by binaryhexa 
        $token = bin2hex(random_bytes(50));

        $_SESSION['token'] = $token;
        $_SESSION['email'] = $email;

        // Debugging: Output session variables
      //  echo "Token: " . $_SESSION['token'] . "<br>";
        //echo "Email: " . $_SESSION['email'] . "<br>";

        require "Mail/phpmailer/PHPMailerAutoload.php";
        $mail = new PHPMailer;

        $mail->isSMTP();
        $mail->Host='smtp.gmail.com';
        $mail->Port=587;
        $mail->SMTPAuth=true;
        $mail->SMTPSecure='tls';

        // h-hotel account
        $mail->Username='cuppajoy88@gmail.com';
        $mail->Password='imjo jrya kyki hgjt';

        // Send by h-hotel email
        $mail->setFrom('cuppajoy88@gmail.com', 'CUPPA JOY - Password Reset');
        // Get email from input
        $mail->addAddress($_POST["email"]);

        // HTML body
        $mail->isHTML(true);
        $mail->Subject="Recover your password";
        $mail->Body="<b>Dear User,</b>
        <h3>We received a request to reset your password.</h3>
        <p>Kindly click the below link to reset your password:</p>
        <a href='http://localhost/fyp/user/reset_psw.php'>Reset Password</a>
        <br><br>
        <p>Thank you,</p>";
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        if(!$mail->send()){
            ?>
             <script>
            <script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
             
                 document.addEventListener('DOMContentLoaded', function() {
                   
    swal({
        icon: 'error',
        title: 'Wrong Email',
        text: '<?php echo "Invalid Email"; ?>'
    });
});
</script>

            </script>
            <?php
        } else {
            ?>
            <script>
                window.location.replace("notification.html");
            </script>
            <?php
        }
    }
}
?>
