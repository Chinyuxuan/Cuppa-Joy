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

$sqlSuperAdminCheck = "SELECT Super_Staff FROM staff WHERE S_ID = '$currentuser'";
$resultSuperAdminCheck = $con->query($sqlSuperAdminCheck);
$currentuserSuperAdmin = false;

if ($resultSuperAdminCheck->num_rows > 0) {
    $rowSuperAdminCheck = $resultSuperAdminCheck->fetch_assoc();
    if ($rowSuperAdminCheck['Super_Staff'] == 'Yes') {
        $currentuserSuperAdmin = true;
    }
}

$sql = "
        SELECT s1.S_ID, s1.S_Name, s1.S_Email, s1.S_ContactNum, s1.S_Photo, s1.S_Status, s1.Super_Staff, s1.Add_By, s2.S_Name AS Added_By_Name 
        FROM staff s1 
        LEFT JOIN staff s2 ON s1.Add_By = s2.S_ID 
        WHERE s1.S_ID != '$currentuser'";

$result = $con->query($sql);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

//generate a unique staff ID
function generateAdminID($con) {
    do {
        $staffID = "CJS" . rand(1, 9999);
        $check_query = "SELECT * FROM staff WHERE S_ID = '$staffID'";
        $check_result = $con->query($check_query);
    } while ($check_result->num_rows > 0);

    return $staffID;
}

