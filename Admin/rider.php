<?php
session_start();
include("../user/db_connection.php");

if (!isset($_SESSION["S_Name"])) {
  header("location:pages-login.php");
  exit;
}

$currentuser = $_SESSION["S_ID"];
$sql = "SELECT * FROM `staff` WHERE S_ID = '$currentuser'";

$gotResult = mysqli_query($con, $sql);

if ($gotResult && mysqli_num_rows($gotResult) > 0) {
    $row = mysqli_fetch_array($gotResult);
    $name = $row['S_Name'];
    $photo = $row['S_Photo'];
    $superStaff = $row['Super_Staff'];
    $title = ($superStaff == 'Yes') ? 'Super Admin' : 'Admin';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

function generateRiderID($con) {
    do {
        $riderID = "CJR" . rand(1000, 9999); 
        $check_query = "SELECT * FROM rider WHERE R_ID = '$riderID'";
        $check_result = $con->query($check_query);
    } while ($check_result->num_rows > 0);

    return $riderID;
}

//send email
function sendEmail($riderEmail, $riderName, $riderID, $password) {
    require ("PHPMailer/PHPMailer.php");
    require ("PHPMailer/SMTP.php"); 
    require ("PHPMailer/Exception.php"); 

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true; 
        $mail->Username   = 'cuppajoy88@gmail.com'; 
        $mail->Password   = 'imjo jrya kyki hgjt'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587; 
  
        $mail->setFrom('cuppajoy88@gmail.com', 'Tok');
        $mail->addAddress($riderEmail);
        $IMAGE = '../user/assets/img/full-black.png';
        $mail->AddEmbeddedImage($IMAGE, 'website_logo');
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification from Cuppa Joy';
          $mail->Body = "
          <div style='font-family: Arial, sans-serif; font-size: 14px; width: 100%; color: #000;'>
              <div style='display: flex; flex-direction: row; justify-content: space-between; width: 100%;'>
                  <div style='width: 40%; color: #000;'>
                      <img src='cid:website_logo' alt='CuppaJoy' style='width: 200px;'>
                      <h2>Email Verification from Cuppa Joy</h2>
                  </div>
                  <div style='width: 60%; float: right; color: #000;'>
                      <p>CuppaJoy</p>
                      <p>123, Jalan Ayer Keroh Lama</p>
                      <p>Kampung Baru Ayer Keroh, 75450 Ayer Keroh,</p>
                      <p>Melaka, Malaysia</p>
                  </div>
              </div>
              <p>Hello $riderName,</p>
              <p>Your Rider ID is: $riderID.</p>
              <p>Your randomly generated password is: <strong>$password</strong></p>
              <p>Please keep this information safe as you need to login to the system with this ID and password. You can log in to the website by clicking the <a href='http://localhost/fyp/rider/pages-login.php'>link</a>.</p>
              <p>Thank you,</p>
              <p>Cuppa Joy Team</p>
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
        return false;
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['toggleStatus']) && !isset($_POST['toggleSuperStaff'])) {
  $riderID = generateRiderID($con);
  $staffName = $_POST["staffName"];
  $staffEmail = $_POST["staffEmail"];
  $staffContact = $_POST["staffContact"];
  $staffPhotoPath = '';
  if (isset($_FILES["staffPhoto"]) && $_FILES["staffPhoto"]["error"] == 0) {
      $targetDir = "../image/rider/";
      $targetFile = $targetDir . basename($_FILES["staffPhoto"]["name"]);
      $riderphoto = $_FILES["staffPhoto"]["name"];
      if (move_uploaded_file($_FILES["staffPhoto"]["tmp_name"], $targetFile)) {
          $staffPhotoPath = $riderphoto;
      } else {
          echo "Error uploading photo.";
      }
  }

  $riderLicensePath = '';
  if (isset($_FILES["riderLicense"]) && $_FILES["riderLicense"]["error"] == 0) {
      $licenseDir = "assets/image/"; 
      $licenseFile = $licenseDir . basename($_FILES["riderLicense"]["name"]);
      $licensephoto = $_FILES["riderLicense"]["name"];
      if (move_uploaded_file($_FILES["riderLicense"]["tmp_name"], $licenseFile)) {
          $riderLicensePath = $licensephoto;
      } else {
          echo "Error uploading license.";
      }
  }

  $riderPlate = $_POST["riderPlate"];
  $riderLicenseExpDate = $_POST["riderLicenseExpDate"]; 
  $bankType = $_POST["riderBankType"];
  $bankNumber = $_POST["riderBankNumber"];
  $staffStatus = $_POST["staffStatus"];
  $staffPassword = $_POST["staffPassword"];
  $hashedPassword = password_hash($staffPassword, PASSWORD_DEFAULT);
  $riderMoneyEarned = $_POST["riderMoneyEarned"];
  $riderSAID = $_SESSION["S_ID"];
  $plainPassword = generateRandomPassword();
  $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
  $checkEmailQuery = "SELECT * FROM rider WHERE R_Email = ?";
  $stmtCheckEmail = $con->prepare($checkEmailQuery);
  $stmtCheckEmail->bind_param("s", $staffEmail);
  $stmtCheckEmail->execute();
  $resultCheckEmail = $stmtCheckEmail->get_result();

    if ($resultCheckEmail && $resultCheckEmail->num_rows > 0) {

      echo "<script>alert('Email already exists in the database.');
      window.location.replace('rider.php');
      </script>";
  }

  $con->begin_transaction();

  $sql = "INSERT INTO rider (R_ID, R_Name, R_Email, R_Contact_Number, R_Photo, R_License, R_PlateNo, L_Exp_Date, Bank_Type, Bank_Number, R_PW, Money_Earned, R_Status, R_SAID)
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $con->prepare($sql);
if ($stmt) {
      $stmt->bind_param("ssssssssssssss", $riderID, $staffName, $staffEmail, $staffContact, $staffPhotoPath, $riderLicensePath, $riderPlate, $riderLicenseExpDate, $bankType, $bankNumber, $hashedPassword, $riderMoneyEarned, $staffStatus, $riderSAID);

      if ($stmt->execute()) {
          if (sendEmail($staffEmail, $staffName, $riderID,$plainPassword)) {
              $con->commit();
              echo "<script>alert('New rider added successfully.Email sent to the rider.');
              window.location.replace('rider.php');
              </script>";
              } else {
              $con->rollback();
              echo "Failed to send email. Record not created.";
          }
      } else {
          $con->rollback();
          echo "Error executing query: " . $stmt->error;
      }
      $stmt->close();
  } else {
      echo "Error preparing statement: " . $con->error;
  }
}


