<?php
  session_start();
  include("../user/db_connection.php");

  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;
  use PHPMailer\PHPMailer\SMTP;

  if (!isset($_SESSION["ridername"])) {
    header("location:pages-login.php");
    exit;
  }

  $currectuser = $_SESSION["riderid"];
  $sql = "SELECT * FROM `rider` WHERE R_ID = '$currectuser'";

  $gotResult = mysqli_query($con, $sql);

  //retrieve data for  that rider id
  if($gotResult){
    if(mysqli_num_rows($gotResult)>0){
      while($row = mysqli_fetch_array($gotResult)){
        $name = $row['R_Name'];
        $phno = $row['R_Contact_Number'];
        $Email = $row['R_Email'];
        $status = $row['R_Status'];
        $password = $row['R_PW'];
        $photo = $row['R_Photo'];
        $ME = $row['Money_Earned'];
        $TC = $row['Total_Claim'];
        $PlateNumber = $row['R_PlateNo'];
        $license = $row['R_License'];
        $led = $row['L_Exp_Date'];
        if(!is_null($led)){
          $parts = explode('-', $led);
          $newled = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
        }else{
          $newled = "00-00-0000";
        }
        $bt = $row['Bank_Type'];
        if($bt == ""){
          $bt = "no bank selected";
        }
        $bacn = $row['Bank_Number'];
      }
    }
  }
  $ttl = $ME + $TC;
  $currentDate = date("Y-m-d");
  $expiryTimestamp = strtotime($led);
  $formattedExpiryDate = date('d-m-Y', $expiryTimestamp);
  $currentTimestamp = time();
  $timeDifference = $expiryTimestamp - $currentTimestamp;
  $monthsDifference = round($timeDifference / (30 * 24 * 60 * 60));

  $sql3 = "SELECT * FROM reservation WHERE R_ID = ? AND Delivery_Status = 'completed'";
  $stmt3 = $con->prepare($sql3);
  $stmt3 -> bind_param("s", $_SESSION['riderid']);
  $stmt3 -> execute();
  $result3 = $stmt3 -> get_result();
  $finishorder = $result3->num_rows;
  $stmt3->close();

  $sql4 = "SELECT COUNT(*) as tasktotal FROM reservation WHERE R_ID = ?";
  $stmt4 = $con->prepare($sql4);
  $stmt4->bind_param("s", $_SESSION['riderid']); // Assuming R_ID is an integer
  $stmt4->execute();
  $result4 = $stmt4->get_result();
  if ($row4 = $result4->fetch_assoc()) {
    $total = $row4['tasktotal'];
  }
  // $currentMonth = date('m');
  
  $sql5 = "SELECT * FROM reservation WHERE R_ID = ? AND Delivery_Status = 'completed' ORDER BY O_ID DESC";
  $stmt5 = $con->prepare($sql5);
  $stmt5 -> bind_param("s", $_SESSION['riderid']);
  $stmt5 -> execute();
  $result5 = $stmt5->get_result();
  $orderrows = [];
  if($result5->num_rows>0){
    while($row5 = $result5->fetch_assoc()){
      $orderrows[] = $row5;
    }
  }

  if(isset($_POST['fullName'])){
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $editName = $_POST['fullName'];
      $editContact = $_POST['phone'];
      $licensedate = $_POST['licensedate'];
      $banktype = $_POST['bank'];
      $banknum = $_POST['banknumber'];

      $rphoto = $photo;
      $lphoto = $license;
      $Path = ''; // Variable to store image path
      if (isset($_FILES["profile-image"]) && $_FILES["profile-image"]["error"] == 0) {
          // Save image to a specific directory
          $targetDir = "../image/rider/"; // Directory where files will be stored
          $targetFile = $targetDir . basename($_FILES["profile-image"]["name"]);
          $rphoto = $_FILES["profile-image"]["name"];
          if (move_uploaded_file($_FILES["profile-image"]["tmp_name"], $targetFile)) {
              $Path = $targetFile; // Set staff photo path
          } else {
              echo "Error uploading photo.";
          }
      }
      if (isset($_FILES["license-image"]) && $_FILES["license-image"]["error"] == 0) {
        // Save image to a specific directory
        $targetDir1 = "../image/rider/"; // Directory where files will be stored
        $targetFile1 = $targetDir1 . basename($_FILES["license-image"]["name"]);
        $lphoto = $_FILES["license-image"]["name"];
        if (move_uploaded_file($_FILES["license-image"]["tmp_name"], $targetFile1)) {
            $Path1 = $targetFile1; // Set staff photo path
        } else {
            echo "Error uploading photo.";
        }
    }
      $sql = "UPDATE `rider` SET R_Name = ?, R_Contact_Number = ?, R_Email = ?, R_PW = ?, R_Photo = ?, R_License = ?, L_Exp_Date = ?, Bank_Type = ?, Bank_Number = ? WHERE R_ID = ?";
      $stmt = mysqli_prepare($con, $sql);
      mysqli_stmt_bind_param($stmt, "ssssssssis", $editName, $editContact, $Email, $password, $rphoto, $lphoto, $licensedate, $banktype, $banknum, $currectuser);
      $result = mysqli_stmt_execute($stmt);
      if ($result) {
        $_SESSION["ridername"] = $editName;
          ?>
          <script type="text/javascript">
            alert("Profile changed successfully.");
            window.location.href = "users-profile.php";
          </script>
          <?php
          exit;
      } else {
          echo "Error updating profile: " . mysqli_error($con);
      }

      mysqli_stmt_close($stmt);
      
    }
  }

  if (isset($_POST['password'])) {
    $pass1 = $_POST['password'];
    $newpass = $_POST['newpassword'];
    $newcfpass = $_POST['renewpassword'];

    if($newpass != $pass1){
      if($newpass == $newcfpass){
        $newpass = password_hash($newpass, PASSWORD_DEFAULT);
        if (password_verify($pass1, $password)) {
          $sql1 = "UPDATE `rider` SET R_Name = ?, R_Contact_Number = ?, R_Email = ?, R_PW = ? WHERE R_ID = ?";

          $stmt1 = mysqli_prepare($con, $sql1);
          mysqli_stmt_bind_param($stmt1, "sssss", $name, $phno, $Email, $newpass, $currectuser);

          $result1 = mysqli_stmt_execute($stmt1);

          if ($result1) {
              // Update successful
              echo "<script type='text/javascript'>
                      alert('Update Successfully!');
                      window.location.href = 'users-profile.php';
                    </script>";
              exit;
          } else {
              echo "Error updating profile: " . mysqli_error($con);
          }
          mysqli_stmt_close($stmt1);
        } else {
            echo "<script type='text/javascript'>alert('Old password is incorrect, please enter again.');</script>";
        }
      }else {
        echo "<script type='text/javascript'>alert('Your new password and confirm password is not the same, please enter again.');</script>";
      }
    } else {
      echo "<script type='text/javascript'>alert('Your new password cannot be same as the old password.');</script>";
    }
  }

  function sendEmail($Email, $name, $price, $bank, $num) {

    require ("PHPMailer/PHPMailer.php"); // Adjust path as needed
    require ("PHPMailer/SMTP.php"); // Adjust path as needed
    require ("PHPMailer/Exception.php"); // Include the Exception class

    $mail = new PHPMailer(true);

    try {
      $mail->isSMTP();                                            //Send using SMTP
      $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
      $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
      $mail->Username   = 'cuppajoy88@gmail.com';                     //SMTP username
      $mail->Password   = 'imjo jrya kyki hgjt';                               //SMTP password
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
      $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
  
      //Recipients
      $mail->setFrom('cuppajoy88@gmail.com', 'Tok');
      $mail->addAddress($Email);

      $mail->isHTML(true);                                  //Set email format to HTML
      $mail->Subject = 'Earning Claimed Notice';
      $mail->Body    = "
      <p>Good day, $name.</p>
      <p>Thank you for your hard work. Your claim for the amount of <b>RM $price</b> has been credited to your bank account.</p>
      <p>Bank: $bank <br />Account number: $num</p>
      <hr />
      <p>If you hve any questions or require furthur assistance, please do not hesitate to contact Cuppa Joy.</p>
      <p>Thank you for your attention of this matter.</p>
      <p>Best regards,<p>
      <div>
        <p>CuppaJoy</p>
        <p>123, Jalan Ayer Keroh Lama</p>
        <p>Kampung Baru Ayer Keroh, 75450 Ayer Keroh,</p>
        <p>Melaka, Malaysia</p>
      </div>
      ";
      $mail->SMTPOptions = array(
          'ssl' => array(
              'verify_peer' => false,
              'verify_peer_name' => false,
              'allow_self_signed' => true
          )
      );
      $mail->send();
      return true;
    } catch (Exception $e) {
      echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
      return false; // Failed to send email
    }
  }

  if(isset($_POST['claimmoney'])){
    if($_POST['claimmoney'] == 1){
      $sql7 = "UPDATE `rider` SET Money_Earned = 0, Total_Claim = ? WHERE R_ID = ? ";
      $stmt7 = $con->prepare($sql7);
      $stmt7 -> bind_param("ds", $ttl, $currectuser);
      $result7 = $stmt7 -> execute();
      if($result7){
        echo "successfully";
        $maskedNum = str_repeat('*', strlen($bacn) - 4) . substr($bacn, -4);
        if (sendEmail($Email, $name, $ME, $bt, $maskedNum)) {
          echo 'Email sent successfully.';
      } else {
          echo 'Failed to send email.';
      }
      }
    }
  }
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Rider Cuppa Joy - Profile</title>
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

  <script src="https://kit.fontawesome.com/4dd5e87a71.js" crossorigin="anonymous"></script>
 
  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">
  <style>
    #message { display:none; color: #000; position: relative; margin-left: 10px; font-family: "Poppins", sans-serif; font-size: 14px; }
    #message p { font-size: 14px; margin-top: -10px; }
    .valid { color: green; }
    .invalid { color: red; }
  </style>

  <!-- =======================================================
  * Template Name: NiceAdmin
  * Updated: Jan 29 2024 with Bootstrap v5.3.2
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="index.php" class="logo d-flex align-items-center">
        <span class="d-none d-lg-block">Cuppa&nbsp;</span>
        <img src="assets/image/smile-black.png" alt="">
        <span class="d-none d-lg-block"> Joy</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="../image/rider/<?php echo $photo; ?>" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $_SESSION['ridername'] ?></span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?php echo $_SESSION['ridername'] ?></h6>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.php">
                <i class="bi bi-person"></i>
                <span>My Profile</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center signOutLink" href="pages-login.php">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link collapsed" href="index.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="orderlist.php">
          <i class="bi bi-list-ol"></i>
          <span>Delivery List</span>
        </a>
      </li><!-- End OrderList Nav -->

      <li class="nav-heading">Users</li>

      <li class="nav-item">
        <a class="nav-link " href="users-profile.php">
          <i class="bi bi-person"></i>
          <span>Profile</span>
        </a>
      </li><!-- End Profile Page Nav -->

      <!-- <li class="nav-item">
        <a class="nav-link collapsed" href="pages-contact.html">
          <i class="bi bi-envelope"></i>
          <span>Contact</span>
        </a>
      </li> -->
