<?php
session_start();
include("db_connection.php");

if (isset($_POST['email'])) {
    $Email = $_POST['email'];
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
    $mail->Password = 'imjojryakykiHgjt'; // Change this to your email password

    $mail->setFrom('cuppajoy88@gmail.com', 'CUPPA JOY - OTP Verification');
    $mail->addAddress($Email);

    $mail->isHTML(true);
    $mail->Subject = "Your verification code";
    $mail->Body = "
    <p>Dear user,</p>
    <p>Please use this OTP code to verify your account to sign in to Cuppa Joy.</p>
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
    if (!$mail->send()) {
        echo "<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>";
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                swal({
                    title: 'Register Failed',
                    text: 'Invalid Email',
                    icon: 'error',
                    button: 'OK'
                });
            });
        </script>";
    } else {
        echo "<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>";
        echo "<script>
       
            console.log('DOMContentLoaded event fired');
            swal({
                title: 'Register Successfully',
                text: 'OTP sent to $Email',
                icon: 'success',
                button: 'OK'
            }).then(function() {
                console.log('SweetAlert resolved');
                window.location.href = 'verification.php';
            });
     
        
        </script>";
    }
    exit(); // Ensure no further code is executed after sending the response
}
?>


