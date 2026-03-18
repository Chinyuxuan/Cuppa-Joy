<?php session_start() ?>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Responsive Bootstrap4 Shop Template, Created by Imran Hossain from https://imransdesign.com/">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="reset.css">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" href="assets/img/smile-black.png" type="image/icon">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />

    <title>Reset Password- Cuppa Joy</title>
    <style>
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

<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
<link rel="stylesheet" href="assets/css/all.min.css">
</head>
<body>
<a href="sign-in.php" class="back"> <i class="fas fa-arrow-left"></i> Back to Sign In</a>
<section id="editpassword">
        <h2>Reset Password
            <span class="modal-close" ></span>
        </h2>
        <form action="" id="passwordfrm" name="passwordfrm" method="POST">
            <label for="newpass">Enter new password</label>
            <input type="password" name="password" id="newpass" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                    title="Must contain at least one number and one uppercase and lowercase letter, 
                    and at least 8 characters" required><i class='bx bx-hide eye-icon1'></i>
			<div id="message">
                    
                    <p id="letter" class="invalid">A <b>lowercase</b> letter</p>
                    <p id="capital" class="invalid">A <b>capital (uppercase)</b> letter</p>
                    <p id="number" class="invalid">A <b>number</b></p>
                    <p id="length" class="invalid">Min <b>8 characters</b></p>
            </div>
            <label for="newcfpass">Confirm new password</label>
            
            <input type="password" name="newcfpass" id="newcfpass" required><i class='bx bx-hide eye-icon2'></i>
			<br>
            <span id="passwordMatchStatus" class="status-message"></span>
			<input type="submit" id="passbtn" name="reset" value="Reset" onclick="submitForm(event)">


        </form>
    </section>
</body>
</html>
<?php
if(isset($_POST["reset"])){
    include('db_connection.php');
    $newPassword = $_POST["password"];

    if(isset($_SESSION['token']) && isset($_SESSION['email'])) {
        $token = $_SESSION['token'];
        $email = $_SESSION['email'];

        // Retrieve old password from the database
        $sql = mysqli_query($con, "SELECT C_PW FROM `customer` WHERE C_Email='$email'");
        $query = mysqli_num_rows($sql);
        $fetch = mysqli_fetch_assoc($sql);

        if ($query > 0) {
            $oldPassword = $fetch['C_PW'];

            // Check if new password is the same as the old password
            if (password_verify($newPassword, $oldPassword)) {
                ?>
                <script>
                    // Display alert if new password is same as old password
                    swal({
                        icon: 'error',
                        title: 'Error',
                        text: 'New password cannot be the same as the old password',
                        showConfirmButton: false,
                        
                    });
                </script>
                <?php
            } else {
                // Hash the new password
                $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                // Update password in the database
                mysqli_query($con, "UPDATE `customer` SET C_PW='$newHashedPassword' WHERE C_Email='$email'");

                ?>
                <script>
                    // Display success message and redirect
                    swal({
                        icon: 'success',
                        title:'Success',
                        text: 'Your password has been successfully reset',
                        showConfirmButton: false,
                       
                    }).then(() => {
                        window.location.replace("sign-in.php");
                    });
                </script>
                <?php
            }
        } else {
            // Display error if email not found in database
            echo "Email not found in database<br>";
        }
    } else {
        // Display error if token is not set
        echo "Token is not set!<br>";
        exit;
    }
}
?>


 <script src="reset.js"></script>