<!-- End Contact Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed signOutLink" href="pages-login.php">
          <i class="bi bi-box-arrow-right"></i>
          <span>Sign Out</span>
        </a>
      </li><!-- End LogOutPage Nav -->
    </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Profile</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item">Users</li>
          <li class="breadcrumb-item active">Profile</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section profile">
      <div class="row">
        <div class="col-xl-4">

          <div class="card">
            <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

              <img src="../image/rider/<?php echo $photo; ?>" alt="Profile" class="rounded-circle">
              <h2><?php echo $_SESSION['ridername'] ?></h2>
              
            </div>
          </div>
          <div class="card">
            <div class="card-body datacontainer pt-4">
              <!-- <p>This is an examle page with no contrnt. You can use it as a starter for your custom pages.</p> -->
              <div class="datarow">
                <div class="databox">
                  <i class="bi bi-check-all"></i>
                  <div class="data"><?php echo $finishorder; ?></div>
                  <div class="desc">Finished Order</div>
                </div>
                <div class="databox">
                  <i class="bi bi-clipboard"></i>
                  <div class="data"><?php echo $total; ?></div>
                  <div class="desc">Total Order</div>
                </div>
                <div class="databox">
                  <i class="bi bi-currency-dollar"></i>
                  <div class="data">RM <?php echo number_format($ttl, 2); ?></div>
                  <div class="desc">Total Earning</div>
                </div>
              </div>
              <div class="datarow">
                <div class="databox">
                  <i class="fa-solid fa-sack-dollar"></i>
                  <div class="data current_balance">RM <?php echo number_format($ME, 2); ?></div>
                  <div class="desc">Total Balance</div>
                </div>
                <div class="btnclaim">
                  <button class="btn btn-primary btn-claim">Claim</button>
                </div>
              </div>
            </div>
          </div>

        </div>

        <div class="col-xl-8">

          <div class="card">
            <div class="card-body pt-3">
              <!-- Bordered Tabs -->
              <ul class="nav nav-tabs nav-tabs-bordered">

                <li class="nav-item">
                  <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Overview</button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-settings">Earnings</button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit Profile</button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Change Password</button>
                </li>

              </ul>
              <div class="tab-content pt-2">

                <div class="tab-pane fade show active profile-overview" id="profile-overview">
                  <h5 class="card-title">Profile Details</h5>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label ">Rider ID</div>
                    <div class="col-lg-9 col-md-8 ans"><?php echo $_SESSION['riderid'] ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label ">Name</div>
                    <div class="col-lg-9 col-md-8 ans"><?php echo $name ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Contact Number</div>
                    <div class="col-lg-9 col-md-8 ans"><?php echo '+6' . $phno ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Email</div>
                    <div class="col-lg-9 col-md-8 ans"><?php echo $Email ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Status</div>
                    <div class="col-lg-9 col-md-8 ans"><?php echo $status ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Bank Name</div>
                    <div class="col-lg-9 col-md-8 ans"><?php echo $bt ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Bank Account Number</div>
                    <div class="col-lg-9 col-md-8 ans"><?php echo $bacn ?></div>
                  </div>

                  <h5 class="card-title">Rider Details</h5>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Plate Number</div>
                    <div class="col-lg-9 col-md-8 ans"><?php echo $PlateNumber ?></div>
                  </div>

                  <div class="row">
                      <div class="col-lg-3 col-md-4 label">License</div>
                      <div class="col-lg-9 col-md-8 ans licensephoto">
                          <img id="profile-license-image" src="../image/rider/<?php echo $license; ?>" alt="License" onclick="showImage()">
                          <div id="popupOverlay" class="popup-overlay">
                              <div id="popupContent" class="popup-content">
                                  <span id="closeButton" class="close">&times;</span>
                                  <img src="../image/rider/<?php echo $license; ?>" alt="License-photo" id="imageToShow" class="popup-image">
                              </div>
                          </div>
                      </div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">License Expiry Date</div>
                    <div class="col-lg-9 col-md-8 ans"><?php echo $newled ?></div>
                  </div>
                  <div class="row">
                    <?php
                      if($led < $currentDate){
                        echo '<p style="color: red; font-size: bold;"><em>Your license is already expiry, please change it.</em></p>';
                      }else if($monthsDifference <= 3 && $monthsDifference >= 0 ){
                        echo '<p style="color: red; font-size: smaller;"><em>Your license is expiring soon within 3 months. Please renew it.</em></p>';
                      }
                    ?>
                  </div>

                </div>

                <div class="tab-pane fade profile-edit pt-3" id="profile-edit">

                  <!-- Profile Edit Form -->
                  <form method="post" enctype="multipart/form-data">
                    <div class="row mb-3">
                      <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                      <div class="col-md-8 col-lg-9">
                      <img id="profile-image" src="../image/rider/<?php echo $photo; ?>" alt="Profile">
                        <div class="pt-2">
                          <input type="file" id="upload-input" name="profile-image" style="display: none;">
                          <label for="upload-input" class="btn btn-primary btn-sm" title="Upload new profile image"><i class="bi bi-upload"></i> Upload</label>
                          <!-- <a href="#" class="btn btn-danger btn-sm" title="Remove my profile image"><i class="bi bi-trash"></i></a> -->
                        </div>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Name</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="fullName" type="text" class="form-control" id="fullName" value="<?php echo $name ?>" required>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Phone" class="col-md-4 col-lg-3 col-form-label">Phone </label>
                      <div class="col-md-8 col-lg-9">
                        <input name="phone" type="text" class="form-control" id="Phone" value="<?php echo $phno ?>" required>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">License Image</label>
                      <div class="col-md-8 col-lg-9">
                      <img id="license-image" src="../image/rider/<?php echo $license; ?>" alt="License">
                        <div class="pt-2">
                          <input type="file" id="upload-license" name="license-image" style="display: none;">
                          <label for="upload-license" class="btn btn-primary btn-sm" title="Upload new license"><i class="bi bi-upload"></i> Upload</label>
                          <!-- <a href="#" class="btn btn-danger btn-sm" title="Remove my profile image"><i class="bi bi-trash"></i></a> -->
                        </div>
                      </div>
                    </div>
                    
                    <div class="row mb-3">
                      <label for="licensedate" class="col-md-4 col-lg-3 col-form-label">License Expiry Date</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="licensedate" type="date" class="form-control" id="licensedate" value="<?php echo $led; ?>" min="<?php echo date('Y-m-d'); ?>" required>
                      </div>
                    </div>
                    
                    <!-- <div class="row mb-3">
                      <label for="Status" class="col-md-4 col-lg-3 col-form-label">Status</label>
                      <div class="col-md-8 col-lg-9">
                        <select name="status" id="status" class="form-control" required>
                          <option value="0" disabled></option>
                          <option value="active">Active</option>
                          <option value="inactive">Inactive</option>
                        </select>
                      </div>
                    </div> -->

                    <div class="row mb-3">
                      <label for="bank" class="col-md-4 col-lg-3 col-form-label">Bank Name</label>
                      <div class="col-md-8 col-lg-9">
                        <!-- <input name="bank" type="option" class="form-control" id="bank" value="<?php echo $bt ?>" required> -->
                         <select name="bank" id="bank" class="form-select" required>
                          <option value="0" <?php if ($bt == 'no bank selected') echo 'selected'; ?> disabled default>Select Your Bank</option>
                          <option value="AEON Bank" <?php if ($bt == 'AEON Bank') echo 'selected'; ?>>AEON Bank</option>
                          <option value="Affin Bank Berhad" <?php if ($bt == 'Affin Bank Berhad') echo 'selected'; ?>>Affin Bank Berhad</option>
                          <option value="Al-Rajhi Banking & Investment Corporation (Malaysia) Berhad" <?php if ($bt == 'Al-Rajhi Banking & Investment Corporation (Malaysia) Berhad') echo 'selected'; ?>>Al-Rajhi Banking & Investment Corporation (Malaysia) Berhad</option>
                          <option value="Alliance Bank Malaysia Berhad" <?php if ($bt == 'Alliance Bank Malaysia Berhad') echo 'selected'; ?>>Alliance Bank Malaysia Berhad</option>
                          <option value="Ambank" <?php if ($bt == 'Ambank') echo 'selected'; ?>>Ambank</option>
                          <option value="BNP Paribas Malaysia Berhad" <?php if ($bt == 'BNP Paribas Malaysia Berhad') echo 'selected'; ?>>BNP Paribas Malaysia Berhad</option>
                          <option value="Bangkok Bank Berhad" <?php if ($bt == 'Bangkok Bank Berhad') echo 'selected'; ?>>Bangkok Bank Berhad</option>
                          <option value="Bank Islam Malaysia Berhad" <?php if ($bt == 'Bank Islam Malaysia Berhad') echo 'selected'; ?>>Bank Islam Malaysia Berhad</option>
                          <option value="Bank Kerjasame Rakyat Malaysia Berhad" <?php if ($bt == 'Bank Kerjasame Rakyat Malaysia Berhad') echo 'selected'; ?>>Bank Kerjasame Rakyat Malaysia Berhad</option>
                          <option value="Bank Muamalat Malaysia Berhad" <?php if ($bt == 'Bank Muamalat Malaysia Berhad') echo 'selected'; ?>>Bank Muamalat Malaysia Berhad</option>
                          <option value="Bank Pertanian Malaysia Berhad (Argobank)" <?php if ($bt == 'Bank Pertanian Malaysia Berhad (Argobank)') echo 'selected'; ?>>Bank Pertanian Malaysia Berhad (Argobank)</option>
                          <option value="Bank Simpanna Nasional Berhad" <?php if ($bt == 'Bank Simpanna Nasional Berhad') echo 'selected'; ?>>Bank Simpanna Nasional Berhad</option>
                          <option value="Bank of America" <?php if ($bt == 'Bank of America') echo 'selected'; ?>>Bank of America</option>
                          <option value="Bank of China (Malaysia) Berhad" <?php if ($bt == 'Bank of China (Malaysia) Berhad') echo 'selected'; ?>>Bank of China (Malaysia) Berhad</option>
                          <option value="BigPay" <?php if ($bt == 'BigPay') echo 'selected'; ?>>BigPay</option>
                          <option value="Boost Bank" <?php if ($bt == 'Boost Bank') echo 'selected'; ?>>Boost Bank</option>
                          <option value="China Construction Bank (Malaysia) Berhad" <?php if ($bt == 'China Construction Bank (Malaysia) Berhad') echo 'selected'; ?>>China Construction Bank (Malaysia) Berhad</option>
                          <option value="CIMB Bank" <?php if ($bt == 'CIMB Bank') echo 'selected'; ?>>CIMB Bank</option>
                          <option value="Citibank Berhad" <?php if ($bt == 'Citibank Berhad') echo 'selected'; ?>>Citibank Berhad</option>
                          <option value="Deutsche Bank (Malaysia) Berhad" <?php if ($bt == 'Deutsche Bank (Malaysia) Berhad') echo 'selected'; ?>>Deutsche Bank (Malaysia) Berhad</option>
                          <option value="Finexus Cards Sdn Bhd" <?php if ($bt == 'Finexus Cards Sdn Bhd') echo 'selected'; ?>>Finexus Cards Sdn Bhd</option>
                          <option value="GXBank" <?php if ($bt == 'GXBank') echo 'selected'; ?>>GXBank</option>
                          <option value="HSBC Bank Malaysia Berhad" <?php if ($bt == 'HSBC Bank Malaysia Berhad') echo 'selected'; ?>>HSBC Bank Malaysia Berhad</option>
                          <option value="Hong Leong Bank" <?php if ($bt == 'Hong Leong Bank') echo 'selected'; ?>>Hong Leong Bank</option>
                          <option value="Industrial and Commercial Bank of China (M) Berhad" <?php if ($bt == 'Industrial and Commercial Bank of China (M) Berhad') echo 'selected'; ?>>Industrial and Commercial Bank of China (M) Berhad</option>
                          <option value="J.P. Morgan Chase Bank Bhd" <?php if ($bt == 'J.P. Morgan Chase Bank Bhd') echo 'selected'; ?>>J.P. Morgan Chase Bank Bhd</option>
                          <option value="Kuwait Finance House (Malaysia) Berhad" <?php if ($bt == 'Kuwait Finance House (Malaysia) Berhad') echo 'selected'; ?>>Kuwait Finance House (Malaysia) Berhad</option>
                          <option value="MBSB Bank Berhad" <?php if ($bt == 'MBSB Bank Berhad') echo 'selected'; ?>>MBSB Bank Berhad</option>
                          <option value="MUFG Bank (Malaysia) Berhad" <?php if ($bt == 'MUFG Bank (Malaysia) Berhad') echo 'selected'; ?>>MUFG Bank (Malaysia) Berhad</option>
                          <option value="Maybank" <?php if ($bt == 'Maybank') echo 'selected'; ?>>Maybank</option>
                          <option value="Merchantdrade" <?php if ($bt == 'Merchantdrade') echo 'selected'; ?>>Merchantdrade</option>
                          <option value="Mizuho Corporate Bank Malaysia Berhad" <?php if ($bt == 'Mizuho Corporate Bank Malaysia Berhad') echo 'selected'; ?>>Mizuho Corporate Bank Malaysia Berhad</option>
                          <option value="OCBC Bank (Malaysia) Berhad" <?php if ($bt == 'OCBC Bank (Malaysia) Berhad') echo 'selected'; ?>>OCBC Bank (Malaysia) Berhad</option>
                          <option value="Public Bank Berhad/ Public Islamic Bank" <?php if ($bt == 'Public Bank Berhad/ Public Islamic Bank') echo 'selected'; ?>>Public Bank Berhad/ Public Islamic Bank</option>
                          <option value="RHB Bank" <?php if ($bt == 'RHB Bank') echo 'selected'; ?>>RHB Bank</option>
                          <option value="ShopeePay" <?php if ($bt == 'ShopeePay') echo 'selected'; ?>>ShopeePay</option>
                          <option value="Standard Chartered Bank Malaysia" <?php if ($bt == 'Standard Chartered Bank Malaysia') echo 'selected'; ?>>Standard Chartered Bank Malaysia</option>
                          <option value="Sumitomo Mitsui Banking Corporation Malaysia Berhad" <?php if ($bt == 'Sumitomo Mitsui Banking Corporation Malaysia Berhad') echo 'selected'; ?>>Sumitomo Mitsui Banking Corporation Malaysia Berhad</option>
                          <option value="Touch n Go eWallet" <?php if ($bt == 'Touch n Go eWallet') echo 'selected'; ?>>Touch n Go eWallet</option>
                          <option value="United Overseas Bank Berhad" <?php if ($bt == 'United Overseas Bank Berhad') echo 'selected'; ?>>United Overseas Bank Berhad</option>
                         </select>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="banknumber" class="col-md-4 col-lg-3 col-form-label">Bank Account Number</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="banknumber" type=""  maxlength="19"  class="form-control" id="banknumber" value="<?php echo $bacn ?>" required>
                      </div>
                    </div>

                    <div class="text-center">
                      <button type="submit" class="btn btn-success"  onclick="validateProfileForm(event)">Save Changes</button>
                    </div>
                  </form><!-- End Profile Edit Form -->

                </div>

                <div class="tab-pane fade" id="profile-settings">
                  <h5 class="card-title">Detail of Earnings <span>| Recent</span></h5>
                  <div class="searchdate">
                    <input type="date" class="searchbydate form-control">
                    <button class="btn btn-success" onclick="searchByDate()"><i class="bi bi-search"></i> Search</button>
                    <button class="btn clearbutton" onclick="showallactivity()"><i class="bi bi-calendar-x"></i>Clear</button>
                  </div>
                  <div class="row">
                    <div class="card-body">
                      <div class="activity">
                      <?php
                          $totalrowtoshow = 0;
                          $addclass = "";
                          foreach($orderrows as $orderrow){
                            $totalrowtoshow++;
                            if($totalrowtoshow>15){
                              $addclass = "hidden-activity";
                            }
                            $earn = $orderrow['Total'] * 0.08;
                            $date = $orderrow['Date'];
                            $dateparts = explode('-', $date);
                            $newdate = $dateparts[2] . '-' . $dateparts[1] . '-' . $dateparts[0];
                            $addingfee = 0;
                            $ratingr = null;
                            $sql6 = "SELECT * FROM rating WHERE O_ID = ?";
                            $stmt6 = $con->prepare($sql6);
                            $stmt6 -> bind_param("i", $orderrow['O_ID']);
                            $stmt6->execute();
                            $result6 = $stmt6->get_result();
                            if($row6 = $result6->fetch_assoc()){
                              $ratingr = $row6['Rating_R'];
                            }
                            if($ratingr == 5){
                              $addingfee = $orderrow['Total'] * 0.05;
                              $totalrowtoshow++;
                              if($totalrowtoshow>15){
                                $addclass = "hidden-activity";
                              }
                              echo '<div class="activity-item d-flex '.$addclass.'">';
                              echo   '<div class="activite-label">'.$newdate.'</div>';
                              echo   "<i class='bi bi-circle-fill activity-badge text-primary align-self-start'></i>";
                              echo   '<div class="activity-content extraadding">';
                              echo   'You get a five star rating from Order '.$orderrow['O_ID'].', this is your reward. Congrate.';
                              echo '<span class="coinbox"><i class="bi bi-coin"></i> +  RM'.number_format($addingfee, 2).'</span>';
                              echo   '</div>';
                              echo '</div>';
                            }
                            echo '<div class="activity-item d-flex '.$addclass.'">';
                            echo   '<div class="activite-label">'.$newdate.'</div>';
                            echo   "<i class='bi bi-circle-fill activity-badge text-success align-self-start'></i>";
                            echo   '<div class="activity-content">';
                            echo   'You have earned  from Order '.$orderrow['O_ID'].'.';
                            echo '<span class="coinbox"><i class="bi bi-coin"></i> +  RM'.number_format($earn, 2).'</span>';
                            echo   '</div>';
                            echo '</div>';
                          }
                        ?>
                        <!-- <div class="activity-item d-flex">
                          <div class="activite-label"><div class="inlinemoneybox"></div></div>
                          <i class='bi bi-circle-fill activity-badge text-success align-self-start'></i>
                          <div class="activity-content">
                            Quia quae rerum <a href="#" class="fw-bold text-dark">explicabo officiis</a> beatae
                          </div>
                        </div>  -->
                        <!-- End activity item  -->
                        <span class="searchnotification">If you do not find the record that you want, you may find by using the date.</span>
                      </div>
                    </div>
                  </div>
                  <div class="shownorecord" style="display: none;">There are no earnings as no task commpleted.</div>
                </div>

                <div class="tab-pane fade pt-3" id="profile-change-password">
                  <!-- Change Password Form -->
                  <form method="post">

                    <div class="row mb-3">
                      <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Current Password</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="password" type="password" class="form-control" id="currentPassword" required>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New Password</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="newpassword" type="password" class="form-control" id="newPassword" required>
                      </div>
                    </div>
                    <div id="message">
                      <p id="letter" class="invalid"><i class="bi bi-info-circle me-1"></i>A <b>lowercase</b> letter</p>
                      <p id="capital" class="invalid"><i class="bi bi-info-circle me-1"></i>A <b>capital (uppercase)</b> letter</p>
                      <p id="number" class="invalid"><i class="bi bi-info-circle me-1"></i>A <b>number</b></p>
                      <p id="length" class="invalid"><i class="bi bi-info-circle me-1"></i>Min <b>8 characters</b></p>
                    </div>

                    <div class="row mb-3">
                      <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Re-enter New Password</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="renewpassword" type="password" class="form-control" id="renewPassword" required>
                      </div>
                    </div>

                    <div class="text-center">
                      <button type="submit" class="btn btn-success" onclick="submitForm(event)">Change Password</button>
                    </div>
                  </form><!-- End Change Password Form -->

                </div>

              </div><!-- End Bordered Tabs -->

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <!-- <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>NiceAdmin</span></strong>. All Rights Reserved
    </div>
    <div class="credits"> -->
      <!-- All the links in the footer should remain intact. -->
      <!-- You can delete the links only if you purchased the pro version. -->
      <!-- Licensing information: https://bootstrapmade.com/license/ -->
      <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
      <!-- Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
    </div>
  </footer>End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.min.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>
  <script>
    document.getElementById('upload-input').addEventListener('change', function(event) {
      var file = event.target.files[0];
      var reader = new FileReader();
      reader.onload = function(event) {
          document.getElementById('profile-image').src = event.target.result;
      };
      reader.readAsDataURL(file);
    });
    document.getElementById('upload-license').addEventListener('change', function(event) {
      var file = event.target.files[0];
      var reader = new FileReader();
      reader.onload = function(event) {
          document.getElementById('license-image').src = event.target.result;
      };
      reader.readAsDataURL(file);
    });
    // $(document).ready(function(){
    //   $('#showImageBtn').on('click', function(){
    //     if($('#imageToShow').css('display') == "none"){
    //         $('#imageToShow').css('display', 'block');
    //     } else {
    //         $('#imageToShow').css('display', 'none');
    //     }
    //   });
    // });
    function showImage() {
      var overlay = document.getElementById("popupOverlay");
      var image = document.getElementById("popupContent");

      overlay.style.display = "block";
      image.style.display = "block";
    }

    // Close the popup when the close button is clicked
    document.getElementById("closeButton").onclick = function() {
      var overlay = document.getElementById("popupOverlay");
      var image = document.getElementById("popupContent");

      overlay.style.display = "none";
      image.style.display = "none";
    };
    function searchByDate() {
      var searchDateInput = $('.searchbydate').val();
      var searchDate = convertDateFormat(searchDateInput); // Convert input date format
      $('.activity-item').each(function() {
        var itemDate = $(this).find('.activite-label').text();
        if (searchDate != '' && itemDate != searchDate) {
            $(this).addClass('hidden-activity'); // Add a class to hide the element
        } else {
            $(this).removeClass('hidden-activity'); // Remove the class to show the element
        }
      });
    }
    function convertDateFormat(inputDate) {
      // Split input date into day, month, and year
      var parts = inputDate.split('-');
      // Rearrange parts to match the desired format (dd-mm-yyyy)
      var formattedDate = parts[2] + '-' + parts[1] + '-' + parts[0];
      return formattedDate;
    }
    function showallactivity(){
        $('.searchbydate').val('');
        var countotal = 1;
        $('.activity-item').each(function(){
            if(countotal <= 15){
                $(this).removeClass('hidden-activity');
            } else {
                $(this).addClass('hidden-activity');
            }
            countotal++;
        });
    }
    const pwField = document.getElementById("newPassword");
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
      function submitPasswordForm(event) {
        // Check if any of the validation indicators contain the 'invalid' class
        if ($('#letter').hasClass('invalid') || 
            $('#capital').hasClass('invalid') || 
            $('#number').hasClass('invalid') || 
            $('#length').hasClass('invalid')) {
            alert('Please enter password correctly.');
            event.preventDefault(); // Prevent form submission
            return false;
        }
        return true; // Allow form submission
      }

      // Attach the submitPasswordForm function to the password change form submit event
      $('#profile-change-password form').on('submit', submitPasswordForm);
    });

    $(document).ready(function(){
      $('.btn-claim').on('click', function(){
        var currentmoney = $('.current_balance').text().replace('RM ', '');
        if(currentmoney != 0){
          $.ajax({
            url: 'users-profile.php',
            type: 'POST',
            data: {
              claimmoney: 1, 
            },
            success: function(response){
              if(response.includes('successfully')){
                alert('Money earned successfully.');
                $('.current_balance').html("RM 0.00");
              }
            }
          })
        } else {
          alert('There is no money for you to claim.');
        }
      });
    });

    $(document).ready(function() {
      function validateProfileForm(event) {
        var namefield = $('#fullName').val();
        var isValid = true;
        var phonePattern = /^\d{10,11}$/;
        var phone = $('#Phone').val();
        var licenseDate = $('#licensedate').val();
        var today = new Date().toISOString().split('T')[0];
        var accountnumber = $('#banknumber').val();
        var bankSelect = $('#bank').val();
        var profileImage = $('#upload-input').val();
        var licenseImage = $('#upload-license').val();
        var regex = /^[a-zA-Z\s]+$/;

        if(!regex.test(namefield)){
          alert('Please enter a valid name.');
          isValid = false;
        }

        // Check phone number pattern
        if (!phonePattern.test(phone)) {
          alert('Please enter a valid phone number with 10-11 digits.');
          isValid = false;
        }

        // Check license expiry date
        if (licenseDate < today || licenseDate == "0000-00-00") {
          alert('Please select a valid license expiry date');
          isValid = false;
        }

        // Check bank account number length
        if (accountnumber.length < 5 || accountnumber.length > 18) {
          alert('Please enter a correct bank account number');
          isValid = false;
        }

        // Check if the account number contains only digits
        if (!/^\d+$/.test(accountnumber)) {
          alert('Please enter a correct bank account number');
          isValid = false;
        }

        // Check if a bank is selected
        if (bankSelect == '') {
          alert('Please select a bank.');
          isValid = false;
        }

        if (!isValid) {
          event.preventDefault(); // Prevent form submission
        }

        return isValid;
      }

      $('#profile-edit form').on('submit', validateProfileForm);
    });

    $(document).ready(function(){
      var countactivityrow = 0;
      $('.activity-item').each(function(){
        countactivityrow++;
      });
      if(countactivityrow == 0){
        $('.searchnotification').hide();
        $('.searchdate').hide();
        $('.shownorecord').show();
      }
    });

    $(document).ready(function(){
      $('.signOutLink').on('click', function(){
        return confirm("Are you sure you want to sign out?");
      });
    });
  </script>
</body>

</html>