if (isset($_POST['toggleStatus'])) {
  $riderID = $_POST["riderID"];
  
  $statusQuery = "SELECT R_Status FROM rider WHERE R_ID = '$riderID'";
  $statusResult = $con->query($statusQuery);
  
  if ($statusResult->num_rows > 0) {
      $statusRow = $statusResult->fetch_assoc();
      $currentStatus = $statusRow["R_Status"];
      $newStatus = ($currentStatus == 'Active') ? 'Inactive' : 'Active';
      $updateStatusQuery = "UPDATE rider SET R_Status = '$newStatus' WHERE R_ID = '$riderID'";
      if ($con->query($updateStatusQuery) === TRUE) {
          echo $newStatus;
          exit();
        } else {
            echo "Error updating Status attribute: " . $con->error;
        }
    } else {
        echo "Rider ID not found";
    }
  }

function generateRandomPassword($length = 8) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $password = '';
  for ($i = 0; $i < $length; $i++) {
      $password .= $characters[rand(0, strlen($characters) - 1)];
  }
  return $password;
}
  
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Rider - Cuppa Joy</title>
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

</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="dashboard.php" class="logo d-flex align-items-center">
        <img src="assets/image/full-logo-black.png" alt="">
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <li class="nav-item dropdown pe-3">
        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="../image/admin/<?php echo $photo; ?>" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $name; ?></span>
        </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
          <li class="dropdown-header">
              <h6><?php echo $name; ?></h6>
              <span><?php echo $title; ?></span>
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
              <a class="dropdown-item d-flex align-items-center" href="pages-login.php" onclick="return confirmLogout();">
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
        <a class="nav-link collapsed" href="dashboard.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <li class="nav-heading">Management</li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="staff.php">
          <i class="ri-user-star-fill"></i>
          <span>Admin</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="rider.php">
          <i class="ri-motorbike-fill"></i>
          <span>Rider</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="customer.php">
          <i class="bx bx-group"></i>
          <span>Customer</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="category.php">
          <i class="bx bxs-category-alt"></i>
          <span>Product Category</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="customisation.php">
          <i class="bx bxs-heart-square"></i>
          <span>Customization</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="product.php">
          <i class="bx bxs-coffee"></i>
          <span>Product</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="promo.php">
          <i class="ri-coupon-fill"></i>
          <span>Promo Code</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="order2.php">
          <i class="ri-shopping-cart-fill"></i>
          <span>Order</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="message.php">
          <i class="ri-chat-3-fill"></i>
          <span>Message Center</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="barista.php">
          <i class="ri-contacts-line"></i>
          <span>Barista</span>
        </a>
      </li>

      <li class="nav-heading">Account</li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="users-profile.php">
          <i class="bx bxs-user-circle"></i>
          <span>Profile</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="pages-login.php" onclick="return confirmLogout();">
          <i class="bx bxs-log-out"></i>
          <span>Sign Out</span>
        </a>
      </li>

    </ul>

  </aside>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Rider Management</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Management</li>
          <li class="breadcrumb-item active">Rider</li>
        </ol>
      </nav>
    </div>

    <section class="section">

    <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Rider List</h5>
                <div class="d-flex justify-content-end mb-3">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">Add New Rider</button>
                </div>
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>Rider ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Contact Number</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>

                <?php
                    $sql = "SELECT rider.R_ID, rider.R_Name, rider.R_Photo, rider.R_Contact_Number, rider.R_Email, rider.R_License, rider.L_Exp_Date, rider.R_PlateNo, rider.R_PW, rider.Bank_Type, rider.Bank_Number, rider.Money_Earned, rider.Total_Claim, rider.R_Status, rider.R_SAID, staff.S_Name 
                        FROM rider 
                        JOIN staff ON rider.R_SAID = staff.S_ID";

                    $result = $con->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {

                        echo '<div class="modal fade" id="staffDetailsModal_' . $row["R_ID"] . '" tabindex="-1" aria-labelledby="staffDetailsModalLabel_' . $row["R_ID"] . '" aria-hidden="true">';
                        echo '<div class="modal-dialog modal-dialog-centered modal-lg">';
                        echo '<div class="modal-content">';
                        echo '<div class="modal-header">';
                        echo '<h5 class="modal-title" id="staffDetailsModalLabel_' . $row["R_ID"] . '">Rider Details</h5>';
                        echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
                        echo '</div>';
                        echo '<div class="modal-body">';

                        echo '<div class="row detail border-bottom py-2">';
                        echo '<div class="col-sm-3"><strong>Rider ID:</strong></div>';
                        echo '<div class="col-sm-9">' . $row["R_ID"] . '</div>';
                        echo '</div>';

                        echo '<div class="row detail border-bottom py-2">';
                        echo '<div class="col-sm-3"><strong>Name:</strong></div>';
                        echo '<div class="col-sm-9">' . $row["R_Name"] . '</div>';
                        echo '</div>';

                        echo '<div class="row detail border-bottom py-2">';
                        echo '<div class="col-sm-3"><strong>Photo:</strong></div>';
                        echo '<div class="col-sm-9"><img src="../image/rider/' . $row["R_Photo"] . '" style="width: 100px; height: 100px; object-fit: cover;" alt="Staff Photo"></div>';
                        echo '</div>';

                        echo '<div class="row detail border-bottom py-2">';
                        echo '<div class="col-sm-3"><strong>Contact Number:</strong></div>';
                        echo '<div class="col-sm-9">' . $row["R_Contact_Number"] . '</div>';
                        echo '</div>';

                        echo '<div class="row detail border-bottom py-2">';
                        echo '<div class="col-sm-3"><strong>Email:</strong></div>';
                        echo '<div class="col-sm-9">' . $row["R_Email"] . '</div>';
                        echo '</div>';

                        echo '<div class="row detail border-bottom py-2">';
                        echo '<div class="col-sm-3"><strong>License:</strong></div>';
                        echo '<div class="col-sm-9"><img src="../image/rider/' . $row["R_License"] . '" style="width: 200px; height: 100px; object-fit: contain;" alt="License Photo"></div>';
                        echo '</div>';

                        $expiryTimestamp = strtotime($row["L_Exp_Date"]);
                        $formattedExpiryDate = date('d-m-Y', $expiryTimestamp);
                        $currentTimestamp = time();
                        $timeDifference = $expiryTimestamp - $currentTimestamp;
                        $monthsDifference = round($timeDifference / (30 * 24 * 60 * 60));

                        if ($expiryTimestamp < $currentTimestamp) {
                          echo '<div class="row detail border-bottom py-2">';
                          echo '<div class="col-sm-3"><strong>Licence Expiry Date:</strong></div>';
                          echo '<div class="col-sm-9">' . $formattedExpiryDate . '</div>';
                          echo '</div>';
                          echo '<p style="color: red; font-size: smaller;"><em>The rider\'s license has expired. Please take necessary actions.</em></p>';
                        } elseif ($monthsDifference <= 3) {
                          echo '<div class="row detail border-bottom py-2">';
                          echo '<div class="col-sm-3"><strong>Licence Expiry Date:</strong></div>';
                          echo '<div class="col-sm-9">' . $formattedExpiryDate . '</div>';
                          echo '</div>';
                          echo '<p style="color: red; font-size: smaller;"><em>The rider\'s license is expiring soon within 3 months. Please remind the rider.</em></p>';
                        } else {
                          echo '<div class="row detail border-bottom py-2">';
                          echo '<div class="col-sm-3"><strong>Licence Expiry Date:</strong></div>';
                          echo '<div class="col-sm-9">' . $formattedExpiryDate . '</div>';
                          echo '</div>';
                        }

                        echo '<div class="row detail border-bottom py-2">';
                        echo '<div class="col-sm-3"><strong>Plate Number:</strong></div>';
                        echo '<div class="col-sm-9">' . $row["R_PlateNo"] . '</div>';
                        echo '</div>';

                        echo '<div class="row detail border-bottom py-2">';
                        echo '<div class="col-sm-3"><strong>Status:</strong></div>';
                        echo '<div class="col-sm-9">' . $row["R_Status"] . '</div>';
                        echo '</div>';

                        echo '<div class="row detail border-bottom py-2">';
                        echo '<div class="col-sm-3"><strong>Bank Name:</strong></div>';
                        echo '<div class="col-sm-9">' . $row["Bank_Type"] . '</div>';
                        echo '</div>';

                        echo '<div class="row detail border-bottom py-2">';
                        echo '<div class="col-sm-3"><strong>Bank Account Number:</strong></div>';
                        echo '<div class="col-sm-9">' . $row["Bank_Number"] . '</div>';
                        echo '</div>';

                        echo '<div class="row detail border-bottom py-2">';
                        echo '<div class="col-sm-3"><strong>Money Earned (RM):</strong></div>';
                        echo '<div class="col-sm-9">' . number_format($row["Money_Earned"],2) . '</div>';
                        echo '</div>';

                        echo '<div class="row detail border-bottom py-2">';
                        echo '<div class="col-sm-3"><strong>Total Claim (RM):</strong></div>';
                        echo '<div class="col-sm-9">' . number_format($row["Total_Claim"],2) . '</div>';
                        echo '</div>';

                        echo '<div class="row detail border-bottom py-2">';
                        echo '<div class="col-sm-3"><strong>Added by:</strong></div>';
                        echo '<div class="col-sm-9">' . $row["R_SAID"] . ' (' . $row["S_Name"] . ')</div>';
                        echo '</div>';

                        echo '</div>';
                        echo '<div class="modal-footer">';
                        echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';

                        echo "<tr>";
                        echo "<td>" . $row["R_ID"] . "</td>";
                        echo "<td>" . $row["R_Name"] . "</td>";
                        echo "<td>" . $row["R_Email"] . "</td>";
                        echo "<td>" . $row["R_Contact_Number"] . "</td>";
                        echo "<td>";
                        echo "<button type='button' class='btn btn-" . ($row['R_Status'] == 'Active' ? 'success' : 'danger') . " btn-sm toggle-status' data-staff-id='" . $row['R_ID'] . "' data-current-status='" . $row['R_Status'] . "'>" . $row['R_Status'] . "</button>";
                        echo "</td>";
                        echo "<td>";
                        echo "<button type='button' class='btn btn-info btn-sm' data-bs-toggle='modal' data-bs-target='#staffDetailsModal_" . $row['R_ID'] . "'>View Details</button>";
                        echo "</td>";
                        echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No staff records found</td></tr>";
                    }
                    ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Add rider Modal -->
  <div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStaffModalLabel">Add New Rider</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addStaffForm" action="rider.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                    <div class="mb-3">
                        <label for="staffName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="staffName" name="staffName" required>
                    </div>
                    <div class="mb-3">
                        <label for="staffEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="staffEmail" name="staffEmail" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" title="Please enter a valid email address" required>
                    </div>
                    <div class="mb-3">
                        <label for="staffPhoto" class="form-label">Photo</label>
                        <input type="file" class="form-control" id="staffPhoto" name="staffPhoto" required>
                    </div>
                    <div class="mb-3">
                        <label for="staffContact" class="form-label">Contact Number</label>
                        <input type="text" class="form-control" id="staffContact" name="staffContact" required>
                    </div>
                    <div class="mb-3">
                        <label for="riderLicense" class="form-label">License Photo</label>
                        <input type="file" class="form-control" id="riderLicense" name="riderLicense" required>
                    </div>
                    <div class="mb-3">
                        <label for="riderLicenseExpDate" class="form-label">License Expiration Date</label>
                        <input type="date" class="form-control" id="riderLicenseExpDate" name="riderLicenseExpDate" required>
                    </div>
                    <div class="mb-3">
                        <label for="riderPlate" class="form-label">Plate Number</label>
                        <input type="text" class="form-control" id="riderPlate" name="riderPlate" required>
                    </div>
                    <div class="row mb-3">
                        <label for="riderBankType" class="col-md-4 col-lg-3 col-form-label">Bank Name</label>
                        <div class="col-md-8 col-lg-9">
                            <select name="riderBankType" id="riderBankType" class="form-select" required>
                                <option value="">Select Bank Type</option>
                                <option value="AEON Bank">AEON Bank</option>
                                <option value="Affin Bank Berhad">Affin Bank Berhad</option>
                                <option value="Al-Rajhi Banking & Investment Corporation (Malaysia) Berhad">Al-Rajhi Banking & Investment Corporation (Malaysia) Berhad</option>
                                <option value="Alliance Bank Malaysia Berhad">Alliance Bank Malaysia Berhad</option>
                                <option value="Ambank">Ambank</option>
                                <option value="BNP Paribas Malaysia Berhad">BNP Paribas Malaysia Berhad</option>
                                <option value="Bangkok Bank Berhad">Bangkok Bank Berhad</option>
                                <option value="Bank Islam Malaysia Berhad">Bank Islam Malaysia Berhad</option>
                                <option value="Bank Kerjasame Rakyat Malaysia Berhad">Bank Kerjasame Rakyat Malaysia Berhad</option>
                                <option value="Bank Muamalat Malaysia Berhad">Bank Muamalat Malaysia Berhad</option>
                                <option value="Bank Pertanian Malaysia Berhad (Argobank)">Bank Pertanian Malaysia Berhad (Argobank)</option>
                                <option value="Bank Simpanna Nasional Berhad">Bank Simpanna Nasional Berhad</option>
                                <option value="Bank of America">Bank of America</option>
                                <option value="Bank of China (Malaysia) Berhad">Bank of China (Malaysia) Berhad</option>
                                <option value="BigPay">BigPay</option>
                                <option value="Boost Bank">Boost Bank</option>
                                <option value="China Construction Bank (Malaysia) Berhad">China Construction Bank (Malaysia) Berhad</option>
                                <option value="CIMB Bank">CIMB Bank</option>
                                <option value="Citibank Berhad">Citibank Berhad</option>
                                <option value="Deutsche Bank (Malaysia) Berhad">Deutsche Bank (Malaysia) Berhad</option>
                                <option value="Finexus Cards Sdn Bhd">Finexus Cards Sdn Bhd</option>
                                <option value="GXBank">GXBank</option>
                                <option value="HSBC Bank Malaysia Berhad">HSBC Bank Malaysia Berhad</option>
                                <option value="Hong Leong Bank">Hong Leong Bank</option>
                                <option value="Industrial and Commercial Bank of China (M) Berhad">Industrial and Commercial Bank of China (M) Berhad</option>
                                <option value="J.P. Morgan Chase Bank Bhd">J.P. Morgan Chase Bank Bhd</option>
                                <option value="J.P. Morgan Chase Bank Bhd">J.P. Morgan Chase Bank Bhd</option>
                                <option value="Kuwait Finance House (Malaysia) Berhad">Kuwait Finance House (Malaysia) Berhad</option>
                                <option value="MBSB Bank Berhad">MBSB Bank Berhad</option>
                                <option value="MUFG Bank (Malaysia) Berhad">MUFG Bank (Malaysia) Berhad</option>
                                <option value="Maybank">Maybank</option>
                                <option value="Merchantdrade">Merchantdrade</option>
                                <option value="Mizuho Corporate Bank Malaysia Berhad">Mizuho Corporate Bank Malaysia Berhad</option>
                                <option value="OCBC Bank (Malaysia) Berhad">OCBC Bank (Malaysia) Berhad</option>
                                <option value="Public Bank Berhad/ Public Islamic Bank">Public Bank Berhad/ Public Islamic Bank</option>
                                <option value="RHB Bank">RHB Bank</option>
                                <option value="ShopeePay">ShopeePay</option>
                                <option value="Standard Chartered Bank Malaysia">Standard Chartered Bank Malaysia</option>
                                <option value="Sumitomo Mitsui Banking Corporation Malaysia Berhad">Sumitomo Mitsui Banking Corporation Malaysia Berhad</option>
                                <option value="Touch n Go eWallet">Touch n Go eWallet</option>
                                <option value="United Overseas Bank Berhad">United Overseas Bank Berhad</option>
                            </select>

                          </div>
                          <div class="mb-3">
                                          <label for="riderBankNumber" class="form-label">Bank Account Number</label>
                                          <input type="text" class="form-control" id="riderBankNumber" name="riderBankNumber" minlength="5" maxlength="19" title="Please enter a bank account number between 5 and 19 digits" required>
                                          <small class="text-muted d-block mt-2">*<em>By default, the rider status is set to active.</em></small>
                                      </div>
                                      <div class="mb-3" style="display: none;">
                                          <label for="defaultPassword" class="form-label">Default Password</label>
                                          <input type="password" class="form-control" id="staffPassword" name="staffPassword" value="Cuppa_joy123">
                                      </div>
                                      <input type="hidden" name="staffStatus" value="Active">
                                      <input type="hidden" name="riderMoneyEarned" value="0">
                                  </form>
                              </div>
                              <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                  <button type="button" class="btn btn-primary" id="saveStaffBtn">Save</button>
                              </div>
                          </div>
                      </div>
                  </div>
  <!-- End Add Staff Modal -->

  
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  
<script>

