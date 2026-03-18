<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Pages / Register - NiceAdmin Bootstrap Template</title>
  <meta content="" name="description">
  <meta content="" name="keywords">


  <!-- Favicons -->
  <link href="assets/image/smile-black.png" rel="icon">
  <link href="assets/image/smile-black.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

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

  <!------ Include the above in your HEAD tag ---------->
  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

  <title>Admin Recover Password - Cuppa Joy</title>
</head>
<body>
<main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

              <!-- <div class="d-flex justify-content-center py-4">
                <a href="index.html" class="logo d-flex align-items-center w-auto">
                  <span class="d-none d-lg-block">Cuppa&nbsp;</span>
                  <img src="assets/image/smile-black.png" alt="">
                  <span class="d-none d-lg-block">Joy</span>
                </a>
              </div>End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Recover Password</h5>
                    <p class="text-center small">Enter your email to make sure it is your action.</p>
                  </div>

                  <form class="row g-3 needs-validation" novalidate method="post">

                    <div class="col-12">
                      <label for="yourUsername" class="form-label">Email Address</label>
                      <div class="input-group has-validation">
                        <!-- <span class="input-group-text" id="inputGroupPrepend">@</span> -->
                        <input type="email" name="email" class="form-control" id="yourUsername" required>
                        <div class="invalid-feedback">Please enter your Email.</div>
                      </div>
                    </div>

                    <div class="">
                      <input class="btn btn-primary w-100" type="submit" name="recover" value="Submit">
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
</body>
</html>

<?php 
session_start();
include("../user/db_connection.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

if(isset($_POST["email"])){
    $email = $_POST["email"];

    $_SESSION['token'] = '';
    $_SESSION['email'] = '';

    $sql = mysqli_query($con, "SELECT * FROM staff WHERE S_Email='$email'");
    $query = mysqli_num_rows($sql);
    $fetch = mysqli_fetch_assoc($sql);

    if(mysqli_num_rows($sql) <= 0){
        ?>
        <script>
            alert("<?php echo "Sorry, no emails exist in the database."; ?>");
        </script>
        <?php
    } else {
        $token = bin2hex(random_bytes(50));

        $_SESSION['token'] = $token;
        $_SESSION['email'] = $email;

        echo "Token: " . $_SESSION['token'] . "<br>";
        echo "Email: " . $_SESSION['email'] . "<br>";

        require ("PHPMailer/PHPMailer.php"); 
        require ("PHPMailer/SMTP.php");
        require ("PHPMailer/Exception.php");
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host='smtp.gmail.com';
        $mail->Port=587;
        $mail->SMTPAuth=true;
        $mail->SMTPSecure='tls';

        $mail->Username='cuppajoy88@gmail.com';
        $mail->Password='imjo jrya kyki hgjt';
        $mail->setFrom('cuppajoy88@gmail.com', 'Password Reset');
        $mail->addAddress($_POST["email"]);

        $mail->isHTML(true);
        $mail->Subject="Recover your password";
        $mail->Body="<b>Dear User,</b>
        <h3>We received a request to reset your password.</h3>
        <p>Kindly click the below link to reset your password:</p>
        <a href='localhost/fyp/admin/reset_psw.php?token=$token&email=$email'>Reset Password</a>
        <br><br>
        <p>With regards,</p>
        <b>Cuppa Joy Team</b>";
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
                alert("<?php echo "Invalid Email"; ?>");
            </script>
            <?php
        } else {
            ?>
            <script>
                alert('Email successfully send out !  Kindly check your email inbox.');
                window.location.href="pages-login.php";
            </script>
            <?php
        }
    }
}
?>