//send email
function sendEmail($staffEmail, $staffName, $staffID, $password) {
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
        $mail->addAddress($staffEmail);
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
              <p>Hello $staffName,</p>
              <p>Your Admin ID is: $staffID.</p>
              <p>Your randomly generated password is: <strong>$password</strong></p>
              <p>Please keep this information safe as you need to login to the system with this ID and password. You can log in to the website by clicking the <a href='http://localhost/fyp/admin/pages-login.php'>link</a>.</p>
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
  
  $staffID = generateAdminID($con);
  $staffName = $_POST["staffName"];
  $staffEmail = $_POST["staffEmail"];
  $staffPhotoPath = '';

  if (isset($_FILES["staffPhoto"]) && $_FILES["staffPhoto"]["error"] == 0) {
      $targetDir = "../image/admin/"; 
      $targetFile = $targetDir . basename($_FILES["staffPhoto"]["name"]);
      $staffphoto = $_FILES["staffPhoto"]["name"];
      if (move_uploaded_file($_FILES["staffPhoto"]["tmp_name"], $targetFile)) {
          $staffPhotoPath = $staffphoto;
      } else {
          echo json_encode(array("error" => "Error uploading photo."));
          exit();
      }
  }

  $staffStatus = $_POST["staffStatus"];
  $superStaff = $_POST["superStaff"];
  $staffContactNum = $_POST["staffContactNum"];
  $addby = $_SESSION["S_ID"];

  $plainPassword = generateRandomPassword();
  $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
  
  $checkEmailQuery = "SELECT * FROM staff WHERE S_Email = ?";
  $stmtCheckEmail = $con->prepare($checkEmailQuery);
  $stmtCheckEmail->bind_param("s", $staffEmail);
  $stmtCheckEmail->execute();
  $resultCheckEmail = $stmtCheckEmail->get_result();

  if ($resultCheckEmail && $resultCheckEmail->num_rows > 0) {
      echo "<script>alert('Email already exists in the database.');
      window.location.replace('staff.php');
      </script>";
  }

  $con->begin_transaction();
  
  $sql = "INSERT INTO staff (S_ID, S_Name, S_Email, S_Photo, S_Password, S_Status, Super_Staff, S_ContactNum, Add_By)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
  $stmt = $con->prepare($sql);
  if ($stmt) {
      $stmt->bind_param("sssssssss", $staffID, $staffName, $staffEmail, $staffPhotoPath, $hashedPassword, $staffStatus, $superStaff, $staffContactNum, $addby);
            if ($stmt->execute()) {
          if (sendEmail($staffEmail, $staffName, $staffID, $plainPassword)) {
              $con->commit();
              echo "<script>alert('New admin added successfully. Email sent to the admin.');
              window.location.replace('staff.php');
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
  $staffID = $_POST["staffID"];
  
  $statusQuery = "SELECT S_Status FROM staff WHERE S_ID = '$staffID'";
  $statusResult = $con->query($statusQuery);
  
  if ($statusResult->num_rows > 0) {
      $statusRow = $statusResult->fetch_assoc();
      $currentStatus = $statusRow["S_Status"];
      
      $newStatus = ($currentStatus == 'Active') ? 'Inactive' : 'Active';
      
      $updateStatusQuery = "UPDATE staff SET S_Status = '$newStatus' WHERE S_ID = '$staffID'";
      if ($con->query($updateStatusQuery) === TRUE) {
          echo $newStatus;
          exit();
      } else {
          echo "Error updating Status attribute: " . $con->error;
      }
  } else {
      echo "Staff ID not found";
  }
}

if (isset($_POST['toggleSuperStaff'])) {
  $staffID = $_POST["staffID"];
  
  $superStaffQuery = "SELECT Super_Staff FROM staff WHERE S_ID = '$staffID'";
  $superStaffResult = $con->query($superStaffQuery);
  
  if ($superStaffResult->num_rows > 0) {
      $superStaffRow = $superStaffResult->fetch_assoc();
      $currentSuperStaff = $superStaffRow["Super_Staff"];
      
      $newSuperStaff = ($currentSuperStaff == 'Yes') ? 'No' : 'Yes';
      
      $updateSuperStaffQuery = "UPDATE staff SET Super_Staff = '$newSuperStaff' WHERE S_ID = '$staffID'";
      if ($con->query($updateSuperStaffQuery) === TRUE) {
          echo $newSuperStaff;
          exit();
      } else {
          echo "Error updating Super Staff attribute: " . $con->error;
      }
  } else {
      echo "Staff ID not found";
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

  <title>Admin - Cuppa Joy</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <link href="assets/image/smile-black.png" rel="icon">
  <link href="assets/image/smile-black.png" rel="apple-touch-icon">

  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <link href="assets/css/style.css" rel="stylesheet">

</head>

<body>

  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="dashboard.php" class="logo d-flex align-items-center">
        <img src="assets/image/full-logo-black.png" alt="">
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>

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

          </ul>
        </li>

      </ul>
    </nav>

  </header>

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
        <a class="nav-link" href="staff.php">
          <i class="ri-user-star-fill"></i>
          <span>Admin</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="rider.php">
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
      <h1>Admin Management</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Management</li>
          <li class="breadcrumb-item active">Admin</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Admin List</h5>
                  <?php if ($currentuserSuperAdmin): ?>
                      <div class="d-flex justify-content-end mb-3">
                          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">Add New Admin</button>
                      </div>
                  <?php endif; ?>
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>Admin ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Contact Number</th>
                    <th>Status</th>
                    <th>Super Admin</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>

                <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {

                            echo '<div class="modal fade" id="staffDetailsModal_' . $row["S_ID"] . '" tabindex="-1" aria-labelledby="staffDetailsModalLabel_' . $row["S_ID"] . '" aria-hidden="true">';
                            echo '<div class="modal-dialog modal-dialog-centered modal-lg">';
                            echo '<div class="modal-content">';
                            echo '<div class="modal-header">';
                            echo '<h5 class="modal-title" id="staffDetailsModalLabel_' . $row["S_ID"] . '">Admin Details</h5>';
                            echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
                            echo '</div>';
                            echo '<div class="modal-body">';

                            echo '<div class="row detail border-bottom py-2">';
                            echo '<div class="col-sm-3"><strong>Admin ID:</strong></div>';
                            echo '<div class="col-sm-9">' . $row["S_ID"] . '</div>';
                            echo '</div>';

                            echo '<div class="row detail border-bottom py-2">';
                            echo '<div class="col-sm-3"><strong>Name:</strong></div>';
                            echo '<div class="col-sm-9">' . $row["S_Name"] . '</div>';
                            echo '</div>';

                            echo '<div class="row detail border-bottom py-2">';
                            echo '<div class="col-sm-3"><strong>Email:</strong></div>';
                            echo '<div class="col-sm-9">' . $row["S_Email"] . '</div>';
                            echo '</div>';

                            echo '<div class="row detail border-bottom py-2">';
                            echo '<div class="col-sm-3"><strong>Contact Number:</strong></div>';
                            echo '<div class="col-sm-9">' . $row["S_ContactNum"] . '</div>';
                            echo '</div>';

                            echo '<div class="row detail border-bottom py-2">';
                            echo '<div class="col-sm-3"><strong>Photo:</strong></div>';
                            echo '<div class="col-sm-9"><img src="../image/admin/' . $row["S_Photo"] . '" style="width: 100px; height: 100px; object-fit: cover;" alt="Staff Photo"></div>';
                            echo '</div>';

                            echo '<div class="row detail border-bottom py-2">';
                            echo '<div class="col-sm-3"><strong>Added by:</strong></div>';
                            echo '<div class="col-sm-9">' . $row["Add_By"] . ' (' . $row["Added_By_Name"] . ')</div>';
                            echo '</div>';

                            echo '<div class="row detail border-bottom py-2">';
                            echo '<div class="col-sm-3"><strong>Status:</strong></div>';
                            echo '<div class="col-sm-9">' . $row["S_Status"] . '</div>';
                            echo '</div>';

                            echo '<div class="row detail border-bottom py-2">';
                            echo '<div class="col-sm-3"><strong>Super Admin:</strong></div>';
                            echo '<div class="col-sm-9">' . $row["Super_Staff"] . '</div>';
                            echo '</div>';

                            echo '</div>'; 
                            echo '<div class="modal-footer">';
                            echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';

                            // Table row
                            echo "<tr>";
                            echo "<td>" . $row["S_ID"] . "</td>";
                            echo "<td>" . $row["S_Name"] . "</td>";
                            echo "<td>" . $row["S_Email"] . "</td>";
                            echo "<td>" . $row["S_ContactNum"] . "</td>";
                            echo "<td>";
                            if ($currentuserSuperAdmin) {
                              echo "<button type='button' class='btn btn-" . ($row['S_Status'] == 'Active' ? 'success' : 'danger') . " btn-sm toggle-status' data-staff-id='" . $row['S_ID'] . "' data-current-status='" . $row['S_Status'] . "'>" . $row['S_Status'] . "</button>";
                          } else {
                              $statusClass = '';
                              if ($row['S_Status'] == 'Inactive') {
                                  $statusClass = 'pending';
                              } else if ($row['S_Status'] == 'Active') {
                                  $statusClass = 'completed'; 
                              }
                              echo "<span class='delivery-status $statusClass'>" . $row['S_Status'] . "</span>";
                          }
                          echo "</td>";
                          echo "<td>";
                          if ($currentuserSuperAdmin) {
                              echo "<button type='button' class='btn btn-" . ($row['Super_Staff'] == 'Yes' ? 'success' : 'secondary') . " btn-sm toggle-super-staff' data-staff-id='" . $row['S_ID'] . "' data-current-super-staff='" . $row['Super_Staff'] . "'>" . $row['Super_Staff'] . "</button>";
                          } else {
                              $superStaffClass = '';
                              if ($row['Super_Staff'] == 'No') {
                                  $superStaffClass = 'pending';
                              } else if ($row['Super_Staff'] == 'Yes') {
                                  $superStaffClass = 'completed'; 
                              }
                              echo "<span class='delivery-status $superStaffClass'>" . $row['Super_Staff'] . "</span>";
                          }
                            echo "</td>";
                            echo "<td>";
                            echo "<button type='button' class='btn btn-info btn-sm' data-bs-toggle='modal' data-bs-target='#staffDetailsModal_" . $row['S_ID'] . "'>View Details</button>";
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

<!-- Add Staff Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStaffModalLabel">Add New Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addStaffForm" action="staff.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="staffName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="staffName" name="staffName" required>
                    </div>
                    <div class="mb-3">
                        <label for="staffPhoto" class="form-label">Photo</label>
                        <input type="file" class="form-control" id="staffPhoto" name="staffPhoto">
                    </div>
                    <div class="mb-3">
                        <label for="staffEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="staffEmail" name="staffEmail" required>
                    </div>
                    <div class="mb-4">
                        <label for="staffContactNum" class="form-label">Contact Number</label>
                        <input type="tel" class="form-control" id="staffContactNum" name="staffContactNum" required>
                        <small class="text-muted d-block mt-2">*<em>By default, the status is set to active and the role is admin.</em></small>
                    </div>
                    <!-- <div class="mb-3" style="display: none;">
                        <label for="staffPassword" class="form-label">Default Password</label>
                        <input type="password" class="form-control" id="staffPassword" name="staffPassword" value="<?php echo $staffID; ?>" required>
                    </div> -->

                    <input type="hidden" name="staffStatus" value="Active">
                    <input type="hidden" name="superStaff" value="No">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveStaffBtn">Save</button>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
    $(document).ready(function() {
        $('.toggle-status').click(function() {
            var staffID = $(this).data('staff-id');
            var currentStatus = $(this).data('current-status');

            if (confirm('Do you want to update the status for admin ID: ' + staffID + '?')) {
                $.ajax({
                    type: 'POST',
                    url: 'staff.php',
                    data: {
                        toggleStatus: true,
                        staffID: staffID
                    },
                    success: function(newStatus) {
                        $('.toggle-status[data-staff-id="' + staffID + '"]').removeClass('btn-success btn-danger').addClass('btn-' + (newStatus == 'Active' ? 'success' : 'danger')).text(newStatus).data('current-status', newStatus);
                    }
                });
            }
        });

        $('.toggle-super-staff').click(function() {
            var staffID = $(this).data('staff-id');
            var currentSuperStaff = $(this).data('current-super-staff');

            if (confirm('Do you want to update the super admin status for admin ID: ' + staffID + '?')) {
                $.ajax({
                    type: 'POST',
                    url: 'staff.php',
                    data: {
                        toggleSuperStaff: true,
                        staffID: staffID
                    },
                    success: function(newSuperStaff) {
                        $('.toggle-super-staff[data-staff-id="' + staffID + '"]').removeClass('btn-success btn-secondary').addClass('btn-' + (newSuperStaff == 'Yes' ? 'success' : 'secondary')).text(newSuperStaff).data('current-super-staff', newSuperStaff);
                    }
                });
            }
        });

        $('#saveStaffBtn').click(function() {
            if (validateForm()) {
                var staffEmail = $('#staffEmail').val();
                checkEmailExists(staffEmail);
            }
        });

        function checkEmailExists(staffEmail) {
            $.ajax({
                type: 'POST',
                url: 'staff.php',
                data: {
                    checkEmail: true,
                    staffEmail: staffEmail
                },
                success: function(response) {
                    if (response === 'exists') {
                        displayErrorMessage('Email already exists in the database. Please use a different email.');
                    } else {
                        $('#addStaffForm').submit();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error checking email existence:', error);
                    displayErrorMessage('Error checking email existence. Please try again later.');
                }
            });
        }

        function displayErrorMessage(message) {
            alert(message); 
        }

        function validateForm() {
            var staffName = $('#staffName').val();
            var staffEmail = $('#staffEmail').val();
            var staffContactNum = $('#staffContactNum').val();
            var staffPhoto = $('#staffPhoto').val();
            var staffPassword = $('#staffPassword').val();

            if (staffName.trim() === '') {
                displayErrorMessage("Name cannot be empty.");
                return false;
            }
            if (staffEmail.trim() === '') {
                displayErrorMessage("Email cannot be empty.");
                return false;
            }
            if (!isValidEmail(staffEmail)) {
                displayErrorMessage("Please enter a valid email address.");
                return false;
            }
            if (staffContactNum.trim() === '') {
                displayErrorMessage("Contact Number cannot be empty.");
                return false;
            }
            if (!isValidContactNum(staffContactNum)) {
                displayErrorMessage("Contact Number must be numeric and between 10 to 11 digits.");
                return false;
            }
            if (staffPhoto.trim() === '') {
                displayErrorMessage("Please upload a photo.");
                return false;
            }

            return true; 
        }

        function isValidEmail(email) {
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        function isValidContactNum(contactNum) {
            var contactNumRegex = /^\d{10,11}$/; 
            return contactNumRegex.test(contactNum);
        }
    });

    function confirmLogout() {
        return confirm("Are you sure you want to log out?");
    }

</script>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.min.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <script src="assets/js/main.js"></script>

</body>

</html>