$(document).ready(function() {
    $('.toggle-status').click(function() {
        var riderID = $(this).data('staff-id');
        var currentStatus = $(this).data('current-status');

        if (confirm('Do you want to update the status for rider ID: ' + riderID + '?')) {
            $.ajax({
                type: 'POST',
                url: 'rider.php',
                data: {
                    toggleStatus: true,
                    riderID: riderID
                },
                success: function(newStatus) {
                    $('.toggle-status[data-staff-id="' + riderID + '"]').removeClass('btn-success btn-danger').addClass('btn-' + (newStatus == 'Active' ? 'success' : 'danger')).text(newStatus).data('current-status', newStatus);
                }
            });
        }
    });

    $('#saveStaffBtn').click(function(event) {
        event.preventDefault();

        if (validateForm()) {
            $('#addStaffForm').submit();
        }
    });
});

function validateForm() {
    var staffName = document.getElementById("staffName").value.trim();
    var staffEmail = document.getElementById("staffEmail").value.trim();
    var staffPhoto = document.getElementById("staffPhoto").value.trim();
    var staffContact = document.getElementById("staffContact").value.trim();
    var staffPassword = document.getElementById("staffPassword").value.trim();
    var riderLicense = document.getElementById("riderLicense").value.trim();
    var riderLicenseExpDate = document.getElementById("riderLicenseExpDate").value;
    var riderPlate = document.getElementById("riderPlate").value.trim();
    var riderBankType = document.getElementById("riderBankType").value;
    var riderBankNumber = document.getElementById("riderBankNumber").value.trim();

    if (staffName === "" || staffEmail === "" || staffPhoto === "" || staffContact === "" || riderLicense === "" || riderLicenseExpDate === "" || riderPlate === "" || riderBankType === "" || riderBankNumber === "") {
        alert("Please fill in all required fields.");
        return false;
    }

    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(staffEmail)) {
        alert("Please enter a valid email address.");
        return false;
    }

    var contactNumRegex = /^\d{10,11}$/;
    if (!contactNumRegex.test(staffContact)) {
        alert("Contact Number must be numeric and between 10 to 11 digits.");
        return false;
    }

    var plateNumRegex = /^[a-zA-Z0-9]{1,10}$/;
    if (!plateNumRegex.test(riderPlate)) {
        alert("Plate Number must be alphanumeric and up to 10 characters.");
        return false;
    }

    var bankNumRegex = /^\d{5,19}$/;
    if (!bankNumRegex.test(riderBankNumber)) {
        alert("Bank Account Number must be numeric and between 5 to 19 digits.");
        return false;
    }

    return true;
}

document.addEventListener('DOMContentLoaded', function() {
    var today = new Date().toISOString().split('T')[0];
    document.getElementById('riderLicenseExpDate').setAttribute('min', today);
});

document.getElementById('riderBankNumber').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/\D/g, '');
});

function confirmLogout() {
    return confirm("Are you sure you want to log out?");
}

</script>

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